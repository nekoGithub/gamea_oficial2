@extends('layouts.vertical', ['title' => 'Reporte de SSL'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Reportes', 'title' => 'Certificados SSL'])

    <div class="row">
        <div class="col-12">

            <!-- Formulario de Filtros y Exportación -->
            <div class="card">
                <div class="card-header border-light">
                    <h4 class="card-title mb-0">
                        <i class="ti ti-filter me-2"></i>
                        Filtros y Exportación
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reportes.ssl.exportar-pdf') }}" method="GET" target="_blank">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label">Estado del SSL</label>
                                <select name="estado" class="form-select">
                                    <option value="">Todos los estados</option>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                </select>
                            </div>

                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label">Días para Vencimiento</label>
                                <select name="dias_vencimiento" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="30">Próximos 30 días</option>
                                    <option value="60">Próximos 60 días</option>
                                    <option value="90">Próximos 90 días</option>
                                </select>
                            </div>

                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="ti ti-file-type-pdf me-1"></i> Exportar a PDF
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alertas de Seguridad -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="ti ti-alert-triangle fs-5 me-2"></i>
                        <div>
                            <strong>¡Importante!</strong> Los certificados SSL próximos a vencer pueden causar interrupciones en los servicios. 
                            Revisa regularmente este reporte para renovarlos a tiempo.
                        </div>
                    </div>

                    <h5 class="mb-3">Contenido del Reporte</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Listado completo de certificados SSL</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Fecha de emisión y vencimiento</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Días restantes hasta vencimiento</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Sistema y dominio asociado</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Estado actual del certificado</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Estadísticas de vencimientos</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
@endsection