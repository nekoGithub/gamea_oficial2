<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemaVersionServidor extends Model
{
    protected $table = 'sistema_version_servidores';    

    protected $fillable = ['sistema_version_id', 'servidor_id'];
}
