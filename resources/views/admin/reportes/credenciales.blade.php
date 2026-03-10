@extends('layouts.vertical', ['title' => 'Reporte de Credenciales'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Reportes', 'title' => 'Credenciales de Acceso'])

    <div class="row">
        <div class="col-12">

            <!-- Alerta de Seguridad -->
            <div class="alert alert-danger border-danger" role="alert">
                <div class="d-flex align-items-center">
                    <i class="ti ti-lock-access fs-4 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-1">Reporte Confidencial</h5>
                        <p class="mb-0">
                            Este reporte contiene información sensible de acceso a sistemas. Las contraseñas se muestran ocultas por seguridad.
                            <strong>Mantén este documento en lugar seguro y limita su distribución solo a personal autorizado.</strong>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Formulario de Filtros y Exportación -->
            <div class="card">
                <div class="card-header border-light">
                    <h4 class="card-title mb-0">
                        <i class="ti ti-filter me-2"></i>
                        Filtros y Exportación
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reportes.credenciales.exportar-pdf') }}" method="GET" target="_blank">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label">Tipo de Credencial</label>
                                <select name="tipo" class="form-select">
                                    <option value="">Todos los tipos</option>
                                    <option value="admin">Administrador</option>
                                    <option value="usuario">Usuario</option>
                                    <option value="bd">Base de Datos</option>
                                    <option value="api">API</option>
                                </select>
                            </div>

                            <div class="col-lg-4 col-md-6 mb-3">
                                <label class="form-label">Sistema</label>
                                <select name="sistema_id" class="form-select">
                                    <option value="">Todos los sistemas</option>
                                    @foreach($sistemas as $sistema)
                                        <option value="{{ $sistema->id }}">{{ $sistema->nombre }}</option>
                                    @endforeach
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

            <!-- Información del Reporte -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="mb-3">Contenido del Reporte</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Listado de credenciales por sistema</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Tipo de credencial (Admin, Usuario, BD, API)</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Nombre de usuario de acceso</li>
                        <li class="mb-2"><i class="ti ti-shield-lock text-danger me-2"></i> Contraseñas ocultas (••••••••)</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Observaciones y notas de seguridad</li>
                        <li class="mb-2"><i class="ti ti-check text-success me-2"></i> Estadísticas por tipo de credencial</li>
                    </ul>

                    <div class="alert alert-info mt-3" role="alert">
                        <strong>Nota:</strong> Las contraseñas reales NO aparecen en el PDF por seguridad. Solo se muestra que existe una credencial.
                        Para acceder a las contraseñas completas, utilice el módulo de Credenciales del sistema.
                    </div>
                </div>
            </div>

            <!-- Mejores Prácticas -->
            <div class="card mt-4">
                <div class="card-header bg-warning-subtle">
                    <h5 class="mb-0">Mejores Prácticas de Seguridad</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-success mb-3">Recomendado</h6>
                            <ul class="small">
                                <li>Cambiar credenciales cada 90 días</li>
                                <li>Usar contraseñas de mínimo 12 caracteres</li>
                                <li>Incluir mayúsculas, minúsculas, números y símbolos</li>
                                <li>No reutilizar contraseñas entre sistemas</li>
                                <li>Activar autenticación de dos factores (2FA)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-danger mb-3">Evitar</h6>
                            <ul class="small">
                                <li>Contraseñas simples como "123456" o "password"</li>
                                <li>Compartir credenciales por email o WhatsApp</li>
                                <li>Anotar contraseñas en papel o archivos sin cifrar</li>
                                <li>Usar la misma contraseña para múltiples sistemas</li>
                                <li>Dejar sesiones abiertas en computadoras compartidas</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection