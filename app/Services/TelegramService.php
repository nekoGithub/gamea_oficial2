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

        $maxIntentos = 5;

        for ($intento = 1; $intento <= $maxIntentos; $intento++) {
            try {
                Log::info("🔄 Intento {$intento}/{$maxIntentos} de envío a Telegram");

                $response = Http::withOptions([
                    'timeout' => 30,
                    'connect_timeout' => 30,
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
                $espera = $intento * 3;
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
}
