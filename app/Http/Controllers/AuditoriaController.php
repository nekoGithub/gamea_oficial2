<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use App\Services\AuditoriaPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuditoriaController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.auditorias.index')->only(['index', 'datatable', 'count']);
        $this->middleware('can:admin.auditorias.show')->only('show');
        $this->middleware('can:admin.reportes.generar')->only('exportarPDF');
        $this->middleware('can:admin.auditorias.index')->only('limpiar');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Solo usuarios únicos para el filtro
        $usuarios = Auditoria::with('user')
            ->get()
            ->pluck('user')
            ->unique('id')
            ->filter();

        return view('admin.auditorias.index', compact('usuarios'));
    }

    /**
     * ✅ NUEVO: API para DataTables con paginación del servidor
     */
    public function datatable(Request $request)
    {
        $query = Auditoria::with('user');

        // ========== FILTROS GLOBALES ==========
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('usuario')) {
            $query->where('user_id', $request->usuario);
        }

        // ========== BÚSQUEDA POR COLUMNA (DataTables) ==========
        if ($request->has('columns')) {
            foreach ($request->columns as $index => $column) {
                if (!empty($column['search']['value'])) {
                    $searchValue = $column['search']['value'];

                    switch ($index) {
                        case 0: // ID
                            $query->where('id', 'like', "%{$searchValue}%");
                            break;
                        case 1: // Usuario
                            $query->whereHas('user', function ($q) use ($searchValue) {
                                $q->where('name', 'like', "%{$searchValue}%");
                            });
                            break;
                        case 2: // Acción
                            $query->where('accion', 'like', "%{$searchValue}%");
                            break;
                        case 3: // Módulo
                            $query->where('modulo', 'like', "%{$searchValue}%");
                            break;
                        case 4: // Descripción
                            $query->where('descripcion', 'like', "%{$searchValue}%");
                            break;
                        case 5: // IP
                            $query->where('ip_address', 'like', "%{$searchValue}%");
                            break;
                        case 6: // Fecha
                            $query->whereDate('created_at', 'like', "%{$searchValue}%");
                            break;
                    }
                }
            }
        }

        // ========== BÚSQUEDA GLOBAL ==========
        if ($request->has('search') && !empty($request->search['value'])) {
            $searchValue = $request->search['value'];
            $query->where(function ($q) use ($searchValue) {
                $q->where('descripcion', 'like', "%{$searchValue}%")
                    ->orWhere('ip_address', 'like', "%{$searchValue}%")
                    ->orWhere('modulo', 'like', "%{$searchValue}%")
                    ->orWhere('accion', 'like', "%{$searchValue}%")
                    ->orWhereHas('user', function ($q) use ($searchValue) {
                        $q->where('name', 'like', "%{$searchValue}%");
                    });
            });
        }

        // ========== ORDENAMIENTO ==========
        if ($request->has('order')) {
            $orderColumnIndex = $request->order[0]['column'];
            $orderDirection = $request->order[0]['dir'];

            $columns = ['id', 'user_id', 'accion', 'modulo', 'descripcion', 'ip_address', 'created_at'];

            if (isset($columns[$orderColumnIndex])) {
                $query->orderBy($columns[$orderColumnIndex], $orderDirection);
            }
        } else {
            // Por defecto: más reciente primero
            $query->orderBy('id', 'desc');
        }

        // ========== CONTAR REGISTROS ==========
        $totalRecords = Auditoria::count();
        $filteredRecords = $query->count();

        // ========== PAGINACIÓN ==========
        $start = $request->input('start', 0);
        $length = $request->input('length', 25);

        $auditorias = $query->skip($start)->take($length)->get();

        // ========== FORMATEAR DATOS ==========
        $data = $auditorias->map(function ($auditoria) {
            return [
                $auditoria->id,
                $this->getUsuarioHtml($auditoria->nombre_usuario),
                $auditoria->accion_badge,
                $this->getModuloHtml($auditoria->modulo, $auditoria->modulo_icono),
                e(mb_strimwidth($auditoria->descripcion, 0, 50, '...')),
                '<code class="text-muted">' . ($auditoria->ip_address ?? '—') . '</code>',
                '<small class="text-muted">'
                    . $auditoria->created_at->locale('es')->isoFormat('DD MMM, YYYY') . '<br>'
                    . '<span class="text-muted">' . $auditoria->created_at->format('H:i:s') . '</span>'
                    . '</small>',
                $this->getAccionesHtml($auditoria->id)
            ];
        });

        // ========== RESPUESTA JSON ==========
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    /**
     * Show the form for viewing a specific audit.
     */
    public function show(Auditoria $auditoria)
    {
        $auditoria->load('user');

        return response()->json([
            'auditoria' => $auditoria
        ]);
    }

    /**
     * Limpiar auditorías antiguas (opcional - cron job)
     */
    public function limpiar(Request $request)
    {
        $dias = $request->input('dias', 90);

        $eliminadas = Auditoria::where('created_at', '<', now()->subDays($dias))
            ->delete();

        return response()->json([
            'success' => true,
            'eliminadas' => $eliminadas
        ]);
    }

    /**
     * Exportar auditorías a PDF
     */
    public function exportarPDF(Request $request)
    {

        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '512M');
        // ========== OBTENER DATOS FILTRADOS ==========
        $query = Auditoria::with('user');

        // Aplicar los mismos filtros que en datatable
        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }

        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->filled('usuario')) {
            $query->where('user_id', $request->usuario);
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        // Ordenar por más reciente
        $auditorias = $query->orderBy('id', 'asc')
            ->limit(1000) // Límite para no sobrecargar el PDF
            ->get();

        if ($auditorias->isEmpty()) {
            return redirect()->back()->with('error', 'No hay registros para exportar');
        }

        // ========== CREAR PDF ==========
        $pdf = new AuditoriaPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Información del documento
        $pdf->SetCreator('GAMEA - El Alto');
        $pdf->SetAuthor('Gobierno Autónomo Municipal de El Alto');
        $pdf->SetTitle('Reporte de Auditorías - GAMEA');
        $pdf->SetSubject('Auditorías del Sistema de Gestión');
        $pdf->SetKeywords('Auditoría, Reporte, GAMEA, El Alto');

        // Configuración de márgenes
        $pdf->SetMargins(15, 38, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(15);
        $pdf->SetAutoPageBreak(true, 20);

        // Agregar página
        $pdf->AddPage();

        // ✅ AGREGAR MARCA DE AGUA
        $pdf->setMarcaAgua();

        // ========== INFORMACIÓN DEL REPORTE ==========
        $this->agregarInfoReporte($pdf, $request, $auditorias);

        // ========== TABLA DE AUDITORÍAS ==========
        $this->agregarTablaAuditorias($pdf, $auditorias);

        // ❌ ESTADÍSTICAS ELIMINADAS - Ya no se llama al método

        // ========== GENERAR PDF ==========
        $nombreArchivo = 'reporte_auditorias_' . date('Y-m-d_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            $pdf->Output('', 'I');
        }, $nombreArchivo, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Agregar información del reporte
     */
    private function agregarInfoReporte($pdf, $request, $auditorias)
    {
        // ✅ ASEGURAR POSICIÓN CORRECTA
        $pdf->SetY(40); // Posición fija después del header

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 5, 'INFORMACIÓN DEL REPORTE', 0, 1, 'L');
        $pdf->Ln(1);

        $pdf->SetFont('helvetica', '', 8);

        $html = '<style>
        .info-table { 
            border: 2px solid #00BCD4; 
            background-color: #FFFFFF; 
        }
        .info-cell { 
            padding: 6px; 
            border-bottom: 1px solid #E0E0E0;
            color: #424242;
            font-size: 8px;
        }
        .info-label { 
            font-weight: bold; 
            color: #2D2D2D;
            background-color: #F5F5F5;
        }
        .highlight { 
            color: #D32F2F; 
            font-weight: bold; 
        }
    </style>';

        $html .= '<table cellpadding="6" class="info-table" style="width: 100%;">';

        // Fila 1
        $html .= '<tr>';
        $html .= '<td class="info-cell info-label" width="25%">Fecha de Generación:</td>';
        $html .= '<td class="info-cell" width="25%">' . date('d/m/Y H:i:s') . '</td>';
        $html .= '<td class="info-cell info-label" width="25%">Total en Base de Datos:</td>';
        $html .= '<td class="info-cell" width="25%">' . number_format(Auditoria::count()) . ' registros</td>';
        $html .= '</tr>';

        // Fila 2
        $html .= '<tr>';
        $html .= '<td class="info-cell info-label">Registros en Reporte:</td>';
        $html .= '<td class="info-cell"><span class="highlight">' . number_format($auditorias->count()) . ' registros</span></td>';
        $html .= '<td class="info-cell info-label">Generado por:</td>';
        $html .= '<td class="info-cell">' . (Auth::check() ? Auth::user()->name : 'Sistema') . '</td>';
        $html .= '</tr>';

        // Filtros aplicados
        if ($request->filled('modulo') || $request->filled('accion') || $request->filled('usuario') || $request->filled('fecha_inicio') || $request->filled('fecha_fin')) {
            $html .= '<tr>';
            $html .= '<td colspan="4" class="info-cell info-label" style="background-color: #D32F2F; color: white;">FILTROS APLICADOS</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="4" class="info-cell" style="padding: 8px;">';

            $filtros = [];
            if ($request->filled('modulo')) {
                $filtros[] = '<strong>Módulo:</strong> ' . ucfirst(str_replace('_', ' ', $request->modulo));
            }
            if ($request->filled('accion')) {
                $filtros[] = '<strong>Acción:</strong> ' . $this->getAccionTexto($request->accion);
            }
            if ($request->filled('usuario')) {
                $usuario = \App\Models\User::find($request->usuario);
                $filtros[] = '<strong>Usuario:</strong> ' . ($usuario ? $usuario->name : 'Desconocido');
            }
            if ($request->filled('fecha_inicio')) {
                $filtros[] = '<strong>Desde:</strong> ' . date('d/m/Y', strtotime($request->fecha_inicio));
            }
            if ($request->filled('fecha_fin')) {
                $filtros[] = '<strong>Hasta:</strong> ' . date('d/m/Y', strtotime($request->fecha_fin));
            }

            $html .= implode(' &nbsp;•&nbsp; ', $filtros);
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(4); // ✅ Espacio reducido
    }

    /**
     * Agregar tabla de auditorías
     */
    private function agregarTablaAuditorias($pdf, $auditorias)
    {
        // Verificar espacio disponible
        $espacioDisponible = $pdf->getPageHeight() - $pdf->GetY() - 30;

        if ($espacioDisponible < 50) {
            $pdf->AddPage();
            $pdf->setMarcaAgua();
            $pdf->SetY(40);
        }

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 5, 'DETALLE DE AUDITORÍAS', 0, 1, 'L');
        $pdf->Ln(1);

        $pdf->SetFont('helvetica', '', 7);

        $html = '<style>
        .audit-table { 
            border-collapse: collapse; 
            width: 100%; 
            border: 2px solid #212121; 
        }
        .audit-header { 
            background-color: #00BCD4;
            color: #FFFFFF; 
            font-weight: bold; 
            padding: 15px; 
            text-align: center; 
            border: 1px solid #00BCD4;
            font-size: 9px;
            text-transform: uppercase;
        }
        .audit-cell { 
            padding: 4px; 
            border: 1px solid #E0E0E0; 
            color: #424242;
            font-size: 7px;
        }
        .bg-white { background-color: #FFFFFF; }
        .bg-light { background-color: #FAFAFA; }
        .text-center { text-align: center; }
        
        /* Badges mejorados estilo Bootstrap */
        .badge { 
            display: inline-block;
            padding: 4px 10px; 
            border-radius: 12px; 
            font-size: 5px; 
            font-weight: bold; 
            text-transform: uppercase;
            letter-spacing: 0.3px;
            white-space: nowrap;
            align-items: center;
            justify-content: center;
        }
        .badge-login { 
            background-color: #00BCD4; 
            color: white;
            border: 1px solid #00ACC1;
        }
        .badge-logout { 
            background-color: #757575; 
            color: white;
            border: 1px solid #616161;
        }
        .badge-created { 
            background-color: #4CAF50; 
            color: white;
            border: 1px solid #388E3C;
        }
        .badge-updated { 
            background-color: #FF9800; 
            color: white;
            border: 1px solid #F57C00;
        }
        .badge-deleted { 
            background-color: #D32F2F; 
            color: white;
            border: 1px solid #C62828;
        }
        .badge-restored { 
            background-color: #9C27B0; 
            color: white;
            border: 1px solid #7B1FA2;
        }
    </style>';

        $html .= '<table class="audit-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th class="audit-header" style="width: 4%;">ID</th>';
        $html .= '<th class="audit-header" style="width: 12%;">Usuario</th>';
        $html .= '<th class="audit-header" style="width: 10%;">Tipo</th>'; // ✅ Más ancho para los badges
        $html .= '<th class="audit-header" style="width: 10%;">Módulo</th>';
        $html .= '<th class="audit-header" style="width: 38%;">Descripción</th>';
        $html .= '<th class="audit-header" style="width: 13%;">Dirección IP</th>';
        $html .= '<th class="audit-header" style="width: 13%;">Fecha y Hora</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $colorAlternado = true;
        foreach ($auditorias as $auditoria) {
            $bgClass = $colorAlternado ? 'bg-white' : 'bg-light';
            $badgeClass = $this->getAccionBadgeClass($auditoria->accion);

            $html .= '<tr class="' . $bgClass . '">';
            $html .= '<td class="audit-cell text-center" style="width: 4%;"><strong>' . $auditoria->id . '</strong></td>';
            $html .= '<td class="audit-cell" style="width: 12%;">' . e($auditoria->nombre_usuario ?? 'Sistema') . '</td>';
            $html .= '<td class="audit-cell text-center" style="width: 10%;"><span class="badge ' . $badgeClass . '">' . $this->getAccionTextoCorto($auditoria->accion) . '</span></td>';
            $html .= '<td class="audit-cell" style="width: 10%;">' . ucfirst(str_replace('_', ' ', $auditoria->modulo)) . '</td>';
            $html .= '<td class="audit-cell" style="width: 38%;">' . e(Str::limit($auditoria->descripcion, 80)) . '</td>';
            $html .= '<td class="audit-cell text-center" style="width: 13%; font-size: 6px;">' . ($auditoria->ip_address ?? '—') . '</td>';
            $html .= '<td class="audit-cell text-center" style="width: 13%; font-size: 6px;">' . $auditoria->created_at->format('d/m/Y') . '<br>' . $auditoria->created_at->format('H:i:s') . '</td>';
            $html .= '</tr>';

            $colorAlternado = !$colorAlternado;
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(3);
    }

    /**
     * Obtener clase de badge según acción
     */
    private function getAccionBadgeClass($accion)
    {
        $badges = [
            'login' => 'badge-login',
            'logout' => 'badge-logout',
            'created' => 'badge-created',
            'updated' => 'badge-updated',
            'deleted' => 'badge-deleted',
            'restored' => 'badge-restored',
        ];

        return $badges[$accion] ?? 'badge-login';
    }

    /**
     * Obtener texto corto de la acción
     */
    private function getAccionTextoCorto($accion)
    {
        $acciones = [
            'login' => 'LOGIN',
            'logout' => 'LOGOUT',
            'created' => 'CREAR',
            'updated' => 'EDITAR',
            'deleted' => 'ELIMINAR',
            'restored' => 'RESTAURAR',
        ];

        return $acciones[$accion] ?? strtoupper($accion);
    }

    /**
     * Obtener texto completo de la acción
     */
    private function getAccionTexto($accion)
    {
        $acciones = [
            'login' => 'Inicio de Sesión',
            'logout' => 'Cierre de Sesión',
            'created' => 'Creación de Registro',
            'updated' => 'Actualización de Registro',
            'deleted' => 'Eliminación de Registro',
            'restored' => 'Restauración de Registro',
        ];

        return $acciones[$accion] ?? ucfirst($accion);
    }



    // ========== MÉTODOS PRIVADOS PARA FORMATEAR HTML ==========

    private function getUsuarioHtml($nombre)
    {
        return '<div class="d-flex align-items-center">'
            . '<i class="ti ti-user fs-4 text-primary me-2"></i>'
            . '<span>' . e($nombre) . '</span>'
            . '</div>';
    }

    private function getModuloHtml($modulo, $icono)
    {
        $moduloFormateado = ucfirst(str_replace('_', ' ', $modulo));
        return '<div class="d-flex align-items-center">'
            . '<i class="ti ' . e($icono) . ' fs-4 text-secondary me-2"></i>'
            . '<span>' . e($moduloFormateado) . '</span>'
            . '</div>';
    }

    private function getAccionesHtml($id)
    {
        return '<a href="#" class="btn btn-default btn-icon btn-sm rounded-circle view-auditoria-btn" data-id="' . $id . '">'
            . '<i class="ti ti-eye fs-lg"></i>'
            . '</a>';
    }

    public function count(Request $request)
    {
        $query = Auditoria::query();

        if ($request->filled('modulo'))      $query->where('modulo', $request->modulo);
        if ($request->filled('accion'))      $query->where('accion', $request->accion);
        if ($request->filled('usuario'))     $query->where('user_id', $request->usuario);
        if ($request->filled('fecha_inicio')) $query->whereDate('created_at', '>=', $request->fecha_inicio);
        if ($request->filled('fecha_fin'))   $query->whereDate('created_at', '<=', $request->fecha_fin);

        return response()->json(['total' => $query->count()]);
    }
}
