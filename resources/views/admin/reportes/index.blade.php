@extends('layouts.vertical', ['title' => 'Reportes'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Reportes', 'title' => 'Panel de Reportes'])

    <div class="row">
        <div class="col-12">

            <!-- Tarjetas de Estadísticas -->
            <div class="row">
                <!-- Sistemas -->
                <div class="col-lg-3 col-md-6">
                    <div class="card border-start border-primary border-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h4 class="fw-semibold mb-1">{{ number_format($estadisticas['total_sistemas']) }}</h4>
                                    <p class="mb-0 text-muted">Total Sistemas</p>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-primary-subtle text-primary fs-6">
                                        <i class="ti ti-server fs-5"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <small class="text-success">✓ {{ $estadisticas['sistemas_activos'] }} Activos</small>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">✗ {{ $estadisticas['sistemas_inactivos'] }} Inactivos</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SSL -->
                <div class="col-lg-3 col-md-6">
                    <div class="card border-start border-info border-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h4 class="fw-semibold mb-1">{{ number_format($estadisticas['total_ssl']) }}</h4>
                                    <p class="mb-0 text-muted">Certificados SSL</p>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-info-subtle text-info fs-6">
                                        <i class="ti ti-lock fs-5"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <small class="text-success">✓ {{ $estadisticas['ssl_activos'] }} Activos</small>
                                </div>
                                <div class="col-6">
                                    <small class="text-warning">⚠ {{ $estadisticas['ssl_por_vencer'] }} Por vencer</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Servidores -->
                <div class="col-lg-3 col-md-6">
                    <div class="card border-start border-success border-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h4 class="fw-semibold mb-1">{{ number_format($estadisticas['total_servidores']) }}</h4>
                                    <p class="mb-0 text-muted">Servidores</p>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-success-subtle text-success fs-6">
                                        <i class="ti ti-database fs-5"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <small class="text-success">✓ {{ $estadisticas['servidores_activos'] }}
                                        Operativos</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Credenciales -->
                <div class="col-lg-3 col-md-6">
                    <div class="card border-start border-warning border-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h4 class="fw-semibold mb-1">{{ number_format($estadisticas['total_credenciales']) }}
                                    </h4>
                                    <p class="mb-0 text-muted">Credenciales</p>
                                </div>
                                <div class="ms-auto">
                                    <span class="badge bg-warning-subtle text-warning fs-6">
                                        <i class="ti ti-key fs-5"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12">
                                    <small class="text-success">✓ {{ $estadisticas['credenciales_activas'] }}
                                        Activas</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de Reportes Disponibles -->
            <div class="row mt-4">
                <div class="col-12">
                    <h5 class="mb-3">Reportes Disponibles</h5>
                </div>

                <!-- Reporte de Sistemas -->
                <div class="col-lg-6 col-md-12">
                    <div class="card hover-shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="bg-primary-subtle rounded p-3 me-3">
                                    <i class="ti ti-server fs-7 text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-2">Inventario de Sistemas</h5>
                                    <p class="card-text text-muted mb-3">
                                        Listado completo de todos los sistemas, incluyendo estado, servidor, tecnología, y
                                        más detalles técnicos.
                                    </p>
                                    <div class="d-flex gap-2">
                                        @can('admin.reportes.index')
                                            <a href="{{ route('admin.reportes.sistemas') }}" class="btn btn-primary btn-sm">
                                                <i class="ti ti-eye me-1"></i> Ver Reporte
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporte de SSL -->
                <div class="col-lg-6 col-md-12">
                    <div class="card hover-shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="bg-info-subtle rounded p-3 me-3">
                                    <i class="ti ti-lock fs-7 text-info"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-2">Certificados SSL</h5>
                                    <p class="card-text text-muted mb-3">
                                        Control de certificados SSL, fechas de vencimiento, y alertas de seguridad para
                                        evitar caídas de servicios.
                                    </p>
                                    <div class="d-flex gap-2">
                                        @can('admin.reportes.index')
                                            <a href="{{ route('admin.reportes.ssl') }}" class="btn btn-info btn-sm">
                                                <i class="ti ti-eye me-1"></i> Ver Reporte
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Servidores -->
                <div class="col-lg-6 col-md-12 mt-3">
                    <div class="card hover-shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="bg-success-subtle rounded p-3 me-3">
                                    <i class="ti ti-database fs-7 text-success"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-2">Inventario de Servidores</h5>
                                    <p class="card-text text-muted mb-3">
                                        Detalle de toda la infraestructura de servidores, sistemas alojados, recursos y
                                        estado operativo.
                                    </p>
                                    <div class="d-flex gap-2">
                                        @can('admin.reportes.index')
                                            <a href="{{ route('admin.reportes.servidores') }}" class="btn btn-success btn-sm">
                                                <i class="ti ti-eye me-1"></i> Ver Reporte
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Credenciales -->
                <div class="col-lg-6 col-md-12 mt-3">
                    <div class="card hover-shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="bg-warning-subtle rounded p-3 me-3">
                                    <i class="ti ti-key fs-7 text-warning"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-2">Credenciales de Acceso</h5>
                                    <p class="card-text text-muted mb-3">
                                        <span class="badge bg-danger-subtle text-danger">🔒 Confidencial</span>
                                        Reporte de credenciales por sistema con información de seguridad protegida.
                                    </p>
                                    <div class="d-flex gap-2">
                                        @can('admin.credenciales.index')
                                            <a href="{{ route('admin.reportes.credenciales') }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="ti ti-eye me-1"></i> Ver Reporte
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporte de Auditorías -->
                <div class="col-lg-6 col-md-12 mt-3">
                    <div class="card hover-shadow">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="bg-secondary-subtle rounded p-3 me-3">
                                    <i class="ti ti-history fs-7 text-secondary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-2">Registro de Auditorías</h5>
                                    <p class="card-text text-muted mb-3">
                                        Historial completo de actividades del sistema, accesos de usuarios y cambios
                                        realizados.
                                    </p>
                                    <div class="d-flex gap-2">
                                        @can('admin.auditorias.index')
                                            <a href="{{ route('admin.auditorias.index') }}" class="btn btn-secondary btn-sm">
                                                <i class="ti ti-eye me-1"></i> Ver Reporte
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividad Reciente -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Actividad Reciente</h5>
                            <div class="row text-center">
                                <div class="col-md-4">
                                    <div class="border-end">
                                        <h3 class="fw-semibold text-primary">
                                            {{ number_format($estadisticas['actividad_hoy']) }}</h3>
                                        <p class="mb-0 text-muted">Actividades Hoy</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border-end">
                                        <h3 class="fw-semibold text-info">
                                            {{ number_format($estadisticas['actividad_semana']) }}</h3>
                                        <p class="mb-0 text-muted">Esta Semana</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h3 class="fw-semibold text-danger">{{ $estadisticas['ssl_vencidos'] }}</h3>
                                    <p class="mb-0 text-muted">SSL Vencidos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }
    </style>
@endsection
