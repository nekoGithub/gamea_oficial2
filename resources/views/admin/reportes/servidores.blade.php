@extends('layouts.vertical', ['title' => 'Reporte de Servidores'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Reportes', 'title' => 'Inventario de Servidores'])

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
                    <form action="{{ route('admin.reportes.servidores.exportar-pdf') }}" method="GET" target="_blank">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 mb-3">
                                <label class="form-label">Estado del Servidor</label>
                                <select name="estado" class="form-select">
                                    <option value="">Todos los estados</option>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>
                                    <option value="mantenimiento">Mantenimiento</option>
                                </select>
                            </div>

                            <div class="col-lg-6 col-md-6 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="ti ti-file-type-pdf me-1"></i> Exportar a PDF
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información del Reporte -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="alert alert-success d-flex align-items-center" role="alert">
                        <i class="ti ti-info-circle fs-5 me-2"></i>
                        <div>
                            <strong>Infraestructura:</strong> Este reporte muestra el inventario completo de servidores y la distribución 
                            de sistemas alojados en cada uno. Ideal para gestión de recursos y planificación.
                        </div>
                    </div>

                    <h5 class="mb-3">Contenido del Reporte</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Inventario completo de servidores</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Estado operativo de cada servidor</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Dirección IP y puerto de acceso</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Sistema operativo instalado</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Cantidad de sistemas alojados</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Observaciones y notas técnicas</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Estadísticas de uso y distribución</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>
@endsection