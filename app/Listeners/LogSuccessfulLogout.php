<?php

namespace App\Listeners;

use App\Models\Auditoria;
use Illuminate\Auth\Events\Logout;

class LogSuccessfulLogout
{
    public function handle(Logout $event): void
    {
        if ($event->user) {
            Auditoria::create([
                'user_id' => $event->user->id,
                'accion' => 'logout',
                'modulo' => 'users',
                'entidad_id' => $event->user->id,
                'descripcion' => 'Usuario cerró sesión',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}
