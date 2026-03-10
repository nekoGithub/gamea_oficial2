<?php

namespace App\Models;

use App\Traits\Auditable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ssl extends Model
{
    use SoftDeletes, Auditable;
    protected $table = 'ssls';

    protected $fillable = [
        'emisor',
        'archivo_ssl',
        'fecha_emision',
        'fecha_expiracion',
        'estado'
    ];

    protected $appends = ['dias_restantes'];

    public function sistemas()
    {
        return $this->hasMany(Sistema::class);
    }

    protected $casts = [
        'fecha_emision' => 'datetime:Y-m-d',
        'fecha_expiracion' => 'datetime:Y-m-d',
    ];

    /**
     * Accessor para obtener la URL del archivo SSL.
     */
    public function getArchivoSslUrlAttribute()
    {
        if ($this->archivo_ssl) {
            return asset('storage/' . $this->archivo_ssl);
        }
        return null;
    }

    public function getDiasRestantesAttribute()
    {
        $hoy = Carbon::today();
        $expiracion = Carbon::parse($this->fecha_expiracion);

        $dias = $hoy->diffInDays($expiracion, false);
        return (int) floor($dias); 
    }

    /**
     * Verificar si el certificado está vencido
     */
    public function getEstaVencidoAttribute()
    {
        return $this->fecha_expiracion < now();
    }

    /**
     * Verificar si el certificado está por vencer (30 días)
     */
    public function getEstaPorVencerAttribute()
    {
        $dias = $this->dias_restantes;
        return $dias >= 0 && $dias <= 30;
    }


    /**
     * Scope para filtrar SSLs válidos.
     */
    public function scopeValidos($query)
    {
        return $query->where('estado', 'valido');
    }

    /**
     * Scope para filtrar SSLs próximos a vencer.
     */
    public function scopeProximosVencer($query)
    {
        return $query->where('estado', 'proximo_vencer');
    }

    /**
     * Scope para filtrar SSLs vencidos.
     */
    public function scopeVencidos($query)
    {
        return $query->where('estado', 'vencido');
    }
}
