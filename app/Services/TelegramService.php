<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected $token;
    protected $chatId;

    public function __construct()
    {
        $this->token = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    public function sendMessage(string $message): bool
    {
        // Verificar rate limit
        $lastSent = Cache::get('telegram_last_sent', 0);
        $timeSinceLastSent = microtime(true) - $lastSent;

        if ($timeSinceLastSent < 2) {
            $waitTime = 2 - $timeSinceLastSent;
            Log::info("⏳ Esperando {$waitTime}s para respetar rate limit...");
            usleep($waitTime * 1000000);
        }

        $maxIntentos = 3;

        for ($intento = 1; $intento <= $maxIntentos; $intento++) {
            try {
                Log::info("🔄 Intento {$intento}/{$maxIntentos} de envío a Telegram");

                $response = Http::withOptions([
                    'timeout' => 10,
                    'connect_timeout' => 5,
                    'verify' => false,
                    'http_errors' => false,
                ])->post("https://api.telegram.org/bot{$this->token}/sendMessage", [
                    'chat_id' => $this->chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                ]);

                if ($response->successful()) {
                    Cache::put('telegram_last_sent', microtime(true), 10);
                    Log::info("✅ Mensaje enviado en intento {$intento}");
                    return true;
                }

                // Manejar error 429 (Too Many Requests)
                if ($response->status() === 429) {
                    $retryAfter = $response->json('parameters.retry_after', 30);
                    Log::warning("⚠️ Rate limit de Telegram. Esperando {$retryAfter}s...");
                    sleep($retryAfter);
                    continue;
                }

                Log::warning("⚠️ Intento {$intento} falló - Status: {$response->status()}");
            } catch (\Exception $e) {
                Log::warning("⚠️ Intento {$intento} falló - Error: {$e->getMessage()}");
            }

            // Backoff exponencial: 3, 6, 9, 12, 15 segundos
            if ($intento < $maxIntentos) {
                $espera = min($intento * 2, 8);
                Log::info("⏳ Esperando {$espera}s antes del siguiente intento...");
                sleep($espera);
            }
        }

        Log::error("❌ Todos los {$maxIntentos} intentos fallaron");
        return false;
    }

    /**
     * Enviar alerta de SSL próximo a vencer (FORMATO MEJORADO)
     */
    public function sendSslExpirationAlert(string $emisor, int $diasRestantes, string $sistemaNombre): bool
    {
        $emoji = $diasRestantes <= 7 ? '🚨' : ($diasRestantes <= 15 ? '⚠️' : '⏰');
        $urgencia = $diasRestantes <= 7 ? 'URGENTE' : ($diasRestantes <= 15 ? 'IMPORTANTE' : 'AVISO');

        // ========== ENCABEZADO ==========
        $message = "{$emoji} <b>Certificado SSL por vencer</b>\n\n";
        $message .= "━━━━━━━━━━━━━━\n\n";

        // ========== INFORMACIÓN PRINCIPAL ==========
        $message .= "Nivel de urgencia: <b>{$urgencia}</b>\n";
        $message .= "Emisor: {$emisor}\n";
        $message .= "Sistema: {$sistemaNombre}\n";
        $message .= "Días restantes: <b>{$diasRestantes} días</b>\n";
        $message .= "Fecha: " . now()->format('d/m/Y H:i') . "\n\n";

        $message .= "━━━━━━━━━━━━━━\n\n";

        // ========== ACCIÓN REQUERIDA ==========
        $message .= "Acción requerida: <b>Actualizar certificado SSL</b>";

        return $this->sendMessage($message);
    }

    /**
     * Enviar alerta de SSL vencido (FORMATO MEJORADO)
     */
    public function sendSslExpiredAlert(string $emisor, int $diasVencido, string $sistemaNombre): bool
    {
        // ========== ENCABEZADO ==========
        $message = "🔴 <b>CRÍTICO: Certificado SSL VENCIDO</b>\n\n";
        $message .= "━━━━━━━━━━━━━━\n\n";

        // ========== INFORMACIÓN PRINCIPAL ==========
        $message .= "Emisor: {$emisor}\n";
        $message .= "Sistema: {$sistemaNombre}\n";
        $message .= "Vencido hace: <b>{$diasVencido} días</b>\n";
        $message .= "Fecha: " . now()->format('d/m/Y H:i') . "\n\n";

        $message .= "━━━━━━━━━━━━━━\n\n";

        // ========== ACCIÓN REQUERIDA ==========
        $message .= "⚠️ Acción inmediata: <b>Renovar certificado SSL</b>";

        return $this->sendMessage($message);
    }

    /**
     * Enviar mensaje de prueba
     */
    public function sendTestMessage(): bool
    {
        $message = "
✅ <b>Prueba de Conexión</b>

El bot de alertas del Sistema ELALTO está funcionando correctamente.

📅 " . now()->format('d/m/Y H:i:s');

        return $this->sendMessage($message);
    }

    /**
     * Enviar notificación de nueva versión creada (FORMATO MEJORADO)
     */
    public function sendNewVersionNotification(array $data): bool
    {
        // Emoji según estado
        $estadoEmoji = match ($data['estado']) {
            'estable' => '🟢',
            'beta' => '🟡',
            'deprecated' => '🔴',
            default => '⚪'
        };

        // ========== ENCABEZADO ==========
        $message = "🚀 <b>Nueva versión publicada</b>\n\n";
        $message .= "━━━━━━━━━━━━━━\n\n";

        // ========== INFORMACIÓN PRINCIPAL ==========
        $message .= "Sistema: {$data['sistema']}\n";
        $message .= "Versión: <b>{$data['numero_version']}</b>\n";
        $message .= "Publicado por: {$data['usuario']}\n";
        $message .= "Fecha: {$data['fecha']}\n\n";

        $message .= "━━━━━━━━━━━━━━\n\n";

        // ========== TECNOLOGÍAS ==========
        if (!empty($data['tecnologias'])) {
            $message .= "<b>Tecnologías</b>\n";
            foreach ($data['tecnologias'] as $tec) {
                $tipo = isset($tec['tipo']) ? " ({$tec['tipo']})" : "";
                $message .= "• {$tec['nombre']}{$tipo}\n";
            }
            $message .= "\n";
        }

        // ========== SERVIDORES ==========
        if (!empty($data['servidores'])) {
            $message .= "<b>Servidores</b>\n";
            foreach ($data['servidores'] as $srv) {
                $message .= "• {$srv['nombre']}\n";
            }
            $message .= "\n";
        }

        // ========== BASES DE DATOS ==========
        if (!empty($data['bds'])) {
            $message .= "<b>Bases de Datos</b>\n";
            foreach ($data['bds'] as $bd) {
                $message .= "• {$bd['nombre']}\n";
            }
            $message .= "\n";
        }

        // ========== CREDENCIALES ==========
        if (isset($data['total_credenciales']) && $data['total_credenciales'] > 0) {
            $message .= "<b>Credenciales:</b> {$data['total_credenciales']}\n\n";
        }

        // ========== ARCHIVOS INCLUIDOS ==========
        $archivos = [];
        if (!empty($data['archivos']['codigo_fuente'])) $archivos[] = "✅ Código Fuente";
        if (!empty($data['archivos']['manual_tecnico'])) $archivos[] = "✅ Manual Técnico";
        if (!empty($data['archivos']['manual_usuario'])) $archivos[] = "✅ Manual de Usuario";
        if (!empty($data['archivos']['imagen'])) $archivos[] = "✅ Imagen";

        if (!empty($archivos)) {
            $message .= "<b>Archivos incluidos:</b>\n";
            foreach ($archivos as $archivo) {
                $message .= "{$archivo}\n";
            }
            $message .= "\n";
        }

        // ========== DOCUMENTOS ADICIONALES ==========
        if (isset($data['documentos_adicionales']) && $data['documentos_adicionales'] > 0) {
            $message .= "<b>Documentos Adicionales:</b> {$data['documentos_adicionales']}\n\n";
        }

        // ========== ESTADO ==========
        $message .= "━━━━━━━━━━━━━━\n\n";
        $message .= "Estado del despliegue: {$estadoEmoji} <b>" . ucfirst($data['estado']) . "</b>";

        return $this->sendMessage($message);
    }

    /**
     * Alerta inmediata — servidor caído
     */
    public function sendServidorInactivoAlert(string $nombre, string $ipInterna, string $ipExterna = null): bool
    {
        $message = "🔴 <b>ALERTA: Servidor INACTIVO</b>\n\n";
        $message .= "━━━━━━━━━━━━━━\n\n";
        $message .= "Servidor: <b>{$nombre}</b>\n";
        $message .= "IP Interna: <code>{$ipInterna}</code>\n";
        if ($ipExterna) {
            $message .= "IP Externa: <code>{$ipExterna}</code>\n";
        }
        $message .= "Fecha: " . now()->format('d/m/Y H:i:s') . "\n\n";
        $message .= "━━━━━━━━━━━━━━\n\n";
        $message .= "⚠️ Verificar conectividad del servidor inmediatamente.";

        return $this->sendMessage($message);
    }

    /**
     * Resumen diario — servidores con problemas
     */
    public function sendResumenServidores(array $inactivos, array $desconocidos, string $hora): bool
    {
        $totalProblemas = count($inactivos) + count($desconocidos);

        if ($totalProblemas === 0) return false; // No mandar si todo está bien

        $message = "📊 <b>Resumen de Servidores — {$hora}</b>\n\n";
        $message .= "━━━━━━━━━━━━━━\n\n";
        $message .= "Fecha: " . now()->format('d/m/Y H:i') . "\n\n";

        if (!empty($inactivos)) {
            $message .= "🔴 <b>Servidores INACTIVOS (" . count($inactivos) . ")</b>\n";
            foreach ($inactivos as $srv) {
                $message .= "• <b>{$srv['nombre']}</b> — INT: <code>{$srv['ip_interna']}</code>";
                if ($srv['ip_externa']) {
                    $message .= " | EXT: <code>{$srv['ip_externa']}</code>";
                }
                $message .= "\n";
            }
            $message .= "\n";
        }

        if (!empty($desconocidos)) {
            $message .= "⚫ <b>Sin IP Externa ({" . count($desconocidos) . "})</b>\n";
            foreach ($desconocidos as $srv) {
                $message .= "• <b>{$srv['nombre']}</b> — INT: <code>{$srv['ip_interna']}</code>\n";
            }
            $message .= "\n";
        }

        $message .= "━━━━━━━━━━━━━━\n\n";
        $message .= "✅ Servidores activos: <b>{$this->contarActivos()}</b>\n";
        $message .= "🔴 Servidores con problemas: <b>{$totalProblemas}</b>";

        return $this->sendMessage($message);
    }

    private function contarActivos(): int
    {
        return \App\Models\Servidor::where('estado', 'activo')
            ->where('disponibilidad_interna', 'ACTIVO')
            ->count();
    }

    public function sendSistemaWebInactivoAlert(string $nombre, string $dominio, ?int $httpStatus): bool
    {
        $status = $httpStatus ? "HTTP {$httpStatus}" : 'Sin respuesta';

        $message = "🌐 <b>ALERTA: Sistema Web INACTIVO</b>\n\n";
        $message .= "━━━━━━━━━━━━━━\n\n";
        $message .= "Sistema: <b>{$nombre}</b>\n";
        $message .= "Dominio: <code>{$dominio}</code>\n";
        $message .= "Estado HTTP: <b>{$status}</b>\n";
        $message .= "Fecha: " . now()->format('d/m/Y H:i:s') . "\n\n";
        $message .= "━━━━━━━━━━━━━━\n\n";
        $message .= "⚠️ Verificar disponibilidad del sistema web.";

        return $this->sendMessage($message);
    }
}
