@extends('layouts.vertical', ['title' => 'Editar Rol'])

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Roles', 'title' => 'Editar'])

    <form id="editRoleForm" action="{{ route('admin.roles.update', $rol->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- HEADER --}}
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-3">

                    <div class="flex-grow-1">
                        <label class="form-label mb-1 fw-semibold">Nombre del Rol</label>
                        <input type="text" class="form-control" name="name" value="{{ $rol->name }}" required>
                    </div>

                    <div class="text-center">
                        <div class="fw-semibold">Permisos</div>
                        <div class="fs-4 text-primary">
                            <span id="totalSelectedPerms">
                                {{ count(array_intersect($permissions, collect($all_permissions)->flatten()->pluck('name')->toArray())) }}
                            </span>
                            <span class="text-muted fs-6">
                                / {{ collect($all_permissions)->flatten()->count() }}
                            </span>
                        </div>
                    </div>

                    <div class="ms-auto d-flex gap-2">
                        <button type="button" class="btn btn-outline-success" id="selectAllPermissions">
                            Todo
                        </button>
                        <button type="button" class="btn btn-outline-danger" id="deselectAllPermissions">
                            Ninguno
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Guardar
                        </button>
                    </div>

                </div>
            </div>
        </div>

        {{-- PERMISOS --}}
        <div class="row">
            @foreach ($all_permissions as $section => $perms)
                <div class="col-12 col-md-6 col-xl-4 mb-3 d-flex" data-section="{{ $section }}">
                    <div class="card h-100 w-100">

                        {{-- HEADER CARD --}}
                        <div class="card-header bg-dark text-white py-2">
                            <div class="d-flex align-items-center justify-content-between gap-2">
                                <h6 class="mb-0 fw-semibold">
                                    {{ $section }}
                                </h6>

                                <span class="badge bg-primary px-2 py-1">
                                    <span class="selected-count">
                                        {{ collect($perms)->filter(fn($p) => in_array($p->name, $permissions))->count() }}
                                    </span>
                                    / {{ count($perms) }}
                                </span>
                            </div>
                        </div>


                        {{-- BODY --}}
                        <div class="card-body p-3">
                            <div class="row g-2">
                                @foreach ($perms as $perm)
                                    <div class="col-12 col-sm-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input permission-checkbox" type="checkbox"
                                                name="permissions[]" value="{{ $perm->name }}"
                                                id="perm_{{ $perm->id }}" data-section="{{ $section }}"
                                                {{ in_array($perm->name, $permissions) ? 'checked' : '' }}>
                                            <label class="form-check-label small" for="perm_{{ $perm->id }}">
                                                {{ $perm->description ?? $perm->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- FOOTER --}}
                        <div class="card-footer bg-light text-center py-2">
                            <button type="button" class="btn btn-sm btn-outline-success me-1 select-section"
                                data-section="{{ $section }}">
                                Todo
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary deselect-section"
                                data-section="{{ $section }}">
                                Ninguno
                            </button>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

    </form>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editRoleForm');
            const totalSelectedCounter = document.getElementById('totalSelectedPerms');
            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            // Actualizar contador total
            function updateTotalCounter() {
                const totalChecked = document.querySelectorAll('.permission-checkbox:checked').length;
                totalSelectedCounter.textContent = totalChecked;
            }

            // Actualizar contador de sección
            function updateSectionCounter(section) {
                const sectionElement = document.querySelector(`[data-section="${section}"]`);
                const checkedInSection = sectionElement.querySelectorAll('.permission-checkbox:checked').length;
                const counter = sectionElement.querySelector('.selected-count');
                counter.textContent = checkedInSection;
            }

            // Event listener para checkboxes individuales
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    updateTotalCounter();
                    updateSectionCounter(this.dataset.section);
                });
            });

            // Seleccionar todos los permisos
            document.getElementById('selectAllPermissions').addEventListener('click', function() {
                document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                    checkbox.checked = true;
                });
                updateTotalCounter();
                document.querySelectorAll('[data-section]').forEach(section => {
                    updateSectionCounter(section.dataset.section);
                });
            });

            // Deseleccionar todos los permisos
            document.getElementById('deselectAllPermissions').addEventListener('click', function() {
                document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                    checkbox.checked = false;
                });
                updateTotalCounter();
                document.querySelectorAll('[data-section]').forEach(section => {
                    updateSectionCounter(section.dataset.section);
                });
            });

            // Seleccionar todos en una sección
            document.querySelectorAll('.select-section').forEach(button => {
                button.addEventListener('click', function() {
                    const section = this.dataset.section;
                    document.querySelectorAll(`.permission-checkbox[data-section="${section}"]`)
                        .forEach(checkbox => {
                            checkbox.checked = true;
                        });
                    updateTotalCounter();
                    updateSectionCounter(section);
                });
            });

            // Deseleccionar todos en una sección
            document.querySelectorAll('.deselect-section').forEach(button => {
                button.addEventListener('click', function() {
                    const section = this.dataset.section;
                    document.querySelectorAll(`.permission-checkbox[data-section="${section}"]`)
                        .forEach(checkbox => {
                            checkbox.checked = false;
                        });
                    updateTotalCounter();
                    updateSectionCounter(section);
                });
            });

            // Submit del formulario
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                try {
                    const response = await fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Guardado',
                            text: 'Rol actualizado correctamente',
                            showConfirmButton: false,
                            timer: 1500,
                            toast: true,
                            position: 'top-end'
                        }).then(() => {
                            window.location.href = '{{ route('admin.roles.index') }}';
                        });
                    } else {
                        throw new Error(data.message || 'Error');
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo guardar',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                }
            });
        });
    </script>
@endsection
