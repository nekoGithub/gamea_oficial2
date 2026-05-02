@extends('layouts.vertical', ['title' => 'Roles'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Roles', 'title' => 'Listado'])

    <div class="row">
        <div class="col-12">
            <div class="card">

                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Roles del Sistema</h4>
                    @can('admin.roles.store')
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                            <i class="ti ti-plus"></i> Agregar Rol
                        </button>
                    @endcan
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 w-100" id="table-roles">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre del Rol</th>
                                    <th>Permisos Asignados</th>
                                    <th>Fecha de Creación</th>
                                    <th class="text-end">Acciones</th>
                                </tr>
                                <tr class="column-search-input-bar" id="column-search-roles">
                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control" placeholder="ID" type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control" placeholder="Nombre" type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control" placeholder="Permisos" type="text">
                                        </div>
                                    </th>

                                    <th>
                                        <div class="input-group input-group-sm">
                                            <span class="input-group-text">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input class="form-control" placeholder="Fecha" type="text">
                                        </div>
                                    </th>

                                    <th></th>
                                </tr>
                            </thead>

                            <tbody id="tbody-roles">
                                @forelse ($roles as $rol)
                                    <tr data-id="{{ $rol->id }}">
                                        <td>
                                            <span class="badge bg-primary">#{{ $rol->id }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                <div class="fw-semibold">{{ $rol->name }}</div>
                                                <small class="text-muted">{{ Str::slug($rol->name) }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary">
                                                {{ $rol->permissions->count() }} permisos
                                            </span>
                                        </td>
                                        <td class="text-muted">
                                            {{ $rol->created_at->format('d M, Y') }}
                                        </td>
                                        <td class="text-end">
                                            @can('admin.roles.edit')
                                                <a href="{{ route('admin.roles.edit', $rol->id) }}"
                                                    class="btn btn-default btn-icon btn-sm rounded-circle edit-servidor-btn">
                                                    <i class="ti ti-edit fs-lg"></i>
                                                </a>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- MODAL AGREGAR --}}
    @include('admin.roles.modals.add')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-roles.js'])

    <script>
        const canEdit = @json(auth()->user()?->can('admin.roles.edit') ?? false);
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            // ================= AGREGAR ROL =================
            document.getElementById('addRoleForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    });

                    const data = await res.json();

                    if (!res.ok) {
                        if (data.errors) {
                            Object.keys(data.errors).forEach(key => {
                                const input = form.querySelector(`[name="${key}"]`);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    const feedback = input.nextElementSibling;
                                    if (feedback && feedback.classList.contains(
                                            'invalid-feedback')) {
                                        feedback.textContent = data.errors[key][0];
                                    }
                                }
                            });
                        }
                        return;
                    }

                    if (!data.success) {
                        Swal.fire('Error', data.message || 'No se pudo crear el rol', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('addRoleModal')).hide();

                    const dt = window.rolesDataTable;
                    if (dt) {
                        dt.row.add([
                            `<span class="badge bg-primary">#${data.role.id}</span>`,
                            `<div><div class="fw-semibold">${data.role.name}</div><small class="text-muted">${data.role.name.toLowerCase().replace(/\s+/g, '-')}</small></div>`,
                            `<span class="badge bg-primary">0 permisos</span>`,
                            `${new Date(data.role.created_at).toLocaleDateString('es-ES', {day:'2-digit', month:'short', year:'numeric'})}`,
                            canEdit ?
                            `<a href="/admin/roles/${data.role.id}/edit" class="btn btn-default btn-icon btn-sm rounded-circle"><i class="ti ti-edit fs-lg"></i></a>` :
                            ''
                        ]).draw(false);
                    }

                    form.reset();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Rol creado exitosamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error(error);
                    Swal.fire('Error', 'No se pudo crear el rol', 'error');
                }
            });

            // Limpiar modal al cerrar
            document.getElementById('addRoleModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('addRoleForm');
                if (form) {
                    form.reset();
                    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                }
            });

            // Limpiar errores al escribir
            document.querySelectorAll('#addRoleForm input').forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');
                });
            });
        });
    </script>
@endsection
