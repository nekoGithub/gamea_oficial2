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
        'sistema_id',
        'usuario',
        'password_encrypted',
        'estado'
    ];

    protected $hidden = [
        'password_encrypted',
    ];

    public function sistema()
    {
        return $this->belongsTo(Sistema::class);
    }

    public function versiones()
    {
        return $this->belongsToMany(
            SistemaVersion::class,
            'sistema_version_credenciales'
        );
    }
}
