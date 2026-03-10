@extends('layouts.vertical', ['title' => 'Credenciales'])

@section('css')
    @vite(['node_modules/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css'])
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Credenciales', 'title' => 'Listado'])

    <div class="row">
        <div class="col-12">

            {{-- TABS --}}
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#tab-activos">
                        <i class="ti ti-list me-1"></i> Listado
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#tab-papelera">
                        <i class="ti ti-trash me-1"></i> Papelera
                    </a>
                </li>
            </ul>

            <div class="tab-content">

                {{-- ================= TAB ACTIVOS ================= --}}
                <div class="tab-pane fade show active" id="tab-activos">

                    <div class="card">

                        <div class="card-header border-light justify-content-between">
                            <h4 class="card-title mb-0">Credenciales de Acceso</h4>
                            <a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCredencialModal"
                                href="#">
                                <i class="ti ti-plus me-1"></i> Agregar Credencial
                            </a>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-activos">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Usuario</th>
                                            <th>URL Acceso</th>
                                            <th>Contraseña</th>
                                            <th>Estado</th>
                                            <th>Fecha Creación</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                        <tr class="column-search-input-bar" id="column-search-activos">
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="ID" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Usuario" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="URL" type="text">
                                                </div>
                                            </th>

                                            <th></th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Estado" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Fecha" type="text">
                                                </div>
                                            </th>

                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbody-activos">
                                        @forelse ($credenciales as $cred)
                                            <tr data-id="{{ $cred->id }}">
                                                <td>{{ $cred->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-user fs-4 text-info me-2"></i>
                                                        <strong>{{ $cred->usuario }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <a href="{{ $cred->url_acceso }}" target="_blank" class="text-primary">
                                                        {{ Str::limit($cred->url_acceso, 40) }}
                                                        <i class="ti ti-external-link ms-1"></i>
                                                    </a>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-secondary ver-password-btn"
                                                        data-id="{{ $cred->id }}">
                                                        <i class="ti ti-eye me-1"></i> Ver
                                                    </button>
                                                </td>
                                                <td>
                                                    <span
                                                        class="badge {{ $cred->estado === 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                                        {{ ucfirst($cred->estado) }}
                                                    </span>
                                                </td>
                                                <td>{{ $cred->created_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a href="#"
                                                            class="btn btn-default btn-icon btn-sm rounded-circle edit-cred-btn"
                                                            data-id="{{ $cred->id }}">
                                                            <i class="ti ti-edit fs-lg"></i>
                                                        </a>
                                                        <a href="#"
                                                            class="btn btn-default btn-icon btn-sm rounded-circle delete-cred-btn"
                                                            data-id="{{ $cred->id }}">
                                                            <i class="ti ti-trash fs-lg"></i>
                                                        </a>
                                                    </div>
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

                {{-- ================= TAB PAPELERA ================= --}}
                <div class="tab-pane fade" id="tab-papelera">

                    <div class="card">

                        <div class="card-header border-light">
                            <h4 class="card-title mb-0">Credenciales Eliminadas</h4>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover table-centered align-middle mb-0 w-100" id="table-papelera">
                                    <thead class="bg-light bg-opacity-25 thead-sm">
                                        <tr class="text-uppercase fs-xxs">
                                            <th>ID</th>
                                            <th>Usuario</th>
                                            <th>URL Acceso</th>
                                            <th>Eliminado</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                        <tr class="column-search-input-bar" id="column-search-papelera">
                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="ID" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Usuario" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="URL" type="text">
                                                </div>
                                            </th>

                                            <th>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light-subtle border-light">
                                                        <i class="ti ti-search"></i>
                                                    </span>
                                                    <input class="form-control bg-light-subtle border-light"
                                                        placeholder="Fecha" type="text">
                                                </div>
                                            </th>
                                            
                                            <th></th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbody-papelera">
                                        @forelse ($credencialesEliminadas as $cred)
                                            <tr data-id="{{ $cred->id }}">
                                                <td>{{ $cred->id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-user fs-4 text-muted me-2"></i>
                                                        <strong>{{ $cred->usuario }}</strong>
                                                    </div>
                                                </td>
                                                <td>{{ Str::limit($cred->url_acceso, 40) }}</td>
                                                <td>{{ $cred->deleted_at->format('d M, Y') }}</td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a href="#"
                                                            class="btn btn-default btn-icon btn-sm rounded-circle restore-cred-btn"
                                                            data-id="{{ $cred->id }}">
                                                            <i class="ti ti-rotate fs-lg"></i>
                                                        </a>
                                                    </div>
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
        </div>
    </div>

    {{-- MODALES --}}
    @include('admin.credenciales.modals.add')
    @include('admin.credenciales.modals.edit')
    @include('admin.credenciales.modals.ver-password')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/js/datatables/datatables-credenciales.js'])

    <script>
        /* ==================== FUNCIONES DE UTILIDAD ==================== */

        function getUsuarioHtml(usuario) {
            return `
                <div class="d-flex align-items-center">
                    <i class="ti ti-user fs-4 text-info me-2"></i>
                    <strong>${usuario}</strong>
                </div>`;
        }

        function getUrlHtml(url) {
            const urlCorta = url.length > 40 ? url.substring(0, 40) + '...' : url;
            return `
                <a href="${url}" target="_blank" class="text-primary">
                    ${urlCorta}
                    <i class="ti ti-external-link ms-1"></i>
                </a>`;
        }

        function getPasswordBtn(id) {
            return `
                <button class="btn btn-sm btn-outline-secondary ver-password-btn" data-id="${id}">
                    <i class="ti ti-eye me-1"></i> Ver
                </button>`;
        }

        function getEstadoBadge(estado) {
            return estado === 'activo' ?
                '<span class="badge bg-success">Activo</span>' :
                '<span class="badge bg-secondary">Inactivo</span>';
        }

        function formatearFecha(fecha) {
            return new Date(fecha).toLocaleDateString('es-ES', {
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            });
        }

        function accionesActivos(id) {
            return `
                <div class="d-flex justify-content-center gap-1">
                    <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle edit-cred-btn" data-id="${id}">
                        <i class="ti ti-edit fs-lg"></i>
                    </a>
                    <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle delete-cred-btn" data-id="${id}">
                        <i class="ti ti-trash fs-lg"></i>
                    </a>
                </div>`;
        }

        function accionesPapelera(id) {
            return `
                <div class="d-flex justify-content-center">
                    <a href="#" class="btn btn-default btn-icon btn-sm rounded-circle restore-cred-btn" data-id="${id}">
                        <i class="ti ti-rotate fs-lg"></i>
                    </a>
                </div>`;
        }

        /* ==================== LÓGICA PRINCIPAL ==================== */

        document.addEventListener('DOMContentLoaded', function() {

            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            /* ================= AGREGAR CREDENCIAL ================= */
            document.getElementById('addCredencialForm')?.addEventListener('submit', async function(e) {
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

                    if (res.status === 422 && data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');

                                // 🔥 BUSCAR EL ELEMENTO .invalid-feedback CORRECTO
                                // Primero buscar en el parent directo
                                let feedback = input.parentElement.querySelector(
                                    '.invalid-feedback');

                                // Si no existe, buscar después del input
                                if (!feedback) {
                                    feedback = input.nextElementSibling;
                                    if (feedback && !feedback.classList.contains(
                                            'invalid-feedback')) {
                                        feedback = null;
                                    }
                                }

                                // Si no existe, buscar después del parent
                                if (!feedback) {
                                    feedback = input.parentElement.nextElementSibling;
                                    if (feedback && !feedback.classList.contains(
                                            'invalid-feedback')) {
                                        feedback = null;
                                    }
                                }

                                if (feedback && feedback.classList.contains(
                                        'invalid-feedback')) {
                                    feedback.textContent = data.errors[field][0];
                                    feedback.style.display = 'block'; // Asegurar que se muestre
                                }
                            }
                        });
                        return;
                    }

                    if (!res.ok || !data.success) {
                        Swal.fire('Error', 'No se pudo crear la credencial', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('addCredencialModal')).hide();

                    window.credencialesDataTables.activos.row.add([
                        data.credencial.id,
                        getUsuarioHtml(data.credencial.usuario),
                        getUrlHtml(data.credencial.url_acceso),
                        getPasswordBtn(data.credencial.id),
                        getEstadoBadge(data.credencial.estado),
                        formatearFecha(data.credencial.created_at),
                        accionesActivos(data.credencial.id)
                    ]).draw(false);

                    form.reset();

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Credencial creada correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al crear credencial:', error);
                    Swal.fire('Error', 'No se pudo crear la credencial', 'error');
                }
            });

            /* ================= EDITAR CREDENCIAL ================= */
            document.getElementById('editCredencialForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                const id = document.getElementById('editCredencialId').value;

                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                try {
                    const res = await fetch(`/admin/credenciales/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: new FormData(form)
                    });

                    const data = await res.json();

                    // Manejar bloqueo y logout
                    if (res.status === 429 && data.locked) {
                        if (data.logout) {
                            await Swal.fire({
                                icon: 'error',
                                title: 'Sesión Cerrada',
                                text: data.message,
                                allowOutsideClick: false,
                                confirmButtonText: 'Entendido'
                            });
                            window.location.href = '/login';
                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Bloqueado',
                            text: data.message,
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }

                    // Manejar contraseña incorrecta
                    if (res.status === 401 && !data.success) {
                        const input = form.querySelector('[name="current_password"]');
                        if (input) {
                            input.classList.add('is-invalid');
                            let feedback = input.parentElement.nextElementSibling;
                            if (feedback && feedback.classList.contains('invalid-feedback')) {
                                feedback.textContent = data.message;
                                if (data.attempts_remaining !== undefined) {
                                    feedback.textContent +=
                                        ` (Intentos restantes: ${data.attempts_remaining})`;
                                }
                                feedback.style.display = 'block';
                            }
                        }
                        return;
                    }

                    if (res.status === 422 && data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = form.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');

                                // 🔥 BUSCAR EL ELEMENTO .invalid-feedback CORRECTO
                                let feedback = input.parentElement.querySelector(
                                    '.invalid-feedback');

                                if (!feedback) {
                                    feedback = input.nextElementSibling;
                                    if (feedback && !feedback.classList.contains(
                                            'invalid-feedback')) {
                                        feedback = null;
                                    }
                                }

                                if (!feedback) {
                                    feedback = input.parentElement.nextElementSibling;
                                    if (feedback && !feedback.classList.contains(
                                            'invalid-feedback')) {
                                        feedback = null;
                                    }
                                }

                                if (feedback && feedback.classList.contains(
                                        'invalid-feedback')) {
                                    feedback.textContent = data.errors[field][0];
                                    feedback.style.display = 'block';
                                }
                            }
                        });
                        return;
                    }

                    if (!res.ok || !data.success) {
                        Swal.fire('Error', 'No se pudo actualizar', 'error');
                        return;
                    }

                    bootstrap.Modal.getInstance(document.getElementById('editCredencialModal')).hide();

                    window.credencialesDataTables.activos.row(`#cred-${id}`).data([
                        data.credencial.id,
                        getUsuarioHtml(data.credencial.usuario),
                        getUrlHtml(data.credencial.url_acceso),
                        getPasswordBtn(id),
                        getEstadoBadge(data.credencial.estado),
                        formatearFecha(data.credencial.created_at),
                        accionesActivos(id)
                    ]).draw(false);

                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Credencial actualizada correctamente',
                        showConfirmButton: false,
                        timer: 2000
                    });

                } catch (error) {
                    console.error('Error al actualizar credencial:', error);
                    Swal.fire('Error', 'No se pudo actualizar', 'error');
                }
            });

            /* ================= DELEGACIÓN DE EVENTOS ================= */
            document.addEventListener('click', async function(e) {

                /* ===== VER CONTRASEÑA ===== */
                const verPasswordBtn = e.target.closest('.ver-password-btn');
                if (verPasswordBtn) {
                    e.preventDefault();

                    const id = verPasswordBtn.dataset.id;
                    document.getElementById('verPasswordCredencialId').value = id;

                    new bootstrap.Modal(document.getElementById('verPasswordModal')).show();

                    return;
                }

                /* ===== EDITAR ===== */
                const editBtn = e.target.closest('.edit-cred-btn');
                if (editBtn) {
                    e.preventDefault();

                    const id = editBtn.dataset.id;

                    try {
                        const res = await fetch(`/admin/credenciales/${id}/edit`);
                        const data = await res.json();

                        document.getElementById('editCredencialId').value = data.credencial.id;
                        document.getElementById('editUsuario').value = data.credencial.usuario;
                        document.getElementById('editUrlAcceso').value = data.credencial.url_acceso;
                        document.getElementById('editPassword').value = '';

                        if (data.credencial.estado === 'activo') {
                            document.getElementById('editEstadoActivo').checked = true;
                        } else {
                            document.getElementById('editEstadoInactivo').checked = true;
                        }

                        new bootstrap.Modal(document.getElementById('editCredencialModal')).show();

                    } catch (error) {
                        console.error('Error al cargar credencial:', error);
                        Swal.fire('Error', 'No se pudo cargar la credencial', 'error');
                    }

                    return;
                }

                /* ===== ELIMINAR ===== */
                const deleteBtn = e.target.closest('.delete-cred-btn');
                if (deleteBtn) {
                    e.preventDefault();

                    const id = deleteBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Eliminar Credencial?',
                        text: 'Se enviará a la papelera',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/credenciales/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json();

                        if (!data.success) {
                            Swal.fire('Error', 'No se pudo eliminar', 'error');
                            return;
                        }

                        const dtA = window.credencialesDataTables.activos;
                        const dtP = window.credencialesDataTables.papelera;

                        const rowData = dtA.row(`#cred-${id}`).data();

                        dtA.row(`#cred-${id}`).remove().draw(false);

                        dtP.row.add([
                            rowData[0],
                            rowData[1],
                            rowData[2].replace(/<a.*?>(.*?)<\/a>/, '$1').replace(/<i.*?><\/i>/,
                                ''),
                            formatearFecha(new Date()),
                            accionesPapelera(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Credencial enviada a la papelera',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al eliminar credencial:', error);
                        Swal.fire('Error', 'No se pudo eliminar', 'error');
                    }

                    return;
                }

                /* ===== RESTAURAR ===== */
                const restoreBtn = e.target.closest('.restore-cred-btn');
                if (restoreBtn) {
                    e.preventDefault();

                    const id = restoreBtn.dataset.id;

                    const confirm = await Swal.fire({
                        title: '¿Restaurar Credencial?',
                        text: 'Volverá a la lista activa',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, restaurar',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#6c757d'
                    });

                    if (!confirm.isConfirmed) return;

                    try {
                        const res = await fetch(`/admin/credenciales/${id}/restore`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': csrf,
                                'Accept': 'application/json'
                            }
                        });

                        const data = await res.json();

                        if (!res.ok || !data.success) {
                            Swal.fire('Error', 'No se pudo restaurar', 'error');
                            return;
                        }

                        const dtA = window.credencialesDataTables.activos;
                        const dtP = window.credencialesDataTables.papelera;

                        dtP.row(`#cred-${id}`).remove().draw(false);

                        dtA.row.add([
                            data.credencial.id,
                            getUsuarioHtml(data.credencial.usuario),
                            getUrlHtml(data.credencial.url_acceso),
                            getPasswordBtn(id),
                            getEstadoBadge(data.credencial.estado),
                            formatearFecha(data.credencial.created_at),
                            accionesActivos(id)
                        ]).draw(false);

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Credencial restaurada correctamente',
                            showConfirmButton: false,
                            timer: 2000
                        });

                    } catch (error) {
                        console.error('Error al restaurar credencial:', error);
                        Swal.fire('Error', 'No se pudo restaurar', 'error');
                    }

                    return;
                }

            });

            /* ================= VERIFICAR Y VER CONTRASEÑA ================= */
            document.getElementById('verPasswordForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const form = this;
                const id = document.getElementById('verPasswordCredencialId').value;
                const passwordInput = document.getElementById('currentPassword');

                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                try {
                    const res = await fetch(`/admin/credenciales/${id}/ver-password`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            current_password: passwordInput.value
                        })
                    });

                    const data = await res.json();

                    // Manejar bloqueo y logout
                    if (res.status === 429 && data.locked) {
                        bootstrap.Modal.getInstance(document.getElementById('verPasswordModal')).hide();

                        if (data.logout) {
                            await Swal.fire({
                                icon: 'error',
                                title: 'Sesión Cerrada',
                                text: data.message,
                                allowOutsideClick: false,
                                confirmButtonText: 'Entendido'
                            });
                            window.location.href = '/login';
                            return;
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Bloqueado',
                            text: data.message,
                            confirmButtonText: 'Entendido'
                        });
                        return;
                    }

                    // Manejar contraseña incorrecta
                    if (res.status === 401 || !data.success) {
                        passwordInput.classList.add('is-invalid');
                        const feedback = passwordInput.parentElement.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            let message = data.message || 'La contraseña es incorrecta';
                            if (data.attempts_remaining !== undefined) {
                                message += ` (Intentos restantes: ${data.attempts_remaining})`;
                            }
                            feedback.textContent = message;
                            feedback.style.display = 'block';
                        }
                        return;
                    }

                    // Mostrar la contraseña revelada
                    document.getElementById('passwordRevelada').textContent = data.password;
                    document.getElementById('verPasswordFormContainer').classList.add('d-none');
                    document.getElementById('passwordReveladaContainer').classList.remove('d-none');

                } catch (error) {
                    console.error('Error al verificar contraseña:', error);
                    Swal.fire('Error', 'No se pudo verificar la contraseña', 'error');
                }
            });

            /* ================= COPIAR CONTRASEÑA AL PORTAPAPELES ================= */
            document.getElementById('copyPasswordBtn')?.addEventListener('click', function() {
                const password = document.getElementById('passwordRevelada').textContent;

                navigator.clipboard.writeText(password).then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Contraseña copiada',
                        showConfirmButton: false,
                        timer: 1500
                    });
                });
            });

            /* ================= LIMPIAR ERRORES AL ESCRIBIR ================= */
            document.querySelectorAll(
                '#addCredencialForm input, #addCredencialForm textarea, #editCredencialForm input, #editCredencialForm textarea'
            ).forEach(input => {
                input.addEventListener('input', function() {
                    this.classList.remove('is-invalid');

                    // 🔥 OCULTAR TAMBIÉN EL MENSAJE DE ERROR
                    // Buscar el .invalid-feedback en las 3 ubicaciones posibles
                    let feedback = this.parentElement.querySelector('.invalid-feedback');

                    if (!feedback) {
                        feedback = this.nextElementSibling;
                        if (feedback && !feedback.classList.contains('invalid-feedback')) {
                            feedback = null;
                        }
                    }

                    if (!feedback) {
                        feedback = this.parentElement.nextElementSibling;
                        if (feedback && !feedback.classList.contains('invalid-feedback')) {
                            feedback = null;
                        }
                    }

                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                        feedback.textContent = '';
                        feedback.style.display = 'none';
                    }
                });
            });

            // 🔥 LIMPIAR ERRORES EN MODAL DE VER CONTRASEÑA
            document.getElementById('currentPassword')?.addEventListener('input', function() {
                this.classList.remove('is-invalid');

                let feedback = this.parentElement.querySelector('.invalid-feedback');

                if (!feedback) {
                    feedback = this.nextElementSibling;
                    if (feedback && !feedback.classList.contains('invalid-feedback')) {
                        feedback = null;
                    }
                }

                if (!feedback) {
                    feedback = this.parentElement.nextElementSibling;
                    if (feedback && !feedback.classList.contains('invalid-feedback')) {
                        feedback = null;
                    }
                }

                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.textContent = '';
                    feedback.style.display = 'none';
                }
            });

            /* ================= LIMPIAR MODALES AL CERRAR ================= */
            document.getElementById('addCredencialModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('addCredencialForm');
                if (!form) return;

                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                // 🔥 OCULTAR TODOS LOS MENSAJES DE ERROR
                form.querySelectorAll('.invalid-feedback').forEach(feedback => {
                    feedback.textContent = '';
                    feedback.style.display = 'none';
                });
            });

            document.getElementById('editCredencialModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('editCredencialForm');
                if (!form) return;

                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                // 🔥 OCULTAR TODOS LOS MENSAJES DE ERROR
                form.querySelectorAll('.invalid-feedback').forEach(feedback => {
                    feedback.textContent = '';
                    feedback.style.display = 'none';
                });
            });

            document.getElementById('verPasswordModal')?.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('verPasswordForm');
                if (!form) return;

                form.reset();
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

                // 🔥 OCULTAR TODOS LOS MENSAJES DE ERROR
                form.querySelectorAll('.invalid-feedback').forEach(feedback => {
                    feedback.textContent = '';
                    feedback.style.display = 'none';
                });

                document.getElementById('verPasswordFormContainer').classList.remove('d-none');
                document.getElementById('passwordReveladaContainer').classList.add('d-none');
            });

        });
    </script>
@endsection
