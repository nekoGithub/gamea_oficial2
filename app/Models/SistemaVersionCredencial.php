<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemaVersionCredencial extends Model
{
    protected $table = 'sistema_version_credenciales';
    public $timestamps = false;

    protected $fillable = ['sistema_version_id', 'credencial_id'];
}
