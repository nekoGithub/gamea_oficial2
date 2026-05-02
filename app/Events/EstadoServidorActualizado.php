<?php

namespace App\Events;

use App\Models\Servidor;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EstadoServidorActualizado implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $servidor;
    public $pingInterna;
    public $pingExterna;

    public function __construct(Servidor $servidor, array $pingInterna = [], array $pingExterna = [])
    {
        $this->servidor   = $servidor;
        $this->pingInterna = $pingInterna;
        $this->pingExterna = $pingExterna;
    }

    public function broadcastOn()
    {
        return new Channel('monitoreo-servidores');
    }

    public function broadcastAs()
    {
        return 'estado.actualizado';
    }

    public function broadcastWith()
    {
        return [
            'id'                     => $this->servidor->id,
            'nombre'                 => $this->servidor->nombre,
            'ip_interna'             => $this->servidor->ip_interna,
            'ip_externa'             => $this->servidor->ip_externa,
            'disponibilidad_interna' => $this->servidor->disponibilidad_interna,
            'disponibilidad_externa' => $this->servidor->disponibilidad_externa,
            'ms_interna'             => $this->pingInterna['ms'] ?? null,
            'ms_externa'             => $this->pingExterna['ms'] ?? null,
            'ultima_verificacion'    => $this->servidor->ultima_verificacion
                ? $this->servidor->ultima_verificacion->format('d/m/Y H:i:s')
                : null,
        ];
    }
}
