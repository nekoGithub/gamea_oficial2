<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Models\Servidor;
use App\Models\Sistema;
use App\Models\Ssl;
use App\Models\Tecnologia;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:dashboard')->only('index');
    }

    public function index()
    {
        // Sistemas
        $totalSistemas = Sistema::count();
        $sistemasActivos = Sistema::where('estado', 'activo')->count();
        $sistemasInactivos = Sistema::where('estado', 'inactivo')->count();
        $porcentajeSistemasActivos = $totalSistemas > 0 ? round(($sistemasActivos / $totalSistemas) * 100, 1) : 0;

        // SSL
        $totalSsl = Ssl::count();
        $sslValidos = Ssl::where('estado', 'valido')->count();
        $sslPorVencer = Ssl::where('estado', 'proximo_vencer')->count();
        $sslVencidos = Ssl::where('estado', 'vencido')->count();
        $porcentajeSslValidos = $totalSsl > 0 ? round(($sslValidos / $totalSsl) * 100, 1) : 0;

        // Servidores
        $totalServidores = Servidor::count();
        $servidoresActivos = Servidor::where('estado', 'activo')->count();
        $servidoresFisicos = Servidor::where('tipo_servidor', 'físico')->count();
        $servidoresVirtuales = Servidor::where('tipo_servidor', 'virtual')->count();
        $porcentajeServidoresActivos = $totalServidores > 0 ? round(($servidoresActivos / $totalServidores) * 100, 1) : 0;

        // Tecnologías
        $totalTecnologias = Tecnologia::where('estado', 'activo')->count();
        $tecnologiasBackend = Tecnologia::where('tipo', 'backend')->where('estado', 'activo')->count();
        $tecnologiasFrontend = Tecnologia::where('tipo', 'frontend')->where('estado', 'activo')->count();
        $tecnologiasObsoletas = Tecnologia::whereNotNull('fecha_fin_soporte')
            ->whereDate('fecha_fin_soporte', '<', now())
            ->count();

        // Auditorías por día
        $auditoriasPorDia = Auditoria::selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();

        $fechasAuditorias = [];
        $totalesAuditorias = [];

        for ($i = 29; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $fechasAuditorias[] = now()->subDays($i)->locale('es')->isoFormat('D MMM');
            $auditoria = $auditoriasPorDia->firstWhere('fecha', $fecha);
            $totalesAuditorias[] = $auditoria ? $auditoria->total : 0;
        }

        // Tecnologías con sistemas
        $tecnologiasConSistemas = Tecnologia::with(['versiones.sistema'])
            ->where('estado', 'activo')
            ->get()
            ->map(function ($tecnologia) {
                $sistemasUnicos = $tecnologia->versiones()
                    ->with('sistema')
                    ->get()
                    ->pluck('sistema.id')
                    ->unique()
                    ->count();

                return [
                    'nombre' => $tecnologia->nombre,
                    'cantidad' => $sistemasUnicos,
                    'tipo' => $tecnologia->tipo,
                ];
            })
            ->filter(fn($t) => $t['cantidad'] > 0)
            ->sortByDesc('cantidad')
            ->take(10)
            ->values();

        // SSL por vencer
        $sslPorVencerProximos = Ssl::with('sistemas')
            ->where(function ($q) {
                $q->where('estado', 'proximo_vencer')
                    ->orWhere(function ($subQ) {
                        $subQ->where('estado', 'valido')
                            ->whereDate('fecha_expiracion', '<=', now()->addDays(30));
                    });
            })
            ->orderBy('fecha_expiracion')
            ->limit(5)
            ->get();

        // ✅ Top servidores - CORREGIDO
        $topServidores = Servidor::select('servidores.*')
            ->selectRaw('(
                SELECT COUNT(DISTINCT sv.sistema_id) 
                FROM sistema_version_servidores svs
                INNER JOIN sistema_versiones sv ON sv.id = svs.sistema_version_id
                WHERE svs.servidor_id = servidores.id
            ) as sistemas_count')
            ->where('estado', 'activo')
            ->orderByDesc('sistemas_count')
            ->limit(3)
            ->get();

        // Actividad reciente
        $actividadReciente = Auditoria::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('dashboard', [
            'totalSistemas' => $totalSistemas,
            'sistemasActivos' => $sistemasActivos,
            'sistemasInactivos' => $sistemasInactivos,
            'porcentajeSistemasActivos' => $porcentajeSistemasActivos,
            'totalSsl' => $totalSsl,
            'sslValidos' => $sslValidos,
            'sslPorVencer' => $sslPorVencer,
            'sslVencidos' => $sslVencidos,
            'porcentajeSslValidos' => $porcentajeSslValidos,
            'totalServidores' => $totalServidores,
            'servidoresActivos' => $servidoresActivos,
            'servidoresFisicos' => $servidoresFisicos,
            'servidoresVirtuales' => $servidoresVirtuales,
            'porcentajeServidoresActivos' => $porcentajeServidoresActivos,
            'totalTecnologias' => $totalTecnologias,
            'tecnologiasBackend' => $tecnologiasBackend,
            'tecnologiasFrontend' => $tecnologiasFrontend,
            'tecnologiasObsoletas' => $tecnologiasObsoletas,
            'fechasAuditorias' => $fechasAuditorias,
            'totalesAuditorias' => $totalesAuditorias,
            'tecnologiasConSistemas' => $tecnologiasConSistemas,
            'sslPorVencerProximos' => $sslPorVencerProximos,
            'topServidores' => $topServidores,
            'actividadReciente' => $actividadReciente,
        ]);
    }
}
