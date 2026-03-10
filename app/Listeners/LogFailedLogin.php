<?php

namespace App\Listeners;

use App\Models\Auditoria;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    public function handle(Failed $event): void
    {
        Auditoria::create([
            'user_id' => null,
            'accion' => 'login',
            'modulo' => 'users',
            'entidad_id' => null,
            'descripcion' => "Intento fallido de login con email: " . ($event->credentials['email'] ?? 'desconocido'),
            'valores_nuevos' => [
                'email' => $event->credentials['email'] ?? null,
            ],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}