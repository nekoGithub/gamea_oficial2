<?php

namespace App\Http\Controllers;

use App\Models\Servidor;
use App\Models\Sistema;

class MonitoreoController extends Controller
{
    public function index()
    {
        // ── Servidores ──────────────────────────────────────────
        $servidores = Servidor::where('estado', 'activo')
            ->with('sistemaOperativo')
            ->orderBy('nombre')
            ->get();

        $totalActivos      = $servidores->where('disponibilidad_interna', 'ACTIVO')->count();
        $totalInactivos    = $servidores->where('disponibilidad_interna', 'INACTIVO')->count();
        $totalDesconocidos = $servidores->where('disponibilidad_interna', 'DESCONOCIDO')->count();

        // ── Sistemas Web ────────────────────────────────────────        
        $sistemas = Sistema::where('estado', 'activo')
            ->whereNotNull('dominio')
            ->where('dominio', '!=', '')
            ->whereNotNull('ssl_id')  
            ->orderBy('nombre')
            ->get();

        $webActivos      = $sistemas->where('disponibilidad_web', 'ACTIVO')->count();
        $webInactivos    = $sistemas->where('disponibilidad_web', 'INACTIVO')->count();
        $webDesconocidos = $sistemas->where('disponibilidad_web', 'DESCONOCIDO')->count();

        return view('admin.monitoreo.index', compact(
            'servidores',
            'totalActivos',
            'totalInactivos',
            'totalDesconocidos',
            'sistemas',
            'webActivos',
            'webInactivos',
            'webDesconocidos'
        ));
    }

    public function pingServidor(Servidor $servidor)
    {
        $resultado = [];

        if ($servidor->ip_interna) {
            $cmd = "ping -c 4 -W 2 " . escapeshellarg($servidor->ip_interna) . " 2>&1";
            exec($cmd, $output, $code);
            $ms = null;
            foreach ($output as $linea) {
                if (preg_match('/time[=<]([\d.]+)\s*ms/i', $linea, $m)) {
                    $ms = round((float) $m[1]);
                    break;
                }
            }
            $resultado['interna'] = [
                'ip'     => $servidor->ip_interna,
                'estado' => $code === 0 ? 'ACTIVO' : 'INACTIVO',
                'ms'     => $ms,
                'output' => $output,
            ];
        }

        if ($servidor->ip_externa) {
            $output2 = [];
            $cmd2    = "ping -c 4 -W 2 " . escapeshellarg($servidor->ip_externa) . " 2>&1";
            exec($cmd2, $output2, $code2);
            $ms2 = null;
            foreach ($output2 as $linea) {
                if (preg_match('/time[=<]([\d.]+)\s*ms/i', $linea, $m)) {
                    $ms2 = round((float) $m[1]);
                    break;
                }
            }
            $resultado['externa'] = [
                'ip'     => $servidor->ip_externa,
                'estado' => $code2 === 0 ? 'ACTIVO' : 'INACTIVO',
                'ms'     => $ms2,
                'output' => $output2,
            ];
        }

        return response()->json([
            'servidor'  => $servidor->nombre,
            'timestamp' => now()->format('H:i:s'),
            'resultado' => $resultado,
        ]);
    }

    public function pingSistema(Sistema $sistema)
    {
        $dominio  = trim(preg_replace('/^https?:\/\//i', '', $sistema->dominio));
        $cmd      = "ping -c 4 -W 2 " . escapeshellarg($dominio) . " 2>&1";
        $inicio   = microtime(true);

        exec($cmd, $output, $code);

        $ms = null;
        foreach ($output as $linea) {
            if (preg_match('/time[=<]([\d.]+)\s*ms/i', $linea, $m)) {
                $ms = round((float) $m[1]);
                break;
            }
        }

        return response()->json([
            'sistema'   => $sistema->nombre,
            'dominio'   => $dominio,
            'timestamp' => now()->format('H:i:s'),
            'resultado' => [
                'dominio' => [
                    'ip'     => $dominio,
                    'estado' => $code === 0 ? 'ACTIVO' : 'INACTIVO',
                    'ms'     => $ms,
                    'output' => $output,
                ]
            ],
        ]);
    }
}
