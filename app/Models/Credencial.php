<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credencial extends Model
{
    use SoftDeletes, Auditable;

    protected $table = 'credenciales';

    protected $fillable = [
        'usuario',
        'password_encrypted',
        'url_acceso',
        'estado'
    ];

    protected $hidden = [
        'password_encrypted',
    ];

    public function versiones()
    {
        return $this->belongsToMany(
            SistemaVersion::class,
            'sistema_version_credenciales'
        );
    }
}
