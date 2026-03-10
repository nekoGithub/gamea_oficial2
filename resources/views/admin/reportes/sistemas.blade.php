@extends('layouts.vertical', ['title' => 'Reporte de Sistemas'])

@section('content')
    @include('layouts.partials/page-title', [
        'subtitle' => 'Reportes',
        'title' => 'Inventario de Sistemas',
    ])

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
                    <form id="form-filtros" method="GET">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label">Estado del Sistema</label>
                                <select name="estado" class="form-select">
                                    <option value="">Todos los estados</option>
                                    <option value="activo">Activo</option>
                                    <option value="inactivo">Inactivo</option>                                    
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label">Servidor</label>
                                <select name="servidor_id" class="form-select">
                                    <option value="">Todos los servidores</option>
                                    @foreach ($servidores as $servidor)
                                        <option value="{{ $servidor->id }}">{{ $servidor->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label">Tecnología</label>
                                <select name="tecnologia_id" class="form-select">
                                    <option value="">Todas las tecnologías</option>
                                    @foreach ($tecnologias as $tecnologia)
                                        <option value="{{ $tecnologia->id }}">{{ $tecnologia->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-lg-3 col-md-6 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-grid gap-2">
                                    <button type="button" class="btn btn-danger" onclick="exportar('pdf')">
                                        <i class="ti ti-file-type-pdf me-1"></i> Exportar a PDF
                                    </button>
                                    <button type="button" class="btn btn-success" onclick="exportar('excel')">
                                        <i class="ti ti-file-spreadsheet me-1"></i> Exportar a Excel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información del Reporte -->
            <div class="card mt-4">
                <div class="card-body">
                    <div class="alert alert-primary d-flex align-items-center" role="alert">
                        <i class="ti ti-info-circle fs-5 me-2"></i>
                        <div>
                            <strong>Información:</strong> Este reporte genera un archivo con el inventario completo de
                            sistemas registrados en GAMEA.
                            Utiliza los filtros para personalizar el contenido del reporte según tus necesidades.
                        </div>
                    </div>

                    <h5 class="mb-3">Contenido del Reporte</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">
                                <i class="ti ti-file-type-pdf text-danger me-1"></i> Formato PDF
                            </h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Información completa de
                                    cada sistema</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Estado actual (activo,
                                    inactivo, mantenimiento)</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Servidor donde está alojado
                                </li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Tecnología, sistema
                                    operativo y base de datos</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> URL de acceso</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Estadísticas por estado y
                                    tecnología</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">
                                <i class="ti ti-file-spreadsheet text-success me-1"></i> Formato Excel
                            </h6>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> 3 hojas: Portada,
                                    Inventario y Estadísticas</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Datos organizados en tabla
                                    con filtros</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Colores según estado del
                                    sistema</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Estadísticas con gráficos
                                    automáticos</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Formato editable para
                                    análisis</li>
                                <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Columnas auto-ajustables
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function exportar(tipo) {
            const form = document.getElementById('form-filtros');
            const formData = new FormData(form);
            const params = new URLSearchParams(formData).toString();

            let url = '';
            if (tipo === 'pdf') {
                url = '{{ route('admin.reportes.sistemas.exportar-pdf') }}';
            } else if (tipo === 'excel') {
                url = '{{ route('admin.reportes.sistemas.exportar-excel') }}';
            }

            window.open(url + (params ? '?' + params : ''), '_blank');
        }
    </script>
@endsection
