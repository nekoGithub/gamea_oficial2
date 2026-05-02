<?php

namespace App\Http\Controllers;

use App\Models\BaseDato;
use App\Models\Servidor;
use App\Models\Sistema;
use App\Models\Tecnologia;
use App\Models\Unidad;
use Illuminate\Http\Request;

class BusquedaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $like = '%' . strtolower($q) . '%';
        $resultados = [];

        // Sistemas
        Sistema::where(function ($query) use ($like) {
            $query->whereRaw('LOWER(nombre) LIKE ?', [$like])
                ->orWhereRaw('LOWER(descripcion) LIKE ?', [$like]);
        })
            ->limit(3)->get()
            ->each(fn($s) => $resultados[] = [
                'tipo'      => 'Sistema',
                'icon'      => 'ti ti-apps',
                'titulo'    => $s->nombre,
                'subtitulo' => $s->descripcion ?? '—',
                'url'       => route('admin.sistemas.index'),
            ]);

        // Servidores
        Servidor::where(function ($query) use ($like) {
            $query->whereRaw('LOWER(nombre) LIKE ?', [$like])
                ->orWhereRaw('LOWER(ip_interna) LIKE ?', [$like]);
        })
            ->limit(3)->get()
            ->each(fn($s) => $resultados[] = [
                'tipo'      => 'Servidor',
                'icon'      => 'ti ti-server',
                'titulo'    => $s->nombre,
                'subtitulo' => $s->ip_interna ?? '—',
                'url'       => route('admin.servidores.index'),
            ]);

        // Tecnologías
        Tecnologia::whereRaw('LOWER(nombre) LIKE ?', [$like])
            ->limit(3)->get()
            ->each(fn($t) => $resultados[] = [
                'tipo'      => 'Tecnología',
                'icon'      => 'ti ti-code',
                'titulo'    => $t->nombre,
                'subtitulo' => 'v' . $t->version,
                'url'       => route('admin.tecnologias.index'),
            ]);

        // Bases de Datos
        BaseDato::whereRaw('LOWER(gestor) LIKE ?', [$like])
            ->limit(2)->get()
            ->each(fn($b) => $resultados[] = [
                'tipo'      => 'Base de Datos',
                'icon'      => 'ti ti-database',
                'titulo'    => $b->gestor,
                'subtitulo' => 'v' . $b->version,
                'url'       => route('admin.bases-datos.index'),
            ]);

        // Unidades
        Unidad::where(function ($query) use ($like) {
            $query->whereRaw('LOWER(nombre) LIKE ?', [$like])
                ->orWhereRaw('LOWER(sigla) LIKE ?', [$like]);
        })
            ->limit(2)->get()
            ->each(fn($u) => $resultados[] = [
                'tipo'      => 'Unidad',
                'icon'      => 'ti ti-building',
                'titulo'    => $u->nombre,
                'subtitulo' => $u->sigla,
                'url'       => route('admin.unidades.index'),
            ]);

        return response()->json($resultados);
    }
}
