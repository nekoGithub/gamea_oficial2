<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sistema;
use App\Events\EstadoSistemaWebActualizado;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonitorearSistemasWeb extends Command
{
    protected $signature   = 'sistemas:monitorear-web';
    protected $description = 'Monitorea la disponibilidad web de todos los sistemas con dominio';

    public function handle()
    {
        $this->info('🌐 Iniciando monitoreo web de sistemas...');
        $telegramService = app(TelegramService::class);

        while (true) {
            $sistemas = Sistema::where('estado', 'activo')
                ->whereNotNull('dominio')
                ->where('dominio', '!=', '')
                ->whereNotNull('ssl_id')
                ->get();

            $this->info('🔍 Verificando ' . $sistemas->count() . ' dominios...');

            foreach ($sistemas as $sistema) {
                $estadoAnterior = $sistema->disponibilidad_web;
                $resultado      = $this->verificarDominio($sistema->dominio);

                $sistema->update([
                    'disponibilidad_web'      => $resultado['estado'],
                    'http_status'             => $resultado['http_status'],
                    'tiempo_respuesta'        => $resultado['tiempo_ms'],
                    'ultima_verificacion_web' => now(),
                ]);

                broadcast(new EstadoSistemaWebActualizado($sistema->fresh()));

                // Alerta inmediata si cayó
                if ($estadoAnterior !== 'INACTIVO' && $resultado['estado'] === 'INACTIVO') {
                    $this->warn("🚨 {$sistema->nombre} CAÍDO — enviando alerta...");
                    $telegramService->sendSistemaWebInactivoAlert(
                        $sistema->nombre,
                        $sistema->dominio,
                        $resultado['http_status']
                    );
                    Log::warning("🚨 Sistema web caído: {$sistema->nombre}", $resultado);
                }

                $icono = $resultado['estado'] === 'ACTIVO' ? '✅' : '❌';
                $ms    = $resultado['tiempo_ms'] ? $resultado['tiempo_ms'] . 'ms' : 'timeout';
                $http  = $resultado['http_status'] ?? 'N/A';

                $this->info("{$icono} {$sistema->nombre} | {$sistema->dominio} | HTTP:{$http} | {$ms}");
            }

            $this->info('⏳ Esperando 60 segundos...');
            sleep(60);
        }
    }

    private function verificarDominio(string $dominio): array
    {
        $dominio = trim(preg_replace('/^https?:\/\//i', '', $dominio));
        $inicio  = microtime(true);

        // ── Intento 1: HTTP/HTTPS ──────────────────
        try {
            $response = \Illuminate\Support\Facades\Http::withOptions([
                'timeout'         => 8,
                'connect_timeout' => 5,
                'verify'          => false,
                'allow_redirects' => true,
            ])->get('https://' . $dominio);

            $ms     = round((microtime(true) - $inicio) * 1000);
            $status = $response->status();

            if ($status < 500) {
                return [
                    'estado'      => 'ACTIVO',
                    'http_status' => $status,
                    'tiempo_ms'   => $ms,
                    'metodo'      => 'HTTP',
                ];
            }
        } catch (\Exception $e) {
            // HTTP falló, intentar ping
        }

        // ── Intento 2: HTTP plano ──────────────────
        try {
            $response2 = \Illuminate\Support\Facades\Http::withOptions([
                'timeout'         => 8,
                'connect_timeout' => 5,
                'verify'          => false,
                'allow_redirects' => true,
            ])->get('http://' . $dominio);

            $ms     = round((microtime(true) - $inicio) * 1000);
            $status = $response2->status();

            if ($status < 500) {
                return [
                    'estado'      => 'ACTIVO',
                    'http_status' => $status,
                    'tiempo_ms'   => $ms,
                    'metodo'      => 'HTTP',
                ];
            }
        } catch (\Exception $e) {
            // HTTP plano también falló, intentar ping
        }


        // ── Intento 3: TCP Socket puerto 443 ──────
        $socket = @fsockopen('ssl://' . $dominio, 443, $errno, $errstr, 5);
        if ($socket) {
            fclose($socket);
            $ms = round((microtime(true) - $inicio) * 1000);
            return [
                'estado'      => 'ACTIVO',
                'http_status' => 200,
                'tiempo_ms'   => $ms,
                'metodo'      => 'TCP',
            ];
        }

        // ── Intento 4: TCP Socket puerto 80 ───────
        $socket2 = @fsockopen($dominio, 80, $errno, $errstr, 5);
        if ($socket2) {
            fclose($socket2);
            $ms = round((microtime(true) - $inicio) * 1000);
            return [
                'estado'      => 'ACTIVO',
                'http_status' => 200,
                'tiempo_ms'   => $ms,
                'metodo'      => 'TCP',
            ];
        }

        // ── Intento 5: ICMP Ping ───────────────────            
        /* $cmd    = "ping -c 1 -W 2 " . escapeshellarg($dominio) . " 2>&1"; */
        $cmd    = "ping -n 1 -w 2000 " . escapeshellarg($dominio) . " 2>&1";
        exec($cmd, $output, $code);

        $ms = null;
        foreach ($output as $linea) {
            /* if (preg_match('/time[=<]([\d.]+)\s*ms/i', $linea, $matches)) {
                $ms = round((float) $matches[1]);
                break;
            } */
            if (
                preg_match('/tiempo[=<]([\d.]+)\s*ms/i', $linea, $matches) ||
                preg_match('/time[=<]([\d.]+)\s*ms/i', $linea, $matches)
            ) {
                $ms = round((float) $matches[1]);
                break;
            }
        }

        $tiempoTotal = round((microtime(true) - $inicio) * 1000);

        return [
            'estado'      => $code === 0 ? 'ACTIVO' : 'INACTIVO',
            'http_status' => null,
            'tiempo_ms'   => $ms ?? $tiempoTotal,
            'metodo'      => 'PING',
        ];
    }
}
