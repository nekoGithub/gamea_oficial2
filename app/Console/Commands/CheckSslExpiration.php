<?php

namespace App\Console\Commands;

use App\Models\Ssl;
use App\Models\Sistema;
use App\Models\Notificacion;
use App\Services\TelegramService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckSslExpiration extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ssl:check-expiration 
                            {--test : Modo de prueba - muestra resultados sin enviar}
                            {--force : Forzar envío incluso si ya se envió hoy}';

    /**
     * The console command description.
     */
    protected $description = 'Verifica certificados SSL próximos a vencer y envía alertas a Telegram';

    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando certificados SSL...');

        $testMode = $this->option('test');
        $forceMode = $this->option('force');
        $diasAlerta = config('services.ssl_alerts.days_warning', 30);

        // Obtener todos los SSLs activos
        $ssls = Ssl::whereHas('sistemas', function ($query) {
            $query->where('estado', 'activo');
        })->get();

        if ($ssls->isEmpty()) {
            $this->warn('⚠️  No hay certificados SSL registrados.');
            return Command::SUCCESS;
        }

        $this->info("📋 Total de certificados: {$ssls->count()}");

        $alertasEnviadas = 0;
        $sslsRevisados = 0;

        foreach ($ssls as $ssl) {
            $sslsRevisados++;

            $fechaExpiracion = Carbon::parse($ssl->fecha_expiracion);
            $diasRestantes = now()->diffInDays($fechaExpiracion, false);

            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->info("SSL #{$ssl->id}: {$ssl->emisor}");
            $this->line("Expira: {$fechaExpiracion->format('d/m/Y')}");

            // Determinar estado
            $estadoAnterior = $ssl->estado;

            if ($diasRestantes < 0) {
                // SSL VENCIDO
                $ssl->update(['estado' => 'vencido']);
                $this->error("❌ VENCIDO hace " . abs($diasRestantes) . " días");

                if (!$testMode && $this->debeEnviarAlerta($ssl, $forceMode)) {
                    $this->enviarAlertaVencido($ssl, abs($diasRestantes));
                    $alertasEnviadas++;
                }
            } elseif ($diasRestantes <= $diasAlerta) {
                // SSL PRÓXIMO A VENCER
                $ssl->update(['estado' => 'proximo_vencer']);
                $this->warn("⚠️  Vence en {$diasRestantes} días");

                if (!$testMode && $this->debeEnviarAlerta($ssl, $forceMode)) {
                    $this->enviarAlertaProximoVencer($ssl, $diasRestantes);
                    $alertasEnviadas++;
                }
            } else {
                // SSL VÁLIDO
                $ssl->update(['estado' => 'valido']);
                $this->info("✅ Válido ({$diasRestantes} días restantes)");
            }
        }

        $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
        $this->info("\nRESUMEN:");
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['SSLs revisados', $sslsRevisados],
                ['Alertas enviadas', $testMode ? '0 (modo test)' : $alertasEnviadas],
                ['Modo', $testMode ? 'PRUEBA' : 'PRODUCCIÓN'],
            ]
        );

        if ($testMode) {
            $this->warn('⚠️  MODO TEST: No se enviaron alertas reales.');
        }

        return Command::SUCCESS;
    }

    /**
     * Verificar si se debe enviar alerta (evitar duplicados diarios)
     */
    protected function debeEnviarAlerta(Ssl $ssl, bool $force): bool
    {
        if ($force) {
            return true;
        }

        // Buscar sistemas asociados al SSL
        $sistemas = Sistema::where('ssl_id', $ssl->id)->where('estado', 'activo')->get();

        foreach ($sistemas as $sistema) {
            // Obtener la última versión del sistema
            $ultimaVersion = $sistema->versiones()->latest()->first();

            if (!$ultimaVersion) {
                continue;
            }

            // Verificar si ya se envió alerta hoy
            $alertaHoy = Notificacion::where('sistema_version_id', $ultimaVersion->id)
                ->whereDate('fecha', today())
                ->where('estado', 'enviado')
                ->exists();

            if ($alertaHoy) {
                $this->line("  ℹ️  Ya se envió alerta hoy para este sistema");
                return false;
            }
        }

        return true;
    }

    /**
     * Enviar alerta de SSL próximo a vencer
     */
    protected function enviarAlertaProximoVencer(Ssl $ssl, int $diasRestantes): void
    {
        $sistemas = Sistema::where('ssl_id', $ssl->id)->where('estado', 'activo')->get();

        if ($sistemas->isEmpty()) {
            $this->warn("  ⚠️  No hay sistemas activos asociados a este SSL");
            return;
        }

        $this->line("  📌 Sistemas asociados: {$sistemas->count()}");

        foreach ($sistemas as $sistema) {
            $this->line("    → Sistema: {$sistema->nombre}");

            $ultimaVersion = $sistema->versiones()->latest()->first();

            if (!$ultimaVersion) {
                $this->warn("      ⚠️  No tiene versiones registradas");
                continue;
            }

            $this->line("      ✓ Versión encontrada: {$ultimaVersion->numero_version}");

            try {
                // Crear notificación en BD
                $notificacion = Notificacion::create([
                    'sistema_version_id' => $ultimaVersion->id,
                    'fecha' => now(),
                    'estado' => 'pendiente',
                    'mensaje' => "SSL '{$ssl->emisor}' vence en {$diasRestantes} días",
                    'usuario_enviado' => 1, // Sistema
                ]);

                // Enviar a Telegram
                $enviado = $this->telegramService->sendSslExpirationAlert(
                    $ssl->emisor,
                    $diasRestantes,
                    $sistema->nombre
                );

                if ($enviado) {
                    $notificacion->marcarEnviado();
                    $this->info("  ✅ Alerta enviada a Telegram y guardada en BD");
                    Log::info("Alerta SSL enviada", [
                        'notificacion_id' => $notificacion->id,
                        'ssl_id' => $ssl->id,
                        'sistema' => $sistema->nombre,
                        'dias_restantes' => $diasRestantes
                    ]);
                } else {
                    $notificacion->marcarFallido();
                    $this->error("  ❌ Error enviando alerta a Telegram");
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Excepción: " . $e->getMessage());
                Log::error("Error enviando alerta SSL", [
                    'ssl_id' => $ssl->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Enviar alerta de SSL vencido
     */
    protected function enviarAlertaVencido(Ssl $ssl, int $diasVencido): void
    {
        $sistemas = Sistema::where('ssl_id', $ssl->id)->where('estado', 'activo')->get();

        foreach ($sistemas as $sistema) {
            $ultimaVersion = $sistema->versiones()->latest()->first();

            if (!$ultimaVersion) {
                continue;
            }

            try {
                $notificacion = Notificacion::create([
                    'sistema_version_id' => $ultimaVersion->id,
                    'fecha' => now(),
                    'estado' => 'pendiente',
                    'mensaje' => "SSL '{$ssl->emisor}' VENCIDO hace {$diasVencido} días",
                    'usuario_enviado' => 1,
                ]);

                $enviado = $this->telegramService->sendSslExpiredAlert(
                    $ssl->emisor,
                    $diasVencido,
                    $sistema->nombre
                );

                if ($enviado) {
                    $notificacion->marcarEnviado();
                    $this->error("  🚨 Alerta CRÍTICA enviada");
                } else {
                    $notificacion->marcarFallido();
                    $this->error("  ❌ Error enviando alerta CRÍTICA");
                }
            } catch (\Exception $e) {
                $this->error("  ❌ Excepción: " . $e->getMessage());
            }
        }
    }
}
