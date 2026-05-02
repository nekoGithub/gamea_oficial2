<?php

namespace App\Http\Controllers;

use App\Models\Sistema;
use App\Models\SistemaVersion;
use App\Models\Ssl;
use App\Models\Servidor;
use App\Models\Credencial;
use App\Models\Auditoria;
use App\Models\Tecnologia;
use App\Services\ReportePDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ReporteController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.reportes.index')->only(['index', 'sistemas', 'ssl', 'servidores', 'credenciales']);
        $this->middleware('can:admin.reportes.generar')->only(['exportarSistemasPDF', 'exportarSslPDF', 'exportarServidoresPDF', 'exportarCredencialesPDF']);
        $this->middleware('can:admin.reportes.exportar')->only('exportarSistemasExcel');
    }
    /**
     * Dashboard principal de reportes
     */
    public function index()
    {
        // Estadísticas generales para el dashboard
        $estadisticas = [
            'total_sistemas' => Sistema::count(),
            'sistemas_activos' => Sistema::where('estado', 'activo')->count(),
            'sistemas_inactivos' => Sistema::where('estado', 'inactivo')->count(),

            'total_ssl' => Ssl::count(),
            'ssl_activos' => Ssl::where('estado', 'valido')->count(),
            'ssl_por_vencer' => Ssl::where('estado', 'proximo_vencer')->count(),
            'ssl_vencidos' => Ssl::where('estado', 'vencido')->count(),

            'total_servidores' => Servidor::count(),
            'servidores_activos' => Servidor::where('estado', 'activo')->count(),

            'total_credenciales' => Credencial::count(),
            'credenciales_activas' => Credencial::where('estado', 'activo')->count(),

            'actividad_hoy' => Auditoria::whereDate('created_at', today())->count(),
            'actividad_semana' => Auditoria::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
        ];

        return view('admin.reportes.index', compact('estadisticas'));
    }

    /**
     * Vista para Reporte de Inventario de Sistemas
     */
    public function sistemas()
    {
        $servidores = Servidor::orderBy('nombre')->get();
        $tecnologias = Tecnologia::orderBy('nombre')->get();

        return view('admin.reportes.sistemas', compact('servidores', 'tecnologias'));
    }

    /**
     * Vista para Reporte de SSL
     */
    public function ssl()
    {
        return view('admin.reportes.ssl');
    }

    /**
     * Vista para Reporte de Servidores
     */
    public function servidores()
    {
        return view('admin.reportes.servidores');
    }

    /**
     * Vista para Reporte de Credenciales
     */
    public function credenciales()
    {
        $sistemas = Sistema::orderBy('nombre')->get();
        return view('admin.reportes.credenciales', compact('sistemas'));
    }

    // ========== EXPORTACIONES PDF ==========

    /**
     * Exportar Inventario de Sistemas a PDF
     */
    public function exportarSistemasPDF(Request $request)
    {
        // ✅ CORRECCIÓN: Cargar las relaciones anidadas correctamente
        $query = Sistema::with([
            'versiones' => function ($q) {
                $q->where('es_actual', true);
            },
            'versiones.tecnologias',
            'versiones.servidores',
            'versiones.basesDatos',
            'ssl',
            'unidad'
        ]);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('servidor_id')) {
            $query->whereHas('versiones', function ($q) use ($request) {
                $q->where('es_actual', true)
                    ->whereHas('servidores', function ($subQ) use ($request) {
                        $subQ->where('servidores.id', $request->servidor_id);
                    });
            });
        }

        if ($request->filled('tecnologia_id')) {
            $query->whereHas('versiones', function ($q) use ($request) {
                $q->where('es_actual', true)
                    ->whereHas('tecnologias', function ($subQ) use ($request) {
                        $subQ->where('tecnologias.id', $request->tecnologia_id);
                    });
            });
        }

        $sistemas = $query->orderBy('nombre')->get();

        if ($sistemas->isEmpty()) {
            return redirect()->back()->with('error', 'No hay registros para exportar');
        }

        $pdf = new ReportePDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setTituloReporte('INVENTARIO DE SISTEMAS');

        $pdf->SetCreator('GAMEA - El Alto');
        $pdf->SetAuthor('Gobierno Autónomo Municipal de El Alto');
        $pdf->SetTitle('Inventario de Sistemas - GAMEA');
        $pdf->SetSubject('Reporte de Sistemas');

        $pdf->SetMargins(15, 38, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(15);
        $pdf->SetAutoPageBreak(true, 20);

        $pdf->AddPage();
        $pdf->setMarcaAgua();

        $this->agregarInfoReporteSistemas($pdf, $request, $sistemas);
        $this->agregarTablaSistemas($pdf, $sistemas);
        $this->agregarEstadisticasSistemas($pdf, $sistemas);

        $nombreArchivo = 'inventario_sistemas_' . date('Y-m-d_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            $pdf->Output('', 'I');
        }, $nombreArchivo, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Exportar SSL a PDF
     */
    public function exportarSslPDF(Request $request)
    {
        $query = Ssl::with('sistemas');

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('dias_vencimiento')) {
            $dias = $request->dias_vencimiento;
            $query->where('fecha_expiracion', '<=', now()->addDays($dias))
                ->where('fecha_expiracion', '>=', now());
        }

        $ssls = $query->orderBy('fecha_expiracion')->get();

        if ($ssls->isEmpty()) {
            return redirect()->back()->with('error', 'No hay registros para exportar');
        }

        $pdf = new ReportePDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setTituloReporte('CERTIFICADOS SSL');

        $pdf->SetCreator('GAMEA - El Alto');
        $pdf->SetAuthor('Gobierno Autónomo Municipal de El Alto');
        $pdf->SetTitle('Reporte de Certificados SSL - GAMEA');
        $pdf->SetSubject('Certificados SSL');

        $pdf->SetMargins(15, 38, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(15);
        $pdf->SetAutoPageBreak(true, 20);

        $pdf->AddPage();
        $pdf->setMarcaAgua();

        $this->agregarInfoReporteSsl($pdf, $request, $ssls);
        $this->agregarTablaSsl($pdf, $ssls);
        $this->agregarEstadisticasSsl($pdf, $ssls);

        $nombreArchivo = 'reporte_ssl_' . date('Y-m-d_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            $pdf->Output('', 'I');
        }, $nombreArchivo, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Exportar Servidores a PDF
     */
    public function exportarServidoresPDF(Request $request)
    {
        $query = Servidor::with(['sistemaOperativo', 'versiones.sistema']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $servidores = $query->orderBy('nombre')->get();

        if ($servidores->isEmpty()) {
            return redirect()->back()->with('error', 'No hay registros para exportar');
        }

        $pdf = new ReportePDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setTituloReporte('INVENTARIO DE SERVIDORES');

        $pdf->SetCreator('GAMEA - El Alto');
        $pdf->SetAuthor('Gobierno Autónomo Municipal de El Alto');
        $pdf->SetTitle('Inventario de Servidores - GAMEA');
        $pdf->SetSubject('Servidores');

        $pdf->SetMargins(15, 38, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(15);
        $pdf->SetAutoPageBreak(true, 20);

        $pdf->AddPage();
        $pdf->setMarcaAgua();

        $this->agregarInfoReporteServidores($pdf, $request, $servidores);
        $this->agregarTablaServidores($pdf, $servidores);
        $this->agregarEstadisticasServidores($pdf, $servidores);

        $nombreArchivo = 'inventario_servidores_' . date('Y-m-d_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            $pdf->Output('', 'I');
        }, $nombreArchivo, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Exportar Credenciales a PDF
     */
    public function exportarCredencialesPDF(Request $request)
    {
        $query = Credencial::with(['versiones.sistema']);

        // No hay campo 'tipo' en credenciales según tu migración
        // Si lo agregaste después, descomenta esto:
        // if ($request->filled('tipo')) {
        //     $query->where('tipo', $request->tipo);
        // }

        if ($request->filled('sistema_id')) {
            $query->whereHas('versiones.sistema', function ($q) use ($request) {
                $q->where('sistemas.id', $request->sistema_id);
            });
        }

        $credenciales = $query->orderBy('usuario')->get();

        if ($credenciales->isEmpty()) {
            return redirect()->back()->with('error', 'No hay registros para exportar');
        }

        $pdf = new ReportePDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setTituloReporte('CREDENCIALES DE ACCESO');

        $pdf->SetCreator('GAMEA - El Alto');
        $pdf->SetAuthor('Gobierno Autónomo Municipal de El Alto');
        $pdf->SetTitle('Reporte de Credenciales - GAMEA');
        $pdf->SetSubject('Credenciales de Acceso');

        $pdf->SetMargins(15, 38, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(15);
        $pdf->SetAutoPageBreak(true, 20);

        $pdf->AddPage();
        $pdf->setMarcaAgua();

        $this->agregarInfoReporteCredenciales($pdf, $request, $credenciales);
        $this->agregarTablaCredenciales($pdf, $credenciales);
        $this->agregarEstadisticasCredenciales($pdf, $credenciales);

        $nombreArchivo = 'reporte_credenciales_' . date('Y-m-d_His') . '.pdf';

        return response()->streamDownload(function () use ($pdf) {
            $pdf->Output('', 'I');
        }, $nombreArchivo, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    // ========== MÉTODOS PARA REPORTE DE SISTEMAS ==========

    private function agregarInfoReporteSistemas($pdf, $request, $sistemas)
    {
        $pdf->SetY(40);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 5, 'INFORMACIÓN DEL REPORTE', 0, 1, 'L');
        $pdf->Ln(1);

        $pdf->SetFont('helvetica', '', 8);

        $html = '<style>
        .info-table { border: 2px solid #00BCD4; background-color: #FFFFFF; }
        .info-cell { padding: 6px; border-bottom: 1px solid #E0E0E0; color: #424242; font-size: 8px; }
        .info-label { font-weight: bold; color: #2D2D2D; background-color: #F5F5F5; }
        .highlight { color: #D32F2F; font-weight: bold; }
        </style>';

        $html .= '<table cellpadding="6" class="info-table" style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<td class="info-cell info-label" width="25%">Fecha de Generación:</td>';
        $html .= '<td class="info-cell" width="25%">' . date('d/m/Y H:i:s') . '</td>';
        $html .= '<td class="info-cell info-label" width="25%">Total Sistemas:</td>';
        $html .= '<td class="info-cell" width="25%"><span class="highlight">' . number_format(Sistema::count()) . ' sistemas</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td class="info-cell info-label">Sistemas en Reporte:</td>';
        $html .= '<td class="info-cell"><span class="highlight">' . number_format($sistemas->count()) . ' sistemas</span></td>';
        $html .= '<td class="info-cell info-label">Generado por:</td>';
        $html .= '<td class="info-cell">' . (Auth::check() ? Auth::user()->name : 'Sistema') . '</td>';
        $html .= '</tr>';

        if ($request->filled('estado') || $request->filled('servidor_id') || $request->filled('tecnologia_id')) {
            $html .= '<tr>';
            $html .= '<td colspan="4" class="info-cell info-label" style="background-color: #D32F2F; color: white;">FILTROS APLICADOS</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="4" class="info-cell" style="padding: 8px;">';

            $filtros = [];
            if ($request->filled('estado')) {
                $filtros[] = '<strong>Estado:</strong> ' . ucfirst($request->estado);
            }
            if ($request->filled('servidor_id')) {
                $servidor = Servidor::find($request->servidor_id);
                $filtros[] = '<strong>Servidor:</strong> ' . ($servidor ? $servidor->nombre : 'Desconocido');
            }
            if ($request->filled('tecnologia_id')) {
                $tecnologia = Tecnologia::find($request->tecnologia_id);
                $filtros[] = '<strong>Tecnología:</strong> ' . ($tecnologia ? $tecnologia->nombre : 'Desconocido');
            }

            $html .= implode(' &nbsp;•&nbsp; ', $filtros);
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(4);
    }

    private function agregarTablaSistemas($pdf, $sistemas)
    {
        $espacioDisponible = $pdf->getPageHeight() - $pdf->GetY() - 30;
        if ($espacioDisponible < 50) {
            $pdf->AddPage();
            $pdf->setMarcaAgua();
            $pdf->SetY(40);
        }

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 5, 'DETALLE DE SISTEMAS', 0, 1, 'L');
        $pdf->Ln(1);

        $pdf->SetFont('helvetica', '', 7);

        $html = '<style>
    .sistema-table { border-collapse: collapse; width: 100%; border: 2px solid #212121; }
    .sistema-header { background-color: #00BCD4; color: #FFFFFF; font-weight: bold; padding: 8px; text-align: center; border: 1px solid #00BCD4; font-size: 8px; }
    .sistema-cell { padding: 5px; border: 1px solid #E0E0E0; color: #424242; font-size: 7px; }
    .bg-white { background-color: #FFFFFF; }
    .bg-light { background-color: #FAFAFA; }
    .text-center { text-align: center; }
    .badge { padding: 3px 8px; border-radius: 10px; font-size: 6px; font-weight: bold; }
    .badge-activo { background-color: #4CAF50; color: white; }
    .badge-inactivo { background-color: #757575; color: white; }
    </style>';

        $html .= '<table class="sistema-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th class="sistema-header" style="width: 4%;">ID</th>';
        $html .= '<th class="sistema-header" style="width: 20%;">NOMBRE</th>';
        $html .= '<th class="sistema-header" style="width: 8%;">SIGLA</th>';
        $html .= '<th class="sistema-header" style="width: 8%;">ESTADO</th>';
        $html .= '<th class="sistema-header" style="width: 15%;">DOMINIO</th>';
        $html .= '<th class="sistema-header" style="width: 15%;">SERVIDORES</th>';
        $html .= '<th class="sistema-header" style="width: 15%;">TECNOLOGÍAS</th>';
        $html .= '<th class="sistema-header" style="width: 15%;">BASES DE DATOS</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $colorAlternado = true;
        foreach ($sistemas as $sistema) {
            $bgClass = $colorAlternado ? 'bg-white' : 'bg-light';
            $badgeEstado = $this->getBadgeEstado($sistema->estado);

            // Obtener datos de la versión activa
            $version = $sistema->versiones->where('es_actual', true)->first();

            // ✅ SIN usar e() ni Str::limit() - Usar htmlspecialchars directamente
            $servidores = '—';
            $tecnologias = '—';
            $basesDatos = '—';

            if ($version) {
                if ($version->servidores->isNotEmpty()) {
                    $servidores = htmlspecialchars($version->servidores->pluck('nombre')->implode(', '), ENT_QUOTES, 'UTF-8');
                }

                if ($version->tecnologias->isNotEmpty()) {
                    $tecnologias = htmlspecialchars($version->tecnologias->pluck('nombre')->implode(', '), ENT_QUOTES, 'UTF-8');
                }

                if ($version->basesDatos->isNotEmpty()) {
                    $basesDatos = htmlspecialchars($version->basesDatos->pluck('gestor')->implode(', '), ENT_QUOTES, 'UTF-8');
                }
            }

            // Limitar longitud DESPUÉS de obtener el valor
            $servidoresTexto = strlen($servidores) > 30 ? substr($servidores, 0, 27) . '...' : $servidores;
            $tecnologiasTexto = strlen($tecnologias) > 30 ? substr($tecnologias, 0, 27) . '...' : $tecnologias;
            $basesDatosTexto = strlen($basesDatos) > 25 ? substr($basesDatos, 0, 22) . '...' : $basesDatos;

            $html .= '<tr class="' . $bgClass . '">';
            $html .= '<td class="sistema-cell text-center" style="width: 4%;"><strong>' . $sistema->id . '</strong></td>';
            $html .= '<td class="sistema-cell" style="width: 20%;">' . htmlspecialchars($sistema->nombre, ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td class="sistema-cell text-center" style="width: 8%;">' . htmlspecialchars($sistema->sigla ?? '—', ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td class="sistema-cell text-center" style="width: 8%;"><span class="badge ' . $badgeEstado . '">' . strtoupper($sistema->estado) . '</span></td>';
            $html .= '<td class="sistema-cell" style="width: 15%; font-size: 6px;">' . htmlspecialchars(strlen($sistema->dominio) > 30 ? substr($sistema->dominio, 0, 27) . '...' : $sistema->dominio, ENT_QUOTES, 'UTF-8') . '</td>';
            $html .= '<td class="sistema-cell" style="width: 15%; font-size: 6px;">' . $servidoresTexto . '</td>';
            $html .= '<td class="sistema-cell" style="width: 15%; font-size: 6px;">' . $tecnologiasTexto . '</td>';
            $html .= '<td class="sistema-cell" style="width: 15%; font-size: 6px;">' . $basesDatosTexto . '</td>';
            $html .= '</tr>';

            $colorAlternado = !$colorAlternado;
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(3);
    }

    private function agregarEstadisticasSistemas($pdf, $sistemas)
    {
        $pdf->AddPage();
        $pdf->setMarcaAgua();
        $pdf->SetY(40);

        $pdf->SetFillColor(211, 47, 47);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, '  ESTADÍSTICAS DEL INVENTARIO', 0, 1, 'L', true);
        $pdf->Ln(3);

        $total = $sistemas->count();

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 6, '1. Distribución por Estado', 0, 1, 'L');
        $pdf->Ln(1);

        $estadisticasEstado = $sistemas->groupBy('estado')->map->count()->sortDesc();

        $html = '<style>
        .stat-table { border: 1px solid #E0E0E0; width: 100%; }
        .stat-header { background-color: #00BCD4; color: #FFFFFF; font-weight: bold; padding: 8px; text-align: center; font-size: 9px; }
        .stat-cell { padding: 6px; border: 1px solid #E0E0E0; font-size: 9px; color: #424242; }
        .stat-alt { background-color: #FAFAFA; }
        .stat-percent { color: #D32F2F; font-weight: bold; }
        </style>';

        $html .= '<table class="stat-table">';
        $html .= '<thead><tr>';
        $html .= '<th class="stat-header" style="width: 50%;">ESTADO</th>';
        $html .= '<th class="stat-header" style="width: 25%;">CANTIDAD</th>';
        $html .= '<th class="stat-header" style="width: 25%;">PORCENTAJE</th>';
        $html .= '</tr></thead><tbody>';

        $rowIndex = 0;
        foreach ($estadisticasEstado as $estado => $cantidad) {
            $porcentaje = round(($cantidad / $total) * 100, 2);
            $altClass = $rowIndex % 2 != 0 ? 'stat-alt' : '';

            $html .= '<tr class="' . $altClass . '">';
            $html .= '<td class="stat-cell" style="width: 50%;">🔹 ' . ucfirst($estado) . '</td>';
            $html .= '<td class="stat-cell" style="width: 25%; text-align: center;"><strong>' . number_format($cantidad) . '</strong></td>';
            $html .= '<td class="stat-cell stat-percent" style="width: 25%; text-align: center;">' . $porcentaje . '%</td>';
            $html .= '</tr>';
            $rowIndex++;
        }

        $html .= '</tbody></table>';
        $pdf->SetFont('helvetica', '', 9);
        $pdf->writeHTML($html, true, false, true, false, '');
    }

    // ========== MÉTODOS PARA REPORTE DE SSL ==========

    private function agregarInfoReporteSsl($pdf, $request, $ssls)
    {
        $pdf->SetY(40);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 5, 'INFORMACIÓN DEL REPORTE', 0, 1, 'L');
        $pdf->Ln(1);

        $pdf->SetFont('helvetica', '', 8);

        $html = '<style>
        .info-table { border: 2px solid #00BCD4; background-color: #FFFFFF; }
        .info-cell { padding: 6px; border-bottom: 1px solid #E0E0E0; color: #424242; font-size: 8px; }
        .info-label { font-weight: bold; color: #2D2D2D; background-color: #F5F5F5; }
        .highlight { color: #D32F2F; font-weight: bold; }
        </style>';

        $html .= '<table cellpadding="6" class="info-table" style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<td class="info-cell info-label" width="25%">Fecha de Generación:</td>';
        $html .= '<td class="info-cell" width="25%">' . date('d/m/Y H:i:s') . '</td>';
        $html .= '<td class="info-cell info-label" width="25%">Total SSL:</td>';
        $html .= '<td class="info-cell" width="25%"><span class="highlight">' . number_format(Ssl::count()) . ' certificados</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td class="info-cell info-label">SSL en Reporte:</td>';
        $html .= '<td class="info-cell"><span class="highlight">' . number_format($ssls->count()) . ' certificados</span></td>';
        $html .= '<td class="info-cell info-label">Generado por:</td>';
        $html .= '<td class="info-cell">' . (Auth::check() ? Auth::user()->name : 'Sistema') . '</td>';
        $html .= '</tr>';

        if ($request->filled('estado') || $request->filled('dias_vencimiento')) {
            $html .= '<tr>';
            $html .= '<td colspan="4" class="info-cell info-label" style="background-color: #D32F2F; color: white;">FILTROS APLICADOS</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="4" class="info-cell" style="padding: 8px;">';

            $filtros = [];
            if ($request->filled('estado')) {
                $filtros[] = '<strong>Estado:</strong> ' . ucfirst($request->estado);
            }
            if ($request->filled('dias_vencimiento')) {
                $filtros[] = '<strong>Vencen en:</strong> ' . $request->dias_vencimiento . ' días';
            }

            $html .= implode(' &nbsp;•&nbsp; ', $filtros);
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(4);
    }

    private function agregarTablaSsl($pdf, $ssls)
    {
        $espacioDisponible = $pdf->getPageHeight() - $pdf->GetY() - 30;
        if ($espacioDisponible < 50) {
            $pdf->AddPage();
            $pdf->setMarcaAgua();
            $pdf->SetY(40);
        }

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 5, 'DETALLE DE CERTIFICADOS SSL', 0, 1, 'L');
        $pdf->Ln(1);

        $pdf->SetFont('helvetica', '', 7);

        $html = '<style>
        .ssl-table { border-collapse: collapse; width: 100%; border: 2px solid #212121; }
        .ssl-header { background-color: #00BCD4; color: #FFFFFF; font-weight: bold; padding: 8px; text-align: center; border: 1px solid #00BCD4; font-size: 8px; }
        .ssl-cell { padding: 5px; border: 1px solid #E0E0E0; color: #424242; font-size: 7px; }
        .bg-white { background-color: #FFFFFF; }
        .bg-light { background-color: #FAFAFA; }
        .text-center { text-align: center; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 6px; font-weight: bold; }
        .badge-valido { background-color: #4CAF50; color: white; }
        .badge-proximo-vencer { background-color: #FF9800; color: white; }
        .badge-vencido { background-color: #D32F2F; color: white; }
        </style>';

        $html .= '<table class="ssl-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th class="ssl-header" style="width: 5%;">ID</th>';
        $html .= '<th class="ssl-header" style="width: 25%;">SISTEMAS</th>';
        $html .= '<th class="ssl-header" style="width: 20%;">EMISOR</th>';
        $html .= '<th class="ssl-header" style="width: 12%;">EMISIÓN</th>';
        $html .= '<th class="ssl-header" style="width: 12%;">EXPIRACIÓN</th>';
        $html .= '<th class="ssl-header" style="width: 10%;">DÍAS REST.</th>';
        $html .= '<th class="ssl-header" style="width: 16%;">ESTADO</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $colorAlternado = true;
        foreach ($ssls as $ssl) {
            $bgClass = $colorAlternado ? 'bg-white' : 'bg-light';
            $diasRestantes = now()->diffInDays($ssl->fecha_expiracion, false);
            $badgeEstado = $this->getBadgeEstadoSsl($ssl, $diasRestantes);

            // Obtener sistemas asociados
            $sistemasNombres = $ssl->sistemas->pluck('nombre')->implode(', ');

            $html .= '<tr class="' . $bgClass . '">';
            $html .= '<td class="ssl-cell text-center" style="width: 5%;"><strong>' . $ssl->id . '</strong></td>';
            $html .= '<td class="ssl-cell" style="width: 25%; font-size: 6px;">' . e(Str::limit($sistemasNombres ?: 'Sin sistema', 50)) . '</td>';
            $html .= '<td class="ssl-cell" style="width: 20%; font-size: 6px;">' . e($ssl->emisor ?? '—') . '</td>';
            $html .= '<td class="ssl-cell text-center" style="width: 12%;">' . \Carbon\Carbon::parse($ssl->fecha_emision)->format('d/m/Y') . '</td>';
            $html .= '<td class="ssl-cell text-center" style="width: 12%;">' . \Carbon\Carbon::parse($ssl->fecha_expiracion)->format('d/m/Y') . '</td>';
            $html .= '<td class="ssl-cell text-center" style="width: 10%;"><strong>' . (int)$diasRestantes . '</strong></td>';
            $html .= '<td class="ssl-cell text-center" style="width: 16%;"><span class="' . $badgeEstado['class'] . '">' . $badgeEstado['text'] . '</span></td>';
            $html .= '</tr>';

            $colorAlternado = !$colorAlternado;
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(3);
    }

    private function agregarEstadisticasSsl($pdf, $ssls)
    {
        $pdf->AddPage();
        $pdf->setMarcaAgua();
        $pdf->SetY(40);

        $pdf->SetFillColor(211, 47, 47);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, '  ESTADÍSTICAS DE CERTIFICADOS SSL', 0, 1, 'L', true);
        $pdf->Ln(3);

        $total = $ssls->count();

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 6, '1. Estado de Certificados SSL', 0, 1, 'L');
        $pdf->Ln(1);

        $estadisticasEstado = $ssls->groupBy('estado')->map->count()->sortDesc();

        $html = '<style>
        .stat-table { border: 1px solid #E0E0E0; width: 100%; }
        .stat-header { background-color: #00BCD4; color: #FFFFFF; font-weight: bold; padding: 8px; text-align: center; font-size: 9px; }
        .stat-cell { padding: 6px; border: 1px solid #E0E0E0; font-size: 9px; color: #424242; }
        .stat-alt { background-color: #FAFAFA; }
        .stat-percent { color: #D32F2F; font-weight: bold; }
        </style>';

        $html .= '<table class="stat-table">';
        $html .= '<thead><tr>';
        $html .= '<th class="stat-header" style="width: 50%;">ESTADO</th>';
        $html .= '<th class="stat-header" style="width: 25%;">CANTIDAD</th>';
        $html .= '<th class="stat-header" style="width: 25%;">PORCENTAJE</th>';
        $html .= '</tr></thead><tbody>';

        $rowIndex = 0;
        foreach ($estadisticasEstado as $estado => $cantidad) {
            $porcentaje = $total > 0 ? round(($cantidad / $total) * 100, 2) : 0;
            $altClass = $rowIndex % 2 != 0 ? 'stat-alt' : '';

            $estadoTexto = match ($estado) {
                'valido' => '✅ Válido',
                'proximo_vencer' => '⚠️ Próximo a Vencer',
                'vencido' => '❌ Vencido',
                default => ucfirst($estado)
            };

            $html .= '<tr class="' . $altClass . '">';
            $html .= '<td class="stat-cell" style="width: 50%;">' . $estadoTexto . '</td>';
            $html .= '<td class="stat-cell" style="width: 25%; text-align: center;"><strong>' . number_format($cantidad) . '</strong></td>';
            $html .= '<td class="stat-cell stat-percent" style="width: 25%; text-align: center;">' . $porcentaje . '%</td>';
            $html .= '</tr>';
            $rowIndex++;
        }

        $html .= '</tbody></table>';
        $pdf->SetFont('helvetica', '', 9);
        $pdf->writeHTML($html, true, false, true, false, '');
    }

    // ========== MÉTODOS PARA REPORTE DE SERVIDORES ==========

    private function agregarInfoReporteServidores($pdf, $request, $servidores)
    {
        $pdf->SetY(40);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 5, 'INFORMACIÓN DEL REPORTE', 0, 1, 'L');
        $pdf->Ln(1);

        $pdf->SetFont('helvetica', '', 8);

        $html = '<style>
        .info-table { border: 2px solid #00BCD4; background-color: #FFFFFF; }
        .info-cell { padding: 6px; border-bottom: 1px solid #E0E0E0; color: #424242; font-size: 8px; }
        .info-label { font-weight: bold; color: #2D2D2D; background-color: #F5F5F5; }
        .highlight { color: #D32F2F; font-weight: bold; }
        </style>';

        $html .= '<table cellpadding="6" class="info-table" style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<td class="info-cell info-label" width="25%">Fecha de Generación:</td>';
        $html .= '<td class="info-cell" width="25%">' . date('d/m/Y H:i:s') . '</td>';
        $html .= '<td class="info-cell info-label" width="25%">Total Servidores:</td>';
        $html .= '<td class="info-cell" width="25%"><span class="highlight">' . number_format(Servidor::count()) . ' servidores</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td class="info-cell info-label">Servidores en Reporte:</td>';
        $html .= '<td class="info-cell"><span class="highlight">' . number_format($servidores->count()) . ' servidores</span></td>';
        $html .= '<td class="info-cell info-label">Generado por:</td>';
        $html .= '<td class="info-cell">' . (Auth::check() ? Auth::user()->name : 'Sistema') . '</td>';
        $html .= '</tr>';

        if ($request->filled('estado')) {
            $html .= '<tr>';
            $html .= '<td colspan="4" class="info-cell info-label" style="background-color: #D32F2F; color: white;">FILTROS APLICADOS</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="4" class="info-cell" style="padding: 8px;"><strong>Estado:</strong> ' . ucfirst($request->estado) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(4);
    }

    private function agregarTablaServidores($pdf, $servidores)
    {
        $espacioDisponible = $pdf->getPageHeight() - $pdf->GetY() - 30;
        if ($espacioDisponible < 50) {
            $pdf->AddPage();
            $pdf->setMarcaAgua();
            $pdf->SetY(40);
        }

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 5, 'DETALLE DE SERVIDORES', 0, 1, 'L');
        $pdf->Ln(1);

        $pdf->SetFont('helvetica', '', 7);

        $html = '<style>
        .servidor-table { border-collapse: collapse; width: 100%; border: 2px solid #212121; }
        .servidor-header { background-color: #00BCD4; color: #FFFFFF; font-weight: bold; padding: 8px; text-align: center; border: 1px solid #00BCD4; font-size: 8px; }
        .servidor-cell { padding: 5px; border: 1px solid #E0E0E0; color: #424242; font-size: 7px; }
        .bg-white { background-color: #FFFFFF; }
        .bg-light { background-color: #FAFAFA; }
        .text-center { text-align: center; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 6px; font-weight: bold; }
        .badge-activo { background-color: #4CAF50; color: white; }
        .badge-inactivo { background-color: #757575; color: white; }
        .badge-fisico { background-color: #2196F3; color: white; }
        .badge-virtual { background-color: #9C27B0; color: white; }
        </style>';

        $html .= '<table class="servidor-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th class="servidor-header" style="width: 4%;">ID</th>';
        $html .= '<th class="servidor-header" style="width: 16%;">NOMBRE</th>';
        $html .= '<th class="servidor-header" style="width: 8%;">ESTADO</th>';
        $html .= '<th class="servidor-header" style="width: 8%;">TIPO</th>';
        $html .= '<th class="servidor-header" style="width: 12%;">IP INTERNA</th>';
        $html .= '<th class="servidor-header" style="width: 12%;">IP EXTERNA</th>';
        $html .= '<th class="servidor-header" style="width: 18%;">SISTEMA OPERATIVO</th>';
        $html .= '<th class="servidor-header" style="width: 8%;">MAC</th>';
        $html .= '<th class="servidor-header" style="width: 7%;">SISTEMAS</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $colorAlternado = true;
        foreach ($servidores as $servidor) {
            $bgClass = $colorAlternado ? 'bg-white' : 'bg-light';
            $badgeEstado = $this->getBadgeEstado($servidor->estado);
            $badgeTipo = $servidor->tipo_servidor === 'físico' ? 'badge-fisico' : 'badge-virtual';

            $cantidadSistemas = $servidor->versiones()
                ->distinct('sistema_id')
                ->count('sistema_id');

            $sistemaOperativo = $servidor->sistemaOperativo
                ? $servidor->sistemaOperativo->nombre . ' ' . $servidor->sistemaOperativo->version
                : '—';

            $html .= '<tr class="' . $bgClass . '">';
            $html .= '<td class="servidor-cell text-center" style="width: 4%;"><strong>' . $servidor->id . '</strong></td>';
            $html .= '<td class="servidor-cell" style="width: 16%;">' . e($servidor->nombre) . '</td>';
            $html .= '<td class="servidor-cell text-center" style="width: 8%;"><span class="badge ' . $badgeEstado . '">' . strtoupper($servidor->estado) . '</span></td>';
            $html .= '<td class="servidor-cell text-center" style="width: 8%;"><span class="badge ' . $badgeTipo . '">' . strtoupper($servidor->tipo_servidor) . '</span></td>';
            $html .= '<td class="servidor-cell text-center" style="width: 12%; font-size: 6px;">' . e($servidor->ip_interna) . '</td>';
            $html .= '<td class="servidor-cell text-center" style="width: 12%; font-size: 6px;">' . e($servidor->ip_externa ?? '—') . '</td>';
            $html .= '<td class="servidor-cell" style="width: 18%; font-size: 6px;">' . e($sistemaOperativo) . '</td>';
            $html .= '<td class="servidor-cell text-center" style="width: 8%; font-size: 6px;">' . e(Str::limit($servidor->mac_address ?? '—', 15)) . '</td>';
            $html .= '<td class="servidor-cell text-center" style="width: 7%;"><strong>' . $cantidadSistemas . '</strong></td>';
            $html .= '</tr>';

            $colorAlternado = !$colorAlternado;
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(3);
    }

    private function agregarEstadisticasServidores($pdf, $servidores)
    {
        $pdf->AddPage();
        $pdf->setMarcaAgua();
        $pdf->SetY(40);

        $pdf->SetFillColor(211, 47, 47);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, '  ESTADÍSTICAS DE SERVIDORES', 0, 1, 'L', true);
        $pdf->Ln(3);

        $total = $servidores->count();

        // ========== SOLO DISTRIBUCIÓN POR ESTADO ==========
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 6, 'Distribución por Estado', 0, 1, 'L');
        $pdf->Ln(1);

        $estadisticasEstado = $servidores->groupBy('estado')->map->count()->sortDesc();

        $html = '<style>
    .stat-table { border: 1px solid #E0E0E0; width: 100%; }
    .stat-header { background-color: #00BCD4; color: #FFFFFF; font-weight: bold; padding: 8px; text-align: center; font-size: 9px; border: 1px solid #00BCD4; }
    .stat-cell { padding: 6px; border: 1px solid #E0E0E0; font-size: 9px; color: #424242; }
    .stat-alt { background-color: #FAFAFA; }
    .stat-percent { color: #D32F2F; font-weight: bold; }
    </style>';

        $html .= '<table class="stat-table">';
        $html .= '<thead><tr>';
        $html .= '<th class="stat-header" style="width: 50%;">ESTADO</th>';
        $html .= '<th class="stat-header" style="width: 25%;">CANTIDAD</th>';
        $html .= '<th class="stat-header" style="width: 25%;">PORCENTAJE</th>';
        $html .= '</tr></thead><tbody>';

        $rowIndex = 0;
        foreach ($estadisticasEstado as $estado => $cantidad) {
            $porcentaje = round(($cantidad / $total) * 100, 2);
            $altClass = $rowIndex % 2 != 0 ? 'stat-alt' : '';

            $html .= '<tr class="' . $altClass . '">';
            $html .= '<td class="stat-cell" style="width: 50%;">' . ucfirst($estado) . '</td>';
            $html .= '<td class="stat-cell" style="width: 25%; text-align: center;"><strong>' . number_format($cantidad) . '</strong></td>';
            $html .= '<td class="stat-cell stat-percent" style="width: 25%; text-align: center;">' . $porcentaje . '%</td>';
            $html .= '</tr>';
            $rowIndex++;
        }

        $html .= '</tbody></table>';
        $pdf->SetFont('helvetica', '', 9);
        $pdf->writeHTML($html, true, false, true, false, '');
    }

    // ========== MÉTODOS PARA REPORTE DE CREDENCIALES ==========

    private function agregarInfoReporteCredenciales($pdf, $request, $credenciales)
    {
        $pdf->SetY(40);
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 5, 'INFORMACIÓN DEL REPORTE', 0, 1, 'L');
        $pdf->Ln(1);

        $pdf->SetFont('helvetica', '', 8);

        $html = '<style>
        .info-table { border: 2px solid #00BCD4; background-color: #FFFFFF; }
        .info-cell { padding: 6px; border-bottom: 1px solid #E0E0E0; color: #424242; font-size: 8px; }
        .info-label { font-weight: bold; color: #2D2D2D; background-color: #F5F5F5; }
        .highlight { color: #D32F2F; font-weight: bold; }
        .warning { background-color: #FFF3CD; color: #856404; padding: 8px; border: 1px solid #FFC107; }
        </style>';

        $html .= '<table cellpadding="6" class="info-table" style="width: 100%;">';
        $html .= '<tr>';
        $html .= '<td class="info-cell info-label" width="25%">Fecha de Generación:</td>';
        $html .= '<td class="info-cell" width="25%">' . date('d/m/Y H:i:s') . '</td>';
        $html .= '<td class="info-cell info-label" width="25%">Total Credenciales:</td>';
        $html .= '<td class="info-cell" width="25%"><span class="highlight">' . number_format(Credencial::count()) . ' credenciales</span></td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td class="info-cell info-label">Credenciales en Reporte:</td>';
        $html .= '<td class="info-cell"><span class="highlight">' . number_format($credenciales->count()) . ' credenciales</span></td>';
        $html .= '<td class="info-cell info-label">Generado por:</td>';
        $html .= '<td class="info-cell">' . (Auth::check() ? Auth::user()->name : 'Sistema') . '</td>';
        $html .= '</tr>';

        $html .= '<tr>';
        $html .= '<td colspan="4" class="warning">🔒 <strong>ADVERTENCIA DE SEGURIDAD:</strong> Este reporte contiene información sensible. Las contraseñas han sido ocultadas por seguridad. Mantenga este documento en lugar seguro.</td>';
        $html .= '</tr>';

        if ($request->filled('sistema_id')) {
            $html .= '<tr>';
            $html .= '<td colspan="4" class="info-cell info-label" style="background-color: #D32F2F; color: white;">FILTROS APLICADOS</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td colspan="4" class="info-cell" style="padding: 8px;">';

            $sistema = Sistema::find($request->sistema_id);
            $html .= '<strong>Sistema:</strong> ' . ($sistema ? $sistema->nombre : 'Desconocido');

            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(4);
    }

    private function agregarTablaCredenciales($pdf, $credenciales)
    {
        $espacioDisponible = $pdf->getPageHeight() - $pdf->GetY() - 30;
        if ($espacioDisponible < 50) {
            $pdf->AddPage();
            $pdf->setMarcaAgua();
            $pdf->SetY(40);
        }

        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 5, 'DETALLE DE CREDENCIALES', 0, 1, 'L');
        $pdf->Ln(1);

        $pdf->SetFont('helvetica', '', 12);

        $html = '<style>
        .credencial-table { border-collapse: collapse; width: 100%; border: 2px solid #212121; }
        .credencial-header { background-color: #00BCD4; color: #FFFFFF; font-weight: bold; padding: 10px; text-align: center; border: 1px solid #00BCD4; font-size: 10px; }
        .credencial-cell { padding: 7px; border: 1px solid #E0E0E0; color: #424242; font-size: 9px; }
        .bg-white { background-color: #FFFFFF; }
        .bg-light { background-color: #FAFAFA; }
        .text-center { text-align: center; }
        .badge { padding: 3px 8px; border-radius: 10px; font-size: 6px; font-weight: bold; }
        .badge-activo { background-color: #4CAF50; color: white; }
        .badge-inactivo { background-color: #757575; color: white; }
        </style>';

        $html .= '<table class="credencial-table">';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th class="credencial-header" style="width: 5%;">ID</th>';
        $html .= '<th class="credencial-header" style="width: 30%;">SISTEMAS</th>';
        $html .= '<th class="credencial-header" style="width: 15%;">USUARIO</th>';
        $html .= '<th class="credencial-header" style="width: 12%;">CONTRASEÑA</th>';
        $html .= '<th class="credencial-header" style="width: 23%;">URL ACCESO</th>';
        $html .= '<th class="credencial-header" style="width: 8%;">ESTADO</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        $colorAlternado = true;
        foreach ($credenciales as $credencial) {
            $bgClass = $colorAlternado ? 'bg-white' : 'bg-light';
            $badgeEstado = $this->getBadgeEstado($credencial->estado);

            $sistemas = $credencial->versiones()
                ->with('sistema')
                ->get()
                ->pluck('sistema.nombre')
                ->unique()
                ->implode(', ');

            $html .= '<tr class="' . $bgClass . '">';
            $html .= '<td class="credencial-cell text-center" style="width: 5%;"><strong>' . $credencial->id . '</strong></td>';
            $html .= '<td class="credencial-cell" style="width: 30%; font-size: 6px;">' . e(Str::limit($sistemas ?: 'Sin sistema', 60)) . '</td>';
            $html .= '<td class="credencial-cell" style="width: 15%;">' . e($credencial->usuario) . '</td>';
            $html .= '<td class="credencial-cell text-center" style="width: 12%; font-family: monospace;">••••••••</td>';
            $html .= '<td class="credencial-cell" style="width: 23%; font-size: 6px;">' . e(Str::limit($credencial->url_acceso, 45)) . '</td>';
            $html .= '<td class="credencial-cell text-center" style="width: 8%;"><span class="badge ' . $badgeEstado . '">' . strtoupper($credencial->estado) . '</span></td>';
            $html .= '</tr>';

            $colorAlternado = !$colorAlternado;
        }

        $html .= '</tbody>';
        $html .= '</table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Ln(3);
    }

    private function agregarEstadisticasCredenciales($pdf, $credenciales)
    {
        $pdf->AddPage();
        $pdf->setMarcaAgua();
        $pdf->SetY(40);

        $pdf->SetFillColor(211, 47, 47);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, '  ESTADÍSTICAS DE CREDENCIALES', 0, 1, 'L', true);
        $pdf->Ln(3);

        $total = $credenciales->count();

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->SetTextColor(45, 45, 45);
        $pdf->Cell(0, 6, '1. Distribución por Estado', 0, 1, 'L');
        $pdf->Ln(1);

        $estadisticasEstado = $credenciales->groupBy('estado')->map->count()->sortDesc();

        $html = '<style>
        .stat-table { border: 1px solid #E0E0E0; width: 100%; }
        .stat-header { background-color: #00BCD4; color: #FFFFFF; font-weight: bold; padding: 8px; text-align: center; font-size: 9px; }
        .stat-cell { padding: 6px; border: 1px solid #E0E0E0; font-size: 9px; color: #424242; }
        .stat-alt { background-color: #FAFAFA; }
        .stat-percent { color: #D32F2F; font-weight: bold; }
        </style>';

        $html .= '<table class="stat-table">';
        $html .= '<thead><tr>';
        $html .= '<th class="stat-header" style="width: 50%;">ESTADO</th>';
        $html .= '<th class="stat-header" style="width: 25%;">CANTIDAD</th>';
        $html .= '<th class="stat-header" style="width: 25%;">PORCENTAJE</th>';
        $html .= '</tr></thead><tbody>';

        $rowIndex = 0;
        foreach ($estadisticasEstado as $estado => $cantidad) {
            $porcentaje = round(($cantidad / $total) * 100, 2);
            $altClass = $rowIndex % 2 != 0 ? 'stat-alt' : '';

            $html .= '<tr class="' . $altClass . '">';
            $html .= '<td class="stat-cell" style="width: 50%;">🔑 ' . ucfirst($estado) . '</td>';
            $html .= '<td class="stat-cell" style="width: 25%; text-align: center;"><strong>' . number_format($cantidad) . '</strong></td>';
            $html .= '<td class="stat-cell stat-percent" style="width: 25%; text-align: center;">' . $porcentaje . '%</td>';
            $html .= '</tr>';
            $rowIndex++;
        }

        $html .= '</tbody></table>';
        $pdf->SetFont('helvetica', '', 9);
        $pdf->writeHTML($html, true, false, true, false, '');
    }

    // ========== MÉTODOS AUXILIARES ==========

    private function getBadgeEstado($estado)
    {
        $badges = [
            'activo' => 'badge badge-activo',
            'inactivo' => 'badge badge-inactivo',
        ];

        return $badges[$estado] ?? 'badge badge-inactivo';
    }

    private function getBadgeEstadoSsl($ssl, $diasRestantes)
    {
        if ($ssl->estado === 'vencido') {
            return ['class' => 'badge badge-vencido', 'text' => 'VENCIDO'];
        }

        if ($ssl->estado === 'proximo_vencer') {
            return ['class' => 'badge badge-proximo-vencer', 'text' => 'POR VENCER'];
        }

        return ['class' => 'badge badge-valido', 'text' => 'VÁLIDO'];
    }

    private function getBadgeTipoCredencial($tipo)
    {
        $badges = [
            'admin' => 'badge badge-admin',
            'usuario' => 'badge badge-usuario',
            'bd' => 'badge badge-bd',
            'api' => 'badge badge-api',
        ];

        return $badges[$tipo] ?? 'badge badge-usuario';
    }

    // reporete de excel de los sistemas 
    public function exportarSistemasExcel(Request $request)
    {
        // ✅ Consulta optimizada sin duplicados
        $query = Sistema::with([
            'versiones' => function ($q) {
                $q->where('es_actual', true)
                    ->with(['tecnologias', 'servidores.sistemaOperativo', 'basesDatos']);
            },
            'ssl',
            'unidad'
        ]);

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('servidor_id')) {
            $query->whereHas('versiones', function ($q) use ($request) {
                $q->where('es_actual', true)
                    ->whereHas('servidores', function ($subQ) use ($request) {
                        $subQ->where('servidores.id', $request->servidor_id);
                    });
            });
        }

        if ($request->filled('tecnologia_id')) {
            $query->whereHas('versiones', function ($q) use ($request) {
                $q->where('es_actual', true)
                    ->whereHas('tecnologias', function ($subQ) use ($request) {
                        $subQ->where('tecnologias.id', $request->tecnologia_id);
                    });
            });
        }

        $sistemas = $query->orderBy('nombre')->get()->unique('id');

        if ($sistemas->isEmpty()) {
            return redirect()->back()->with('error', 'No hay registros para exportar');
        }

        // Crear Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventario Completo');

        // Configurar propiedades
        $spreadsheet->getProperties()
            ->setCreator('GAMEA - El Alto')
            ->setTitle('Inventario de Sistemas')
            ->setSubject('Reporte de Sistemas');

        // ========== SECCIÓN 1: ENCABEZADO DEL DOCUMENTO ==========
        $row = 1;

        // Logo y título principal
        $sheet->setCellValue("A{$row}", 'GOBIERNO AUTÓNOMO MUNICIPAL DE EL ALTO');
        $sheet->mergeCells("A{$row}:I{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("A{$row}", 'GAMEA - Gestión de Sistemas');
        $sheet->mergeCells("A{$row}:I{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setSize(12);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;

        $sheet->setCellValue("A{$row}", 'INVENTARIO DE SISTEMAS');
        $sheet->mergeCells("A{$row}:I{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(18)->getColor()->setRGB('D32F2F');
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $row++;
        $row++; // Espacio

        // ========== SECCIÓN 2: INFORMACIÓN DEL REPORTE ==========
        $sheet->setCellValue("A{$row}", 'Fecha de Generación:');
        $sheet->setCellValue("B{$row}", now()->format('d/m/Y H:i:s'));
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue("A{$row}", 'Total de Sistemas:');
        $sheet->setCellValue("B{$row}", $sistemas->count());
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $row++;

        $sheet->setCellValue("A{$row}", 'Generado por:');
        $sheet->setCellValue("B{$row}", Auth::check() ? Auth::user()->name : 'Sistema');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true);
        $row++;

        // Filtros aplicados
        if ($request->filled('estado') || $request->filled('servidor_id') || $request->filled('tecnologia_id')) {
            $row++;
            $sheet->setCellValue("A{$row}", 'FILTROS APLICADOS');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
            $row++;

            if ($request->filled('estado')) {
                $sheet->setCellValue("A{$row}", 'Estado:');
                $sheet->setCellValue("B{$row}", ucfirst($request->estado));
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $row++;
            }

            if ($request->filled('servidor_id')) {
                $servidor = \App\Models\Servidor::find($request->servidor_id);
                $sheet->setCellValue("A{$row}", 'Servidor:');
                $sheet->setCellValue("B{$row}", $servidor ? $servidor->nombre : 'N/A');
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $row++;
            }

            if ($request->filled('tecnologia_id')) {
                $tecnologia = \App\Models\Tecnologia::find($request->tecnologia_id);
                $sheet->setCellValue("A{$row}", 'Tecnología:');
                $sheet->setCellValue("B{$row}", $tecnologia ? $tecnologia->nombre : 'N/A');
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $row++;
            }
        }

        $row++; // Espacio antes de la tabla

        // ========== SECCIÓN 3: TABLA DE INVENTARIO ==========
        $headerRow = $row;

        // Encabezados
        $headers = [
            'A' => 'ID',
            'B' => 'SISTEMA',
            'C' => 'ESTADO',
            'D' => 'URL',
            'E' => 'SERVIDOR',
            'F' => 'TECNOLOGÍAS',
            'G' => 'SISTEMA OPERATIVO',
            'H' => 'BASE DE DATOS',
            'I' => 'UNIDAD'
        ];

        foreach ($headers as $col => $header) {
            $sheet->setCellValue("{$col}{$row}", $header);
        }

        // Estilo del encabezado
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D32F2F'] // Rojo GAMEA
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ];

        $sheet->getStyle("A{$row}:I{$row}")->applyFromArray($headerStyle);
        $row++;

        // ✅ DATOS DE SISTEMAS SIN DUPLICADOS
        foreach ($sistemas as $sistema) {
            $versionActual = $sistema->versiones->where('es_actual', true)->first();

            // ID
            $sheet->setCellValue("A{$row}", $sistema->id);

            // Sistema
            $sheet->setCellValue("B{$row}", $sistema->nombre);

            // Estado
            $estado = ucfirst($sistema->estado);
            $sheet->setCellValue("C{$row}", $estado);

            // Colorear según estado
            if ($sistema->estado === 'activo') {
                $sheet->getStyle("C{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('C8E6C9');
            } else {
                $sheet->getStyle("C{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFCDD2');
            }

            // URL - Usar dominio si url está vacío
            $url = $sistema->url ?? $sistema->dominio ?? 'N/A';
            $sheet->setCellValue("D{$row}", $url);

            // ✅ SERVIDORES - Agrupados
            $servidores = $versionActual && $versionActual->servidores->isNotEmpty()
                ? $versionActual->servidores->pluck('nombre')->unique()->join(', ')
                : 'N/A';
            $sheet->setCellValue("E{$row}", $servidores);

            // ✅ TECNOLOGÍAS - Agrupadas
            $tecnologias = $versionActual && $versionActual->tecnologias->isNotEmpty()
                ? $versionActual->tecnologias->pluck('nombre')->unique()->join(', ')
                : 'N/A';
            $sheet->setCellValue("F{$row}", $tecnologias);

            // ✅ SISTEMA OPERATIVO
            $sistemaOperativo = 'N/A';
            if ($versionActual && $versionActual->servidores->isNotEmpty()) {
                $servidor = $versionActual->servidores->first();
                if ($servidor->sistemaOperativo) {
                    $sistemaOperativo = $servidor->sistemaOperativo->nombre . ' ' . $servidor->sistemaOperativo->version;
                }
            }
            $sheet->setCellValue("G{$row}", $sistemaOperativo);

            // ✅ BASE DE DATOS - Agrupadas
            $basesDatos = $versionActual && $versionActual->basesDatos->isNotEmpty()
                ? $versionActual->basesDatos->map(function ($bd) {
                    return $bd->gestor . ' ' . ($bd->version ?? '');
                })->unique()->join(', ')
                : 'N/A';
            $sheet->setCellValue("H{$row}", $basesDatos);

            // Unidad
            $unidad = $sistema->unidad ? $sistema->unidad->nombre : 'N/A';
            $sheet->setCellValue("I{$row}", $unidad);

            // Bordes
            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC']
                    ]
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true
                ]
            ]);

            $row++;
        }

        $lastDataRow = $row - 1;

        // ========== SECCIÓN 4: ESTADÍSTICAS (DEBAJO DE LA TABLA) ==========
        $row += 2; // Espacio

        $sheet->setCellValue("A{$row}", 'ESTADÍSTICAS DEL INVENTARIO');
        $sheet->mergeCells("A{$row}:C{$row}");
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("A{$row}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('26C6DA'); // Cyan GAMEA
        $row++;
        $row++;

        // Por estado
        $sheet->setCellValue("A{$row}", 'DISTRIBUCIÓN POR ESTADO');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setSize(12);
        $row++;

        $sheet->setCellValue("A{$row}", 'Estado');
        $sheet->setCellValue("B{$row}", 'Cantidad');
        $sheet->setCellValue("C{$row}", 'Porcentaje');
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E0E0E0']
            ]
        ]);
        $row++;

        $activosCount = $sistemas->where('estado', 'activo')->count();
        $inactivosCount = $sistemas->where('estado', 'inactivo')->count();
        $total = $sistemas->count();

        $sheet->setCellValue("A{$row}", 'Activo');
        $sheet->setCellValue("B{$row}", $activosCount);
        $sheet->setCellValue("C{$row}", $total > 0 ? number_format(($activosCount / $total) * 100, 1) . '%' : '0%');
        $row++;

        $sheet->setCellValue("A{$row}", 'Inactivo');
        $sheet->setCellValue("B{$row}", $inactivosCount);
        $sheet->setCellValue("C{$row}", $total > 0 ? number_format(($inactivosCount / $total) * 100, 1) . '%' : '0%');
        $row++;

        // Ajustar anchos de columna
        $sheet->getColumnDimension('A')->setWidth(10);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(30);
        $sheet->getColumnDimension('E')->setWidth(25);
        $sheet->getColumnDimension('F')->setWidth(30);
        $sheet->getColumnDimension('G')->setWidth(25);
        $sheet->getColumnDimension('H')->setWidth(25);
        $sheet->getColumnDimension('I')->setWidth(30);

        // Ajustar altura de filas de datos
        for ($i = $headerRow + 1; $i <= $lastDataRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1);
        }

        // Generar archivo
        $writer = new Xlsx($spreadsheet);
        $nombreArchivo = 'gestion_sistemas_' . date('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
