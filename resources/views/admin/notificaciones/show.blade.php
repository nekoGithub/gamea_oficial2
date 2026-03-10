{{-- resources/views/admin/notificaciones/show.blade.php --}}

@extends('layouts.vertical', ['title' => 'Detalle de Notificación'])

@section('content')
    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">GAMEA</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.notificaciones.index') }}">Notificaciones</a>
                        </li>
                        <li class="breadcrumb-item active">Detalle</li>
                    </ol>
                </div>
                <h4 class="page-title">Detalle de Notificación</h4>
            </div>
        </div>
    </div>

    <!-- Tarjeta de Detalle -->
    <div class="row">
        <div class="col-12">
            <div class="card">

                <!-- Header con color según severidad -->
                <div
                    class="card-header 
                    @if ($tipo === 'critica') bg-danger-subtle
                    @elseif($tipo === 'alta') bg-warning-subtle
                    @elseif($tipo === 'media') bg-info-subtle
                    @else bg-success-subtle @endif
                ">
                    <div class="d-flex align-items-center">
                        <!-- Icono -->
                        <div class="me-3">
                            @if ($tipo === 'critica')
                                <div
                                    class="avatar-lg bg-danger rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ti ti-alert-triangle text-white fs-1"></i>
                                </div>
                            @elseif($tipo === 'alta')
                                <div
                                    class="avatar-lg bg-warning rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ti ti-alert-circle text-white fs-1"></i>
                                </div>
                            @elseif($tipo === 'media')
                                <div
                                    class="avatar-lg bg-info rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ti ti-info-circle text-white fs-1"></i>
                                </div>
                            @else
                                <div
                                    class="avatar-lg bg-success rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="ti ti-check text-white fs-1"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Título -->
                        <div class="flex-grow-1">
                            <h4 class="mb-1">
                                <i class="ti ti-bell me-2"></i>
                                {{ $notificacion->sistemaVersion && $notificacion->sistemaVersion->sistema
                                    ? $notificacion->sistemaVersion->sistema->nombre
                                    : 'Alerta del Sistema' }}
                            </h4>
                            <p class="mb-0 text-muted">
                                <i class="ti ti-calendar me-1"></i>
                                {{ $notificacion->fecha->format('d/m/Y H:i') }}
                                <span class="mx-2">•</span>
                                {{ $notificacion->fecha->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <!-- Badges -->
                    <div class="mb-4">
                        <div class="d-flex gap-2">
                            @if ($tipo === 'critica')
                                <span class="badge bg-danger badge-lg">
                                    <i class="ti ti-alert-triangle me-1"></i>Severidad Crítica
                                </span>
                            @elseif($tipo === 'alta')
                                <span class="badge bg-warning badge-lg">
                                    <i class="ti ti-alert-circle me-1"></i>Severidad Alta
                                </span>
                            @elseif($tipo === 'media')
                                <span class="badge bg-info badge-lg">
                                    <i class="ti ti-info-circle me-1"></i>Severidad Media
                                </span>
                            @else
                                <span class="badge bg-success badge-lg">
                                    <i class="ti ti-check me-1"></i>Severidad Baja
                                </span>
                            @endif

                            @if ($notificacion->estado === 'enviado')
                                <span class="badge bg-success badge-lg">
                                    <i class="ti ti-check me-1"></i>Enviado
                                </span>
                            @elseif($notificacion->estado === 'pendiente')
                                <span class="badge bg-warning badge-lg">
                                    <i class="ti ti-clock me-1"></i>Pendiente
                                </span>
                            @else
                                <span class="badge bg-danger badge-lg">
                                    <i class="ti ti-x me-1"></i>Fallido
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Mensaje -->
                    <div class="mb-4">
                        <h5 class="mb-3">
                            <i class="ti ti-message-circle me-2"></i>
                            Mensaje Completo
                        </h5>
                        <div class="alert alert-light border">
                            <p class="mb-0" style="white-space: pre-wrap; font-size: 15px; line-height: 1.8;">
                                {{ $mensajeLimpio }}
                            </p>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.notificaciones.index') }}" class="btn btn-light">
                            <i class="ti ti-arrow-left me-1"></i>
                            Volver a Notificaciones
                        </a>

                        @if ($notificacion->estado !== 'enviado')
                            <button type="button" class="btn btn-success" id="btn-reenviar">
                                <i class="ti ti-send me-1"></i>
                                Reenviar a Telegram
                            </button>
                        @endif

                        <button type="button" class="btn btn-danger" id="btn-eliminar">
                            <i class="ti ti-trash me-1"></i>
                            Eliminar
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const notificacionId = {{ $notificacion->id }};

            // Botón Reenviar
            const btnReenviar = document.getElementById('btn-reenviar');
            if (btnReenviar) {
                btnReenviar.addEventListener('click', async function() {
                    const result = await Swal.fire({
                        title: '¿Reenviar a Telegram?',
                        text: 'Se enviará nuevamente esta notificación',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: '<i class="ti ti-send me-1"></i> Sí, reenviar',
                        cancelButtonText: '<i class="ti ti-x me-1"></i> Cancelar',
                        confirmButtonColor: '#198754',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    });

                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Reenviando...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading()
                    });

                    try {
                        const res = await fetch(`/admin/notificaciones/${notificacionId}/reenviar`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });

                        const data = await res.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: '¡Reenviado!',
                                text: data.message || 'Notificación reenviada correctamente',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#198754',
                                timer: 3000
                            });
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message || 'No se pudo reenviar la notificación',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo reenviar la notificación. Intenta de nuevo.',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }

            // Botón Eliminar
            const btnEliminar = document.getElementById('btn-eliminar');
            if (btnEliminar) {
                btnEliminar.addEventListener('click', async function() {
                    const result = await Swal.fire({
                        title: '¿Eliminar notificación?',
                        text: 'Esta acción no se puede deshacer',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="ti ti-trash me-1"></i> Sí, eliminar',
                        cancelButtonText: '<i class="ti ti-x me-1"></i> Cancelar',
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        reverseButtons: true
                    });

                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Eliminando...',
                        text: 'Por favor espera',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => Swal.showLoading()
                    });

                    try {
                        const res = await fetch(`/admin/notificaciones/${notificacionId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });

                        const data = await res.json();

                        if (data.success) {
                            await Swal.fire({
                                icon: 'success',
                                title: '¡Eliminado!',
                                text: 'Notificación eliminada correctamente',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#198754',
                                timer: 2000
                            });
                            setTimeout(() => {
                                window.location.href =
                                    '{{ route('admin.notificaciones.index') }}';
                            }, 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'No se pudo eliminar la notificación',
                                confirmButtonText: 'Aceptar',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo eliminar la notificación. Intenta de nuevo.',
                            confirmButtonText: 'Aceptar',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }

            console.log('✅ SweetAlert inicializado correctamente');
        });
    </script>
@endsection
