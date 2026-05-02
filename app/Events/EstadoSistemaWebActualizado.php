<?php

namespace App\Events;

use App\Models\Sistema;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EstadoSistemaWebActualizado implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $sistema;

    public function __construct(Sistema $sistema)
    {
        $this->sistema = $sistema;
    }

    public function broadcastOn()
    {
        return new Channel('monitoreo-web');
    }

    public function broadcastAs()
    {
        return 'estado.web.actualizado';
    }

    public function broadcastWith()
    {
        return [
            'id'                    => $this->sistema->id,
            'nombre'                => $this->sistema->nombre,
            'sigla'                 => $this->sistema->sigla,
            'dominio'               => $this->sistema->dominio,
            'disponibilidad_web'    => $this->sistema->disponibilidad_web,
            'http_status'           => $this->sistema->http_status,
            'tiempo_respuesta'      => $this->sistema->tiempo_respuesta,
            'ultima_verificacion_web' => $this->sistema->ultima_verificacion_web
                ? $this->sistema->ultima_verificacion_web->format('H:i:s')
                : null,
        ];
    }
}
