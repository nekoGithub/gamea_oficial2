<?php

namespace App\Http\Controllers;

use App\Models\Tecnologia;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use DOMDocument;
use DOMXPath;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TecnologiaController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.tecnologias.index')->only('index');
        $this->middleware('can:admin.tecnologias.store')->only('store');
        $this->middleware('can:admin.tecnologias.edit')->only('edit');
        $this->middleware('can:admin.tecnologias.update')->only(['update', 'scrapeInfo']);
        $this->middleware('can:admin.tecnologias.destroy')->only('destroy');
        $this->middleware('can:admin.tecnologias.restore')->only('restore');
    }
    public function index()
    {
        $tecnologias = Tecnologia::orderBy('id', 'desc')->get();
        $tecnologiasEliminadas = Tecnologia::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.tecnologias.index', compact(
            'tecnologias',
            'tecnologiasEliminadas'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $anioMax = now()->year + 5;
        $validated = $request->validate([
            'nombre' => 'required|string|max:45',
            'version' => 'required|string|max:15',
            'descripcion' => 'nullable|string|max:1000',
            'url_documentacion' => 'nullable|url|max:120',
            'fecha_lanzamiento'  => ['nullable', 'date', 'after_or_equal:2015-01-01', 'before_or_equal:' . $anioMax . '-12-31'],
            'fecha_fin_soporte'  => ['nullable', 'date', 'after_or_equal:fecha_lanzamiento', 'after_or_equal:2015-01-01', 'before_or_equal:' . $anioMax . '-12-31'],
            'tipo' => 'required|in:backend,frontend,otros/librerias',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 45 caracteres.',
            'version.required' => 'La versión es obligatoria.',
            'version.max' => 'La versión no puede exceder 15 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres.',
            'url_documentacion.url' => 'Debe ingresar una URL válida.',
            'url_documentacion.max' => 'La URL no puede exceder 120 caracteres.',
            'fecha_lanzamiento.after_or_equal'  => 'La fecha debe ser desde el año 2015.',
            'fecha_lanzamiento.before_or_equal' => 'La fecha no puede exceder ' . $anioMax . '.',
            'fecha_fin_soporte.after_or_equal'  => 'La fecha de fin debe ser posterior a la de lanzamiento y desde 2015.',
            'fecha_fin_soporte.before_or_equal' => 'La fecha no puede exceder ' . $anioMax . '.',
            'tipo.required' => 'Debe seleccionar un tipo.',
            'tipo.in' => 'El tipo debe ser backend, frontend u otros/librerias.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ]);

        $tecnologia = Tecnologia::create($validated);

        return response()->json([
            'success' => true,
            'tecnologia' => $tecnologia->fresh()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tecnologia $tecnologia)
    {
        return response()->json([
            'tecnologia' => $tecnologia
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tecnologia $tecnologia)
    {
        $anioMax = now()->year + 5;
        $validated = $request->validate([
            'nombre' => 'required|string|max:45',
            'version' => 'required|string|max:15',
            'descripcion' => 'nullable|string|max:1000',
            'url_documentacion' => 'nullable|url|max:120',
            'fecha_lanzamiento'  => ['nullable', 'date', 'after_or_equal:2015-01-01', 'before_or_equal:' . $anioMax . '-12-31'],
            'fecha_fin_soporte'  => ['nullable', 'date', 'after_or_equal:fecha_lanzamiento', 'after_or_equal:2015-01-01', 'before_or_equal:' . $anioMax . '-12-31'],
            'tipo' => 'required|in:backend,frontend,otros/librerias',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.max' => 'El nombre no puede exceder 45 caracteres.',
            'version.required' => 'La versión es obligatoria.',
            'version.max' => 'La versión no puede exceder 15 caracteres.',
            'descripcion.max' => 'La descripción no puede exceder 1000 caracteres.',
            'url_documentacion.url' => 'Debe ingresar una URL válida.',
            'url_documentacion.max' => 'La URL no puede exceder 120 caracteres.',
            'fecha_lanzamiento.after_or_equal'  => 'La fecha debe ser desde el año 2015.',
            'fecha_lanzamiento.before_or_equal' => 'La fecha no puede exceder ' . $anioMax . '.',
            'fecha_fin_soporte.after_or_equal'  => 'La fecha de fin debe ser posterior a la de lanzamiento y desde 2015.',
            'fecha_fin_soporte.before_or_equal' => 'La fecha no puede exceder ' . $anioMax . '.',
            'tipo.required' => 'Debe seleccionar un tipo.',
            'tipo.in' => 'El tipo debe ser backend, frontend u otros/librerias.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ]);

        $tecnologia->update($validated);

        return response()->json([
            'success' => true,
            'tecnologia' => $tecnologia->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Tecnologia $tecnologia)
    {
        $tecnologia->delete();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $tecnologia = Tecnologia::onlyTrashed()->findOrFail($id);
        $tecnologia->restore();

        return response()->json([
            'success' => true,
            'tecnologia' => $tecnologia->fresh()
        ]);
    }

    public function scrapeInfo($id)
    {
        $tecnologia = Tecnologia::findOrFail($id);

        if (!$tecnologia->url_documentacion) {
            return response()->json(['success' => false, 'message' => 'No hay URL de documentación configurada'], 422);
        }

        $url = $tecnologia->url_documentacion;
        $version = (string) $tecnologia->version;

        try {
            $client = new Client(['verify' => false, 'timeout' => 15, 'headers' => ['User-Agent' => 'Mozilla/5.0']]);
            $res = $client->get($url);
            $html = (string) $res->getBody();

            // parse DOM once
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML($html);
            libxml_clear_errors();
            $xpath = new DOMXPath($dom);

            // Resultado base
            $resultado = [
                'fecha_lanzamiento' => null,
                'fecha_fin_soporte' => null,
                'fuente' => $url,
            ];

            // 1) Intento: TABLAS HTML (laravel, ubuntu, muchos docs)
            $fromTable = $this->scrapearTablas($xpath, $version);
            if ($fromTable['fecha_lanzamiento'] || $fromTable['fecha_fin_soporte']) {
                $resultado = array_merge($resultado, $fromTable);
                return response()->json(['success' => true, 'data' => $resultado]);
            }

            // 2) Intento: JSON-LD / schema.org / meta tags
            $meta = $this->buscarMetadatos($dom);
            if ($meta['fecha_lanzamiento'] || $meta['fecha_fin_soporte']) {
                $resultado = array_merge($resultado, $meta);
                return response()->json(['success' => true, 'data' => $resultado]);
            }

            // 3) Intento: regex sobre texto (manejar "February 14th, 2023" y "2023-02-14")
            $textSearch = $this->buscarFechasPorRegex($html);
            if ($textSearch['fecha_lanzamiento'] || $textSearch['fecha_fin_soporte']) {
                $resultado = array_merge($resultado, $textSearch);
                return response()->json(['success' => true, 'data' => $resultado]);
            }

            // 4) Intento: GitHub Releases API si es github.com o se detecta repo
            if (Str::contains($url, 'github.com')) {
                $gh = $this->buscarEnGithub($url, $version);
                if ($gh['fecha_lanzamiento'] || $gh['fecha_fin_soporte']) {
                    $resultado = array_merge($resultado, $gh);
                    return response()->json(['success' => true, 'data' => $resultado]);
                }
            }

            // Ninguna estrategia funcionó
            return response()->json(['success' => false, 'message' => 'No se encontraron fechas (scraping asistido)'], 200);
        } catch (\Throwable $e) {
            Log::error('scrapeInfo error', ['url' => $url, 'error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Error al analizar la página'], 500);
        }
    }

    /* -------------------------
   Funciones auxiliares
   -------------------------*/

    private function scrapearTablas(DOMXPath $xpath, string $version)
    {
        /** @var \DOMNodeList $rows */
        $rows = $xpath->query('//table//tr');
        $res = ['fecha_lanzamiento' => null, 'fecha_fin_soporte' => null];

        /** @var \DOMElement $row */
        foreach ($rows as $row) {
            if (!($row instanceof \DOMElement)) continue;
            $cols = $row->getElementsByTagName('td');
            // Si es cabecera con th, saltar
            if ($cols->length === 0) continue;

            // Normalizar texto de la primera celda (version)
            $v = trim(preg_replace('/\s+/', ' ', $cols->item(0)->textContent));
            if ($this->versionCoincide($v, $version)) {
                // Atención: muchas tablas cambian columnas: detecta por encabezado si es posible
                // Aquí asumimos que la fecha de lanzamiento y fin de soporte están en columnas comunes
                // Ajusta indices si tu tabla tiene otro orden
                $textCols = [];
                for ($i = 0; $i < $cols->length; $i++) {
                    $textCols[] = trim($cols->item($i)->textContent);
                }

                // heurística: buscar en columnas por patrones
                foreach ($textCols as $idx => $txt) {
                    $f = $this->normalizarFechaTexto($txt);
                    if ($f && !$res['fecha_lanzamiento']) {
                        $res['fecha_lanzamiento'] = $f;
                    } elseif ($f && !$res['fecha_fin_soporte']) {
                        // si ya hay lanzamiento, siguiente fecha es fin soporte
                        $res['fecha_fin_soporte'] = $f;
                    }
                }
                break;
            }
        }

        return $res;
    }

    private function buscarMetadatos(DOMDocument $dom)
    {
        // Buscar JSON-LD, meta tags og:updated_time, article:published_time, etc.
        $res = ['fecha_lanzamiento' => null, 'fecha_fin_soporte' => null];

        // JSON-LD
        $scripts = $dom->getElementsByTagName('script');
        foreach ($scripts as $s) {
            if ($s->getAttribute('type') === 'application/ld+json') {
                $json = @json_decode($s->textContent, true);
                if (is_array($json)) {
                    // common: datePublished, dateModified
                    if (isset($json['datePublished'])) $res['fecha_lanzamiento'] = $this->normalizarFechaTexto($json['datePublished']);
                    if (isset($json['dateModified'])) $res['fecha_fin_soporte'] = $this->normalizarFechaTexto($json['dateModified']);
                    // si es array de items, iterar
                    if (($res['fecha_lanzamiento'] || $res['fecha_fin_soporte'])) break;
                }
            }
        }

        // meta tags (og)
        $metas = $dom->getElementsByTagName('meta');
        foreach ($metas as $m) {
            $p = strtolower($m->getAttribute('property') ?: $m->getAttribute('name'));
            $c = trim($m->getAttribute('content'));
            if (!$c) continue;
            if (in_array($p, ['article:published_time', 'og:updated_time', 'published_time'])) {
                if (!$res['fecha_lanzamiento']) $res['fecha_lanzamiento'] = $this->normalizarFechaTexto($c);
            }
            if (in_array($p, ['article:modified_time', 'updated_time'])) {
                if (!$res['fecha_fin_soporte']) $res['fecha_fin_soporte'] = $this->normalizarFechaTexto($c);
            }
        }

        return $res;
    }

    private function buscarFechasPorRegex(string $html)
    {
        $res = ['fecha_lanzamiento' => null, 'fecha_fin_soporte' => null];

        // patrones: 'February 14th, 2023', 'Feb 14, 2023', '2023-02-14', '14/02/2023'
        $patrones = [
            '/([A-Za-z]+)\s+(\d{1,2})(?:st|nd|rd|th)?,\s+(\d{4})/i',
            '/\b(\d{4}[-\/]\d{1,2}[-\/]\d{1,2})\b/',
            '/\b(\d{1,2}[-\/]\d{1,2}[-\/]\d{4})\b/',
        ];

        foreach ($patrones as $pat) {
            if (preg_match_all($pat, $html, $ms, PREG_SET_ORDER)) {
                // Tomar la primera como lanzamiento y la segunda como fin soporte (heurística)
                if (isset($ms[0])) $res['fecha_lanzamiento'] = $this->normalizarFechaTexto(implode(' ', array_slice($ms[0], 1)));
                if (isset($ms[1])) $res['fecha_fin_soporte'] = $this->normalizarFechaTexto(implode(' ', array_slice($ms[1], 1)));
                break;
            }
        }

        return $res;
    }

    private function buscarEnGithub(string $url, string $version)
    {
        // heurística: extraer owner/repo de URL
        $res = ['fecha_lanzamiento' => null, 'fecha_fin_soporte' => null];

        if (!preg_match('#github\.com/([^/]+)/([^/]+)#i', $url, $m)) return $res;
        $owner = $m[1];
        $repo = rtrim($m[2], '.git');

        // llamar a API releases
        $apiUrl = "https://api.github.com/repos/{$owner}/{$repo}/releases";
        try {
            $client = new Client(['timeout' => 10, 'headers' => ['User-Agent' => 'Mozilla/5.0']]);
            $r = $client->get($apiUrl);
            $list = json_decode((string)$r->getBody(), true);

            foreach ($list as $rel) {
                // buscar tag_name o name que contenga la version
                if (isset($rel['tag_name']) && Str::contains($rel['tag_name'], $version)) {
                    if (!empty($rel['published_at'])) $res['fecha_lanzamiento'] = $this->normalizarFechaTexto($rel['published_at']);
                    // GitHub no tiene 'end of support' por release; eso hay que deducir o tomar de body
                    if (!empty($rel['body'])) {
                        // intentar extraer fechas dentro de body
                        $maybe = $this->buscarFechasPorRegex($rel['body']);
                        if ($maybe['fecha_fin_soporte']) $res['fecha_fin_soporte'] = $maybe['fecha_fin_soporte'];
                    }
                    break;
                }
            }
        } catch (\Throwable $e) {
            // silenciar
        }

        return $res;
    }

    private function versionCoincide(string $vTabla, string $version)
    {
        // normalizar: quitar prefijos 'v', espacios y comparar parcial
        $a = preg_replace('/[^0-9\.]/', '', $vTabla);
        $b = preg_replace('/[^0-9\.]/', '', $version);
        return $a === $b || Str::startsWith($a, $b) || Str::startsWith($b, $a);
    }

    private function normalizarFechaTexto($texto)
    {
        if (!$texto) {
            return null;
        }

        $texto = trim(strtolower($texto));

        // quitar ordinales ingleses (st, nd, rd, th)
        $texto = preg_replace('/\b(st|nd|rd|th)\b/', '', $texto);

        try {
            /**
             * CASO 1: solo año → 2026
             */
            if (preg_match('/^\d{4}$/', $texto)) {
                return Carbon::createFromDate((int)$texto, 1, 1)->format('Y-m-d');
            }

            /**
             * CASO 2: mes + año → july 2026 | june 2029
             */
            if (preg_match('/^(january|february|march|april|may|june|july|august|september|october|november|december)\s+\d{4}$/i', $texto)) {
                return Carbon::parse('1 ' . $texto)->format('Y-m-d');
            }

            /**
             * CASO 3: fecha completa → usar normal
             */
            return Carbon::parse($texto)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
