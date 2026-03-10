@extends('layouts.vertical', ['title' => 'Dashboard GAMEA'])

@section('css')
    @vite(['node_modules/jsvectormap/dist/jsvectormap.min.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', [
        'subtitle' => 'Sistema de Gestión',
        'title' => 'Panel de Control',
    ])

    {{-- ========== FILA 1: TARJETAS DE MÉTRICAS ========== --}}
    <div class="row row-cols-xxl-4 row-cols-md-2 row-cols-1 g-3 align-items-center">

        {{-- Tarjeta: Sistemas (MEJORADA) --}}
        <div class="col">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="avatar avatar-lg flex-shrink-0" style="width: 60px; height: 60px;">
                            <span class="avatar-title bg-primary-subtle text-primary rounded-3 fs-20"
                                style="display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-server"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h3 class="mb-0 fw-bold">{{ $totalSistemas }}</h3>
                            <p class="mb-0 text-muted fs-sm">Total Sistemas</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted fs-xs fw-semibold text-uppercase">Activos</span>
                            <span class="text-primary fw-semibold">{{ $porcentajeSistemasActivos }}%</span>
                        </div>
                        <div class="progress" style="height: 8px; border-radius: 10px;">
                            <div class="progress-bar bg-primary"
                                style="width: {{ $porcentajeSistemasActivos }}%; border-radius: 10px;"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-success"><i class="ti ti-arrow-up"></i> {{ $sistemasActivos }}
                                Activos</small>
                            <small class="text-muted">{{ $sistemasInactivos }} Inactivos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta: SSL --}}
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar avatar-lg flex-shrink-0">
                            <span class="avatar-title bg-info-subtle text-info rounded fs-24">
                                <i class="ti ti-lock"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h4 class="mb-0">{{ $totalSsl }}</h4>
                            <p class="mb-0 text-muted">Certificados SSL</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted fs-xs fw-semibold">VÁLIDOS</span>
                            <span class="text-muted">{{ $porcentajeSslValidos }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" style="width: {{ $porcentajeSslValidos }}%;"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-warning">⚠ {{ $sslPorVencer }} Por vencer</small>
                            <small class="text-danger">✗ {{ $sslVencidos }} Vencidos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta: Servidores --}}
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar avatar-lg flex-shrink-0">
                            <span class="avatar-title bg-success-subtle text-success rounded fs-24">
                                <i class="ti ti-database"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h4 class="mb-0">{{ $totalServidores }}</h4>
                            <p class="mb-0 text-muted">Servidores</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted fs-xs fw-semibold">OPERATIVOS</span>
                            <span class="text-muted">{{ $porcentajeServidoresActivos }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" style="width: {{ $porcentajeServidoresActivos }}%;"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-primary">{{ $servidoresFisicos }} Físicos</small>
                            <small class="text-secondary">{{ $servidoresVirtuales }} Virtuales</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tarjeta: Tecnologías --}}
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar avatar-lg flex-shrink-0">
                            <span class="avatar-title bg-warning-subtle text-warning rounded fs-24">
                                <i class="ti ti-code"></i>
                            </span>
                        </div>
                        <div class="text-end">
                            <h4 class="mb-0">{{ $totalTecnologias }}</h4>
                            <p class="mb-0 text-muted">Tecnologías</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted fs-xs fw-semibold">EN USO</span>
                            <span class="text-muted">100%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-warning" style="width: 100%;"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-primary">{{ $tecnologiasBackend }} Backend</small>
                            <small class="text-info">{{ $tecnologiasFrontend }} Frontend</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== FILA 2: GRÁFICOS PRINCIPALES ========== --}}
    <div class="row mt-3">

        {{-- Gráfico: Auditorías por Día --}}
        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header border-dashed card-tabs d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="card-title">Actividad del Sistema</h4>
                    </div>
                    <span class="badge bg-primary-subtle text-primary">Últimos 30 días</span>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div class="apex-charts" id="auditoria-chart"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Gráfico: Sistemas por Tecnología --}}
        <div class="col-xxl-6">
            <div class="card">
                <div class="card-header border-dashed card-tabs d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h4 class="card-title">Distribución de Tecnologías</h4>
                    </div>
                    <span class="badge bg-info-subtle text-info">Top 10</span>
                </div>
                <div class="card-body">
                    <div dir="ltr">
                        <div class="apex-charts" id="tecnologias-chart"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ========== FILA 3: INFORMACIÓN DETALLADA ========== --}}
    <div class="row mt-3">

        {{-- Alertas Críticas: SSL por vencer --}}
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="ti ti-alert-triangle text-warning me-1"></i>
                        Alertas SSL
                    </h4>
                </div>
                <div class="card-body py-0" data-simplebar style="height: 380px;">
                    @forelse($sslPorVencerProximos as $ssl)
                        @php
                            // Determinar si está vencido o próximo a vencer
                            $estaVencido = $ssl->estado === 'vencido';

                            // Calcular días restantes como ENTERO (sin decimales)
                            $diasRestantes = (int) now()->diffInDays(
                                \Carbon\Carbon::parse($ssl->fecha_expiracion),
                                false,
                            );

                            // Texto de tiempo restante en español
                            if ($estaVencido || $diasRestantes < 0) {
                                $tiempoTexto = 'Vencido';
                                $tiempoColor = 'danger';
                            } else {
                                if ($diasRestantes == 0) {
                                    $tiempoTexto = 'Vence hoy';
                                    $tiempoColor = 'danger';
                                } elseif ($diasRestantes == 1) {
                                    $tiempoTexto = 'Vence mañana';
                                    $tiempoColor = 'danger';
                                } else {
                                    $tiempoTexto = 'En ' . $diasRestantes . ' días';
                                    $tiempoColor = $diasRestantes <= 7 ? 'danger' : 'warning';
                                }
                            }
                        @endphp

                        <div class="d-flex align-items-start gap-2 my-3 border-bottom pb-3">
                            <div class="avatar avatar-sm flex-shrink-0">
                                <span
                                    class="avatar-title bg-{{ $estaVencido || $diasRestantes < 0 ? 'danger' : 'warning' }}-subtle text-{{ $estaVencido || $diasRestantes < 0 ? 'danger' : 'warning' }} rounded">
                                    <i class="ti ti-{{ $estaVencido || $diasRestantes < 0 ? 'x' : 'clock' }}"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1">{{ $ssl->sistemas->first()->nombre ?? 'Sin sistema' }}</h6>
                                <p class="mb-1 text-muted fs-xs">{{ $ssl->emisor ?? 'Emisor desconocido' }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="ti ti-calendar"></i>
                                        Vence:
                                        {{ \Carbon\Carbon::parse($ssl->fecha_expiracion)->locale('es')->isoFormat('D [de] MMM [de] YYYY') }}
                                    </small>
                                    <span class="badge bg-{{ $tiempoColor }}-subtle text-{{ $tiempoColor }} fs-xxs">
                                        {{ $tiempoTexto }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="ti ti-shield-check text-success" style="font-size: 48px;"></i>
                            <p class="text-muted mt-2">No hay SSL por vencer próximamente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Top Servidores --}}
        <div class="col-xxl-4 col-lg-6">
            <div class="card">
                <div class="card-header justify-content-between align-items-center">
                    <h5 class="card-title">
                        <i class="ti ti-trophy text-warning me-1"></i>
                        Top Servidores
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-custom table-nowrap table-hover table-centered mb-0">
                            <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                                <tr class="text-uppercase fs-xxs">
                                    <th class="text-muted">#</th>
                                    <th class="text-muted">Servidor</th>
                                    <th class="text-muted text-end">Sistemas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topServidores as $index => $servidor)
                                    <tr>
                                        <td>
                                            @if ($index === 0)
                                                <span class="text-warning fw-bold">🥇</span>
                                            @elseif($index === 1)
                                                <span class="text-secondary fw-bold">🥈</span>
                                            @elseif($index === 2)
                                                <span class="text-warning fw-bold" style="opacity: 0.7;">🥉</span>
                                            @else
                                                <span class="text-muted">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td class="text-decoration-underline">{{ $servidor->nombre }}</td>
                                        <td class="text-end">
                                            <span class="badge bg-primary-subtle text-primary">
                                                {{ $servidor->sistemas_count }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No hay datos</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a class="link-reset text-decoration-underline fw-semibold link-offset-3"
                            href="{{ route('admin.servidores.index') }}">
                            Ver todos los Servidores <i class="ti ti-link"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Actividad Reciente --}}
        <div class="col-xxl-4 col-lg-6">
            <div class="card">
                <div class="card-header justify-content-between align-items-center">
                    <h4 class="card-title">
                        <i class="ti ti-history text-info me-1"></i>
                        Actividad Reciente
                    </h4>
                </div>
                <div class="card-body py-0" data-simplebar style="height: 380px;">
                    @forelse($actividadReciente as $auditoria)
                        @php
                            // Traducir acciones al español
                            $accionTraducida = match (true) {
                                str_contains(strtolower($auditoria->accion), 'created') => 'Creado',
                                str_contains(strtolower($auditoria->accion), 'updated') => 'Actualizado',
                                str_contains(strtolower($auditoria->accion), 'deleted') => 'Eliminado',
                                str_contains(strtolower($auditoria->accion), 'login') => 'Inicio de sesión',
                                str_contains(strtolower($auditoria->accion), 'logout') => 'Cierre de sesión',
                                str_contains(strtolower($auditoria->accion), 'restored') => 'Restaurado',
                                default => $auditoria->accion,
                            };

                            // Determinar icono y color según acción
                            $iconoColor = match (true) {
                                str_contains(strtolower($auditoria->accion), 'created') ||
                                    str_contains(strtolower($auditoria->accion), 'login')
                                    => ['icono' => 'plus', 'color' => 'success'],
                                str_contains(strtolower($auditoria->accion), 'deleted') ||
                                    str_contains(strtolower($auditoria->accion), 'logout')
                                    => ['icono' => 'trash', 'color' => 'danger'],
                                default => ['icono' => 'edit', 'color' => 'info'],
                            };
                        @endphp

                        <div class="d-flex align-items-start gap-2 my-3 border-bottom pb-3">
                            <div class="avatar avatar-sm flex-shrink-0">
                                <span
                                    class="avatar-title bg-{{ $iconoColor['color'] }}-subtle text-{{ $iconoColor['color'] }} rounded">
                                    <i class="ti ti-{{ $iconoColor['icono'] }}"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <p class="mb-1 fw-semibold">{{ $accionTraducida }}</p>
                                <small
                                    class="text-muted d-block mb-1">{{ $auditoria->descripcion ?? 'Sin descripción' }}</small>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="ti ti-user"></i>
                                        {{ $auditoria->user->name ?? 'Sistema' }}
                                    </small>
                                    <small class="text-muted">
                                        <i class="ti ti-clock"></i>
                                        {{ $auditoria->created_at->locale('es')->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="ti ti-inbox text-muted" style="font-size: 48px;"></i>
                            <p class="text-muted mt-2">No hay actividad reciente</p>
                        </div>
                    @endforelse
                </div>
                <div class="card-footer bg-body-secondary border-top border-dashed">
                    <div class="text-center">
                        <a class="link-reset text-decoration-underline fw-semibold link-offset-3"
                            href="{{ route('admin.auditorias.index') }}">
                            Ver todas las Auditorías <i class="ti ti-link"></i>
                        </a>
                    </div>
                </div>
                
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Datos desde Laravel
        const fechasAuditorias = @json($fechasAuditorias);
        const totalesAuditorias = @json($totalesAuditorias);
        const tecnologiasData = @json($tecnologiasConSistemas);
    </script>
    @vite(['resources/js/pages/dashboard-gamea.js'])
@endsection
