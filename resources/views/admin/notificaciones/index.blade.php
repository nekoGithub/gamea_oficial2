@extends('layouts.vertical', ['title' => 'Notificaciones'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', [
        'subtitle' => 'Notificaciones',
        'title' => 'Sistema de Alertas',
    ])

    {{-- ESTADÍSTICAS --}}
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-primary-subtle text-primary rounded">
                                    <i class="ti ti-bell fs-2"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Total</p>
                            <h4 class="mb-0">{{ $estadisticas['total'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-warning-subtle text-warning rounded">
                                    <i class="ti ti-clock fs-2"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Pendientes</p>
                            <h4 class="mb-0">{{ $estadisticas['pendientes'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-danger-subtle text-danger rounded">
                                    <i class="ti ti-alert-triangle fs-2"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Críticas</p>
                            <h4 class="mb-0">{{ $estadisticas['criticas'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 me-3">
                            <div class="avatar-sm">
                                <span class="avatar-title bg-success-subtle text-success rounded">
                                    <i class="ti ti-check fs-2"></i>
                                </span>
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <p class="text-muted mb-1">Enviadas</p>
                            <h4 class="mb-0">{{ $estadisticas['enviadas'] }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLA --}}
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-light d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Listado de Notificaciones</h4>
                    @can('admin.notificaciones.update')
                        <button class="btn btn-danger btn-sm" id="limpiarBtn">
                            <i class="ti ti-trash me-1"></i> Limpiar Antiguas
                        </button>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-notificaciones">
                            <thead class="bg-light bg-opacity-25 thead-sm">
                                <tr class="text-uppercase fs-xxs">
                                    <th>ID</th>
                                    <th>Fecha</th>
                                    <th>Severidad</th>
                                    <th>Mensaje</th>
                                    <th>Sistema</th>
                                    <th>Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                                <tr class="column-search-input-bar" id="column-search">
                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light" placeholder="ID"
                                                type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light" placeholder="Fecha"
                                                type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light" placeholder="Severidad"
                                                type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light" placeholder="Mensaje"
                                                type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light" placeholder="Sistema"
                                                type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text bg-light-subtle border-light">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control bg-light-subtle border-light" placeholder="Estado"
                                                type="text">
                                        </div>
                                    </th>

                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($notificaciones as $notif)
                                    <tr data-id="{{ $notif->id }}">
                                        <td>{{ $notif->id }}</td>
                                        <td>{{ $notif->fecha->format('d/m/Y H:i') }}</td>
                                        <td>{!! $notif->severidad_badge !!}</td>
                                        <td>{{ Str::limit($notif->mensaje, 80) }}</td>
                                        <td>
                                            @if ($notif->sistemaVersion)
                                                <span
                                                    class="badge bg-info">{{ $notif->sistemaVersion->sistema->nombre }}</span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                        <td>{!! $notif->estado_badge !!}</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-1">
                                                @can('admin.notificaciones.update')
                                                    @if ($notif->estado !== 'enviado')
                                                        <button
                                                            class="btn btn-success btn-icon btn-sm rounded-circle reenviar-btn"
                                                            data-id="{{ $notif->id }}" title="Reenviar">
                                                            <i class="ti ti-send fs-lg"></i>
                                                        </button>
                                                    @endif
                                                @endcan
                                                @can('admin.notificaciones.show')
                                                    <button class="btn btn-default btn-icon btn-sm rounded-circle ver-btn"
                                                        data-id="{{ $notif->id }}" data-mensaje="{{ $notif->mensaje }}"
                                                        title="Ver completo">
                                                        <i class="ti ti-eye fs-lg"></i>
                                                    </button>
                                                @endcan
                                                @can('admin.notificaciones.destroy')
                                                    <button class="btn btn-default btn-icon btn-sm rounded-circle delete-btn"
                                                        data-id="{{ $notif->id }}" title="Eliminar">
                                                        <i class="ti ti-trash fs-lg"></i>
                                                    </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-notificaciones.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            // VER MENSAJE COMPLETO
            document.addEventListener('click', function(e) {
                const verBtn = e.target.closest('.ver-btn');
                if (verBtn) {
                    e.preventDefault();
                    const mensaje = verBtn.dataset.mensaje;

                    Swal.fire({
                        title: 'Mensaje Completo',
                        html: `<pre class="text-start">${mensaje}</pre>`,
                        width: 600,
                        confirmButtonText: 'Cerrar'
                    });
                }
            });

            // REENVIAR
            document.addEventListener('click', async function(e) {
                const reenviarBtn = e.target.closest('.reenviar-btn');
                if (reenviarBtn) {
                    e.preventDefault();
                    const id = reenviarBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Reenviar a Telegram?',
                        text: 'Se enviará nuevamente esta alerta',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, reenviar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/notificaciones/${id}/reenviar`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json();

                        if (data.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Notificación reenviada',
                                showConfirmButton: false,
                                timer: 2000
                            });

                            setTimeout(() => location.reload(), 2000);
                        } else {
                            Swal.fire('Error', 'No se pudo reenviar', 'error');
                        }
                    } catch (error) {
                        Swal.fire('Error', 'Error al reenviar', 'error');
                    }
                }
            });

            // ELIMINAR
            document.addEventListener('click', async function(e) {
                const deleteBtn = e.target.closest('.delete-btn');
                if (deleteBtn) {
                    e.preventDefault();
                    const id = deleteBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Eliminar notificación?',
                        text: 'Esta acción no se puede deshacer',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#d33'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/notificaciones/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json();

                        if (data.success) {
                            window.notificacionesDataTable.row(`[data-id="${id}"]`).remove().draw();

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Notificación eliminada',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    } catch (error) {
                        Swal.fire('Error', 'No se pudo eliminar', 'error');
                    }
                }
            });

            // LIMPIAR ANTIGUAS
            document.getElementById('limpiarBtn')?.addEventListener('click', async function() {
                const confirm = await Swal.fire({
                    title: '¿Limpiar notificaciones antiguas?',
                    text: 'Se eliminarán las enviadas hace más de 30 días',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, limpiar',
                    cancelButtonText: 'Cancelar'
                });

                if (!confirm.isConfirmed) return;

                try {
                    const res = await fetch('/admin/notificaciones/limpiar', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();

                    if (data.success) {
                        Swal.fire('Éxito', `${data.eliminadas} notificaciones eliminadas`, 'success');
                        setTimeout(() => location.reload(), 2000);
                    }
                } catch (error) {
                    Swal.fire('Error', 'No se pudo limpiar', 'error');
                }
            });
        });
    </script>
@endsection
