<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
/* use Illuminate\Contracts\Broadcasting\ShouldBroadcast; */
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TestWebSocket implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $mensaje;

    public function __construct($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function broadcastOn()
    {
        return new Channel('test-canal');
    }

    public function broadcastAs()
    {
        return 'test.evento';
    }
}