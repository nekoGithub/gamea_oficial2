<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemaVersionTecnologia extends Model
{
    protected $table = 'sistema_version_tecnologias';    

    protected $fillable = ['sistema_version_id', 'tecnologia_id'];
}
