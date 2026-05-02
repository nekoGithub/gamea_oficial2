<?php

namespace App\Http\Controllers;

use App\Models\Servidor;
use App\Models\Sistema;

class MonitoreoController extends Controller
{
    public function index()
    {
        $servidores = Servidor::where('estado', 'activo')
            ->with('sistemaOperativo')
            ->orderBy('nombre')
            ->get();

        $totalActivos      = $servidores->where('disponibilidad_interna', 'ACTIVO')->count();
        $totalInactivos    = $servidores->where('disponibilidad_interna', 'INACTIVO')->count();
        $totalDesconocidos = $servidores->where('disponibilidad_interna', 'DESCONOCIDO')->count();

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

    // ── Helper: ping compatible Windows/Linux ─────────────────
    private function hacerPing(string $ip): array
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $cmd = 'C:\\Windows\\System32\\ping.exe -n 4 -w 1000 ' . escapeshellarg($ip) . ' 2>&1';
        } else {
            $cmd = 'ping -c 4 -W 2 ' . escapeshellarg($ip) . ' 2>&1';
        }

        exec($cmd, $output, $code);

        // ✅ Convertir encoding Windows CP850 → UTF-8
        if (PHP_OS_FAMILY === 'Windows') {
            $output = array_map(fn($l) => mb_convert_encoding($l, 'UTF-8', 'CP850'), $output);
        }

        $ms = null;
        foreach ($output as $linea) {
            if (
                preg_match('/tiempo[=<]([\d.]+)\s*ms/i', $linea, $m) ||
                preg_match('/time[=<]([\d.]+)\s*ms/i',   $linea, $m)
            ) {
                $ms = round((float) $m[1]);
                break;
            }
        }

        return [
            'ip'     => $ip,
            'estado' => $code === 0 ? 'ACTIVO' : 'INACTIVO',
            'ms'     => $ms,
            'output' => $output,
        ];
    }

    // ── Ping servidor ─────────────────────────────────────────
    public function pingServidor(Servidor $servidor)
    {
        $resultado = [];

        if ($servidor->ip_interna) {
            $resultado['interna'] = $this->hacerPing($servidor->ip_interna);
        }

        if ($servidor->ip_externa) {
            $resultado['externa'] = $this->hacerPing($servidor->ip_externa);
        }

        return response()->json([
            'servidor'  => $servidor->nombre,
            'timestamp' => now()->format('H:i:s'),
            'resultado' => $resultado,
        ]);
    }

    // ── Ping sistema web ──────────────────────────────────────
    public function pingSistema(Sistema $sistema)
    {
        $dominio = trim(preg_replace('/^https?:\/\//i', '', $sistema->dominio));
        $ping    = $this->hacerPing($dominio);

        return response()->json([
            'sistema'   => $sistema->nombre,
            'dominio'   => $dominio,
            'timestamp' => now()->format('H:i:s'),
            'resultado' => [
                'dominio' => $ping,
            ],
        ]);
    }
}
