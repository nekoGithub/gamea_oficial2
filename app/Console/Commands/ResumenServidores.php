<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Servidor;
use App\Services\TelegramService;

class ResumenServidores extends Command
{
    protected $signature   = 'servidores:resumen {--hora=}';
    protected $description = 'Envía resumen de estado de servidores a Telegram';

    public function handle()
    {
        $hora    = $this->option('hora') ?? now()->format('H:i');
        $this->info("Generando resumen de servidores [{$hora}]...");

        $servidores = Servidor::where('estado', 'activo')->get();

        $inactivos    = [];
        $desconocidos = [];

        foreach ($servidores as $srv) {
            if ($srv->disponibilidad_interna === 'INACTIVO') {
                $inactivos[] = [
                    'nombre'     => $srv->nombre,
                    'ip_interna' => $srv->ip_interna,
                    'ip_externa' => $srv->ip_externa,
                ];
            } elseif ($srv->disponibilidad_externa === 'DESCONOCIDO' && $srv->ip_externa) {
                $desconocidos[] = [
                    'nombre'     => $srv->nombre,
                    'ip_interna' => $srv->ip_interna,
                    'ip_externa' => null,
                ];
            }
        }

        if (empty($inactivos) && empty($desconocidos)) {
            $this->info('Todos los servidores están activos — no se envía resumen.');
            return;
        }

        $telegramService = app(TelegramService::class);
        $enviado = $telegramService->sendResumenServidores($inactivos, $desconocidos, $hora);

        $this->info($enviado
            ? "Resumen enviado a Telegram"
            : "Error al enviar resumen"
        );
    }
}