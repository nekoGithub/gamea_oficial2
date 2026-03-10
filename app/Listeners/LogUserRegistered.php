<?php

namespace App\Listeners;

use App\Models\Auditoria;
use Illuminate\Auth\Events\Registered;

class LogUserRegistered
{
    public function handle(Registered $event): void
    {
        Auditoria::create([
            'user_id' => $event->user->id,
            'accion' => 'created',
            'modulo' => 'users',
            'entidad_id' => $event->user->id,
            'descripcion' => 'Nuevo usuario registrado: ' . $event->user->email,
            'valores_nuevos' => [
                'name' => $event->user->name,
                'email' => $event->user->email,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
