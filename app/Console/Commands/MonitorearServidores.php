<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Servidor;
use App\Events\EstadoServidorActualizado;
use Illuminate\Support\Facades\Log;

class MonitorearServidores extends Command
{
    protected $signature   = 'servidores:monitorear';
    protected $description = 'Monitorea el estado de todos los servidores mediante ping';

    public function handle()
    {
        $this->info('🚀 Iniciando monitoreo de servidores...');
        $telegramService = app(\App\Services\TelegramService::class);

        while (true) {
            $servidores = Servidor::where('estado', 'activo')->get();
            $this->info('📡 Verificando ' . $servidores->count() . ' servidores...');

            foreach ($servidores as $servidor) {
                $estadoInternoAnterior = $servidor->disponibilidad_interna;
                $estadoExternoAnterior = $servidor->disponibilidad_externa;

                $interna = $this->ping($servidor->ip_interna);
                $externa = $servidor->ip_externa
                    ? $this->ping($servidor->ip_externa)
                    : ['estado' => 'DESCONOCIDO', 'ms' => null, 'output' => ''];

                $servidor->update([
                    'disponibilidad_interna' => $interna['estado'],
                    'disponibilidad_externa' => $externa['estado'],
                    'ultima_verificacion'    => now(),
                ]);

                broadcast(new EstadoServidorActualizado($servidor->fresh(), $interna, $externa));

                // ✅ ALERTA INMEDIATA: solo si pasó de ACTIVO/DESCONOCIDO a INACTIVO
                $cayoInterno = $estadoInternoAnterior !== 'INACTIVO' && $interna['estado'] === 'INACTIVO';

                if ($cayoInterno) {
                    $this->warn("🚨 ALERTA: {$servidor->nombre} está INACTIVO — enviando Telegram...");
                    $telegramService->sendServidorInactivoAlert(
                        $servidor->nombre,
                        $servidor->ip_interna,
                        $servidor->ip_externa
                    );
                    Log::warning("🚨 Servidor caído: {$servidor->nombre}", [
                        'ip_interna' => $servidor->ip_interna,
                        'ip_externa' => $servidor->ip_externa,
                    ]);
                }

                $iconoI = $interna['estado'] === 'ACTIVO' ? '✅' : '❌';
                $iconoE = $externa['estado'] === 'ACTIVO' ? '✅' : ($externa['estado'] === 'DESCONOCIDO' ? '❓' : '❌');
                $msI    = $interna['ms'] ? $interna['ms'] . 'ms' : 'timeout';
                $msE    = $externa['ms'] ? $externa['ms'] . 'ms' : ($externa['estado'] === 'DESCONOCIDO' ? 'N/A' : 'timeout');

                $this->info("{$iconoI} {$iconoE} {$servidor->nombre} | INT:{$interna['estado']} ({$msI}) EXT:{$externa['estado']} ({$msE})");
            }

            $this->info('⏳ Esperando 30 segundos...');
            sleep(30);
        }
    }

    private function ping($ip)
    {
        if (empty($ip)) return ['estado' => 'DESCONOCIDO', 'ms' => null, 'output' => ''];

       /*  $cmd = "ping -c 1 -W 2 " . escapeshellarg($ip) . " 2>&1"; */
        $cmd = "ping -n 1 -w 1000 " . escapeshellarg($ip) . " 2>&1";
        exec($cmd, $output, $resultado);

        $ms = null;
        foreach ($output as $linea) {
            if (preg_match('/time=([\d.]+)\s*ms/i', $linea, $matches)) {
                $ms = round((float)$matches[1]);
                break;
            }
        }

        return [
            'estado' => $resultado === 0 ? 'ACTIVO' : 'INACTIVO',
            'ms'     => $ms,
            'output' => implode("\n", $output),
        ];
    }
}
