<?php

namespace App\Listeners;

use App\Models\Auditoria;
use Illuminate\Auth\Events\Login; 

class LogSuccessfulLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void 
    {
        Auditoria::create([
            'user_id' => $event->user->id,
            'accion' => 'login',
            'modulo' => 'users',
            'entidad_id' => $event->user->id,
            'descripcion' => 'Usuario inició sesión exitosamente',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}