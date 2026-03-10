<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Tecnologia extends Model
{
    use SoftDeletes;

    protected $table = 'tecnologias';

    protected $fillable = [
        'nombre',
        'version',
        'descripcion',
        'url_documentacion',
        'fecha_lanzamiento',
        'fecha_fin_soporte',
        'tipo',
        'estado'
    ];

    protected $casts = [
        'fecha_lanzamiento' => 'date',
        'fecha_fin_soporte' => 'date',
    ];
    protected $appends = [
        'vigencia',
        'dias_restante_soporte'
    ];


    public function versiones()
    {
        return $this->belongsToMany(
            SistemaVersion::class,
            'sistema_version_tecnologias'
        );
    }


    /**
     * Accessor para calcular la vigencia de la tecnología
     * - vigente: fecha_fin_soporte es futura o null
     * - desactualizada: fecha_fin_soporte vence en menos de 6 meses
     * - obsoleta: fecha_fin_soporte ya pasó
     */
    protected function vigencia(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->fecha_fin_soporte) {
                    return 'vigente';
                }

                $hoy = Carbon::now()->startOfDay();
                $finSoporte = Carbon::parse($this->fecha_fin_soporte)->startOfDay();

                if ($finSoporte->isPast()) {
                    return 'obsoleta';
                }

                $mesesRestantes = $hoy->diffInMonths($finSoporte, false);

                if ($mesesRestantes <= 6) {
                    return 'desactualizada';
                }

                return 'vigente';
            }
        );
    }


    /**
     * Accessor para obtener el tiempo restante hasta el fin de soporte
     */
    protected function diasRestanteSoporte(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->fecha_fin_soporte) {
                    return null;
                }

                $hoy = Carbon::now()->startOfDay();
                $finSoporte = Carbon::parse($this->fecha_fin_soporte)->startOfDay();

                return $hoy->diffInDays($finSoporte, false);
            }
        );
    }
}
