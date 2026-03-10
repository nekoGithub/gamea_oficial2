<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemaVersionBaseDato extends Model
{
    protected $table = 'sistema_version_bases_datos';    

    protected $fillable = ['sistema_version_id', 'base_datos_id'];
}
