@extends('layouts.vertical', ['title' => 'Usuarios'])

@section('css')
@endsection

@section('content')
    @include('layouts.partials/page-title', ['subtitle' => 'Usuarios', 'title' => 'Listado'])

    <div class="row">
        <div class="col-12">
            <div class="card" data-table="" data-table-rows-per-page="8">
                <div class="card-header border-light justify-content-between">
                    <div class="d-flex gap-2">
                        <div class="app-search">
                            <input class="form-control" data-table-search="" placeholder="Buscar usuarios..."
                                type="search" />
                            <i class="app-search-icon text-muted" data-lucide="search"></i>
                        </div>
                        <button class="btn btn-danger d-none" data-table-delete-selected="">Eliminar</button>
                    </div>
                    <div class="d-flex align-items-center gap-1">
                        <!-- Registros por Página -->
                        <div>
                            <select class="form-select form-control my-1 my-md-0" data-table-set-rows-per-page="">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                            </select>
                        </div>
                        <!-- Filtro de Estado -->
                        <div class="app-search">
                            <select class="form-select form-control my-1 my-md-0" data-table-filter="status">
                                <option value="">Todos</option>
                                <option value="Activo">Activos</option>
                                <option value="Inactivo">Inactivos</option>
                            </select>
                            <i class="app-search-icon text-muted" data-lucide="circle"></i>
                        </div>
                        @can('admin.users.store')
                            <a class="btn btn-primary ms-1" data-bs-toggle="modal" data-bs-target="#addUserModal"
                                href="#">
                                <i class="fs-sm me-2" data-lucide="plus"></i> Agregar Usuario
                            </a>
                        @endcan
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-custom table-centered table-select table-hover w-100 mb-0">
                        <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                            <tr class="text-uppercase fs-xxs">
                                <th class="ps-3" style="width: 1%;">
                                    <input class="form-check-input form-check-input-light fs-14 mt-0"
                                        data-table-select-all="" type="checkbox" value="option" />
                                </th>
                                <th data-table-sort="">Nro</th>
                                <th data-table-sort="product">Usuario</th>
                                <th data-table-sort="">Fecha de creación</th>
                                <th data-table-sort="">Verificado</th>
                                <th data-column="status" data-table-sort="">Estado</th>
                                <th class="text-center" style="width: 1%;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr data-id="{{ $user->id }}">
                                    <td class="ps-3">
                                        <input class="form-check-input form-check-input-light fs-14 mt-0" type="checkbox" />
                                    </td>
                                    <td>{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-md me-3">
                                                <img alt="Avatar" class="img-fluid rounded"
                                                    src="{{ $user->profile_photo_path
                                                        ? asset('storage/avatars/' . $user->profile_photo_path)
                                                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" />
                                            </div>
                                            <div>
                                                <h5 class="mb-0">
                                                    <a class="link-reset" href="#">{{ $user->name }}</a>
                                                </h5>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->created_at->format('d M, Y') }}</td>
                                    <td>{{ $user->email_verified_at ? 'Verificado' : 'No Verificado' }}</td>
                                    <td>
                                        <span
                                            class="badge {{ $user->email_verified_at && !$user->deleted_at ? 'badge-soft-success' : 'badge-soft-danger' }}">
                                            {{ $user->email_verified_at && !$user->deleted_at ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            @if ($user->deleted_at)
                                                @can('admin.users.restore')
                                                    <a href="#"
                                                        class="btn btn-success btn-icon btn-sm rounded-circle restore-user-btn"
                                                        data-id="{{ $user->id }}">
                                                        <i class="ti ti-refresh fs-lg"></i>
                                                    </a>
                                                @endcan
                                            @else
                                                @can('admin.users.show')
                                                    <a href="#"
                                                        class="btn btn-default btn-icon btn-sm rounded-circle view-user-btn"
                                                        data-id="{{ $user->id }}">
                                                        <i class="ti ti-eye fs-lg"></i>
                                                    </a>
                                                @endcan
                                                @can('admin.users.edit')
                                                    <a href="#"
                                                        class="btn btn-default btn-icon btn-sm rounded-circle edit-user-btn"
                                                        data-id="{{ $user->id }}">
                                                        <i class="ti ti-edit fs-lg"></i>
                                                    </a>
                                                @endcan
                                                @can('admin.users.destroy')
                                                    <a href="#"
                                                        class="btn btn-default btn-icon btn-sm rounded-circle delete-user-btn"
                                                        data-id="{{ $user->id }}">
                                                        <i class="ti ti-trash fs-lg"></i>
                                                    </a>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-3">No se encontraron usuarios</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div data-table-pagination-info="usuarios"></div>
                        <div data-table-pagination=""></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modales -->
    @include('admin.users.modals.add')
    @include('admin.users.modals.edit')
    @include('admin.users.modals.view')
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let customTableInstance;
        document.addEventListener("DOMContentLoaded", () => {
            customTableInstance = new CustomTable();
        })
    </script>

    {{-- Agregar usuario --}}
    <script>
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Limpiar errores previos
            this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            this.querySelectorAll('.invalid-feedback').forEach(el => {
                el.textContent = '';
                el.style.display = 'none';
            });
            
            // Validar que haya un rol seleccionado
            const roleChecked = document.querySelector('.role-checkbox:checked');
            const roleError = document.getElementById('roleError');
            
            if (!roleChecked) {
                roleError.classList.remove('d-none');
                return;
            } else {
                roleError.classList.add('d-none');
            }
            
            let formData = new FormData(this);

            fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                })
                .then(async res => {
                    const data = await res.json().catch(() => null);

                    if (!res.ok) {
                        if (data && data.errors) {
                            // Mostrar errores para cada campo
                            Object.keys(data.errors).forEach(key => {
                                const input = document.getElementById(key);
                                if (input) {
                                    input.classList.add('is-invalid');
                                    
                                    // Buscar el div de error (puede estar como nextSibling o dentro del parent)
                                    let feedback = input.parentElement.querySelector('.invalid-feedback');
                                    
                                    if (!feedback) {
                                        // Si no está en el parent, buscar como hermano
                                        feedback = input.nextElementSibling;
                                        if (feedback && !feedback.classList.contains('invalid-feedback')) {
                                            feedback = input.parentElement.nextElementSibling;
                                        }
                                    }
                                    
                                    if (feedback && feedback.classList.contains('invalid-feedback')) {
                                        feedback.textContent = data.errors[key][0];
                                        feedback.style.display = 'block';
                                    }
                                }
                            });
                        }
                        return;
                    }

                    const modalEl = document.getElementById('addUserModal');
                    const modal = bootstrap.Modal.getInstance(modalEl);
                    modal.hide();
                    this.reset();

                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Usuario creado exitosamente',
                        showConfirmButton: false,
                        timer: 2000,
                        toast: true
                    }).then(() => {
                        window.location.reload();
                    });
                })
                .catch(err => console.log(err));
        });
    </script>

    {{-- Editar usuario --}}
    <script>
        document.querySelectorAll('.edit-user-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const userId = this.dataset.id;

                const res = await fetch(`/admin/users/${userId}/edit`);
                const data = await res.json();

                document.getElementById('editUserId').value = data.id;
                document.getElementById('editName').value = data.name;
                document.getElementById('editEmail').value = data.email;

                // 👇 Cargar avatar actual
                const editAvatarImage = document.getElementById('editAvatarImage');
                const removeEditAvatarBtn = document.getElementById('removeEditAvatar');
                
                if (data.profile_photo_path) {
                    editAvatarImage.src = `/storage/avatars/${data.profile_photo_path}`;
                    removeEditAvatarBtn.classList.remove('d-none');
                } else {
                    editAvatarImage.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(data.name)}&background=random&size=200`;
                    removeEditAvatarBtn.classList.add('d-none');
                }

                // 👇 Llenar roles con checkboxes
                const rolesContainer = document.getElementById('editRolesContainer');
                rolesContainer.innerHTML = '';

                data.all_roles.forEach(role => {
                    const isChecked = data.roles.includes(role) ? 'checked' : '';
                    const roleDiv = `
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input edit-role-checkbox" 
                                       type="checkbox" 
                                       name="role" 
                                       value="${role}" 
                                       id="edit_role_${role}"
                                       ${isChecked}>
                                <label class="form-check-label" for="edit_role_${role}">
                                    ${role}
                                </label>
                            </div>
                        </div>
                    `;
                    rolesContainer.innerHTML += roleDiv;
                });

                // 👇 Configurar comportamiento de checkboxes (solo uno)
                const editRoleCheckboxes = document.querySelectorAll('.edit-role-checkbox');
                editRoleCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        if (this.checked) {
                            editRoleCheckboxes.forEach(cb => {
                                if (cb !== this) {
                                    cb.checked = false;
                                }
                            });
                            // Ocultar error si hay
                            document.getElementById('editRoleError').classList.add('d-none');
                        }
                    });
                });

                new bootstrap.Modal(document.getElementById('editUserModal')).show();
            });
        });

        document.getElementById('editUserForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Limpiar errores previos
            this.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            this.querySelectorAll('.invalid-feedback').forEach(el => {
                el.style.display = 'none';
            });

            // Validar que haya un rol seleccionado
            const roleChecked = document.querySelector('.edit-role-checkbox:checked');
            const roleError = document.getElementById('editRoleError');
            
            if (!roleChecked) {
                roleError.classList.remove('d-none');
                return;
            } else {
                roleError.classList.add('d-none');
            }

            const userId = document.getElementById('editUserId').value;
            const formData = new FormData(this);

            try {
                const res = await fetch(`/admin/users/${userId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });

                const data = await res.json();

                if (!res.ok) {
                    // Mostrar errores de validación
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            // Mapear nombres de campo a IDs de input
                            const inputMap = {
                                'name': 'editName',
                                'email': 'editEmail',
                                'password': 'editPassword',
                                'avatar': 'editAvatar',
                                'role': 'editRolesContainer'
                            };
                            
                            const inputId = inputMap[key] || ('edit' + key.charAt(0).toUpperCase() + key.slice(1));
                            const input = document.getElementById(inputId);
                            
                            if (input && input.tagName === 'INPUT') {
                                input.classList.add('is-invalid');
                                
                                // Buscar el div de error
                                let feedback = input.parentElement.querySelector('.invalid-feedback');
                                
                                if (!feedback) {
                                    feedback = input.nextElementSibling;
                                    if (feedback && !feedback.classList.contains('invalid-feedback')) {
                                        feedback = input.parentElement.nextElementSibling;
                                        // Si aún no se encuentra, buscar en el contenedor padre
                                        if (feedback && !feedback.classList.contains('invalid-feedback')) {
                                            const parent = input.closest('.col-md-6, .col-12');
                                            feedback = parent ? parent.querySelector('.invalid-feedback') : null;
                                        }
                                    }
                                }
                                
                                if (feedback && feedback.classList.contains('invalid-feedback')) {
                                    feedback.textContent = data.errors[key][0];
                                    feedback.style.display = 'block';
                                }
                            }
                        });
                    }
                    return;
                }

                if (data.success) {
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Usuario actualizado exitosamente',
                        showConfirmButton: false,
                        timer: 2000,
                        toast: true
                    }).then(() => {
                        window.location.reload();
                    });
                }
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Ocurrió un error al actualizar el usuario', 'error');
            }
        });
    </script>

    {{-- Ver usuario --}}
    <script>
        document.querySelectorAll('.view-user-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.dataset.id;

                fetch(`/admin/users/${userId}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.user) {
                            document.getElementById('viewUserAvatar').src = data.user.profile_photo_path ?
                                `/storage/avatars/${data.user.profile_photo_path}` :
                                `https://ui-avatars.com/api/?name=${encodeURIComponent(data.user.name)}&background=random`;
                            document.getElementById('viewUserName').innerText = data.user.name;
                            document.getElementById('viewUserEmail').innerText = data.user.email;
                            document.getElementById('viewUserStatus').innerText = data.user.email_verified_at ? 'Activo' : 'Inactivo';
                            document.getElementById('viewUserStatus').className = data.user.email_verified_at ? 
                                'badge badge-soft-success mb-3' : 'badge badge-soft-danger mb-3';
                            document.getElementById('viewUserId').innerText = data.user.id;
                            document.getElementById('viewUserCreatedAt').innerText = new Date(data.user.created_at)
                                .toLocaleDateString('es-ES', {day: '2-digit', month: 'short', year: 'numeric'});

                            const modalEl = document.getElementById('viewUserModal');
                            const modal = new bootstrap.Modal(modalEl);
                            modal.show();
                        }
                    })
                    .catch(err => console.error(err));
            });
        });
    </script>

    {{-- Eliminar y restaurar usuario --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.delete-user-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = this.dataset.id;

                    Swal.fire({
                        title: 'Eliminar Usuario',
                        text: '¿Seguro que deseas eliminar este usuario?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/admin/users/${userId}`, {
                                    method: 'DELETE',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    Swal.fire('Usuario eliminado', data.message, 'success').then(() => {
                                        window.location.reload();
                                    });
                                })
                                .catch(err => Swal.fire('Error', 'No se pudo eliminar', 'error'));
                        }
                    });
                });
            });

            document.querySelectorAll('.restore-user-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const userId = this.dataset.id;

                    Swal.fire({
                        title: 'Restaurar Usuario',
                        text: '¿Deseas restaurar este usuario?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, restaurar',
                        cancelButtonText: 'Cancelar',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/admin/users/${userId}/restore`, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json'
                                    }
                                })
                                .then(res => res.json())
                                .then(data => {
                                    Swal.fire('Usuario restaurado', data.message, 'success').then(() => {
                                        window.location.reload();
                                    });
                                })
                                .catch(err => Swal.fire('Error', 'No se pudo restaurar', 'error'));
                        }
                    });
                });
            });
        });
    </script>

    {{-- CustomTable Class --}}
    <script>
        class Table {
            constructor(table, parentInstance) {
                this.table = table;
                this.parentInstance = parentInstance;
                this.thead = table.querySelector('thead');
                this.tbody = table.querySelector('tbody');
                this.headers = this.thead ? Array.from(this.thead.querySelectorAll('th')) : [];
                this.rows = Array.from(this.tbody.querySelectorAll('tr'));
                this.filteredRows = [...this.rows];
                this.rowsPerPage = parseInt(table.getAttribute(this.parentInstance.rowsPerPageAttribute) ?? this.parentInstance.rowsPerPage);
                this.currentPage = this.parentInstance.currentPage;
                this.searchInput = null;
                this.pagination = null;
                this.paginationInfo = null;
                this.filters = [];
                this.itemNotFoundMessage = 'No se encontraron resultados';
            }

            get totalPages() {
                return Math.ceil(this.filteredRows.length / this.rowsPerPage) || 1;
            }

            init() {
                this.setupSearch();
                if (this.headers.length > 0) {
                    this.setupFilters();
                    this.setupSort();
                }
                this.setupRowsPerPage();
                this.setupPagination();
                this.setupPaginationInfo();
                this.update();
            }

            setupSearch() {
                this.searchInput = this.table.querySelector(this.parentInstance.searchSelector);
                if (this.searchInput) {
                    this.searchInput.addEventListener('keyup', (e) => {
                        const value = e.target.value.toLowerCase();
                        this.filteredRows = this.rows.filter(row => {
                            const cells = Array.from(row.querySelectorAll('td'));
                            return cells.some(cell => cell.textContent.toLowerCase().includes(value));
                        });
                        this.currentPage = 1;
                        this.update();
                    });
                }
            }

            setupFilters() {
                this.filters = Array.from(this.table.querySelectorAll(this.parentInstance.filterSelector));
                this.filters.forEach(filter => {
                    filter.addEventListener('change', () => {
                        this.applyFilters();
                        this.currentPage = 1;
                        this.update();
                    });
                });
            }

            applyFilters() {
                const matchesFilters = row => {
                    return this.filters.every(filter => {
                        const selectedValue = filter.value;
                        if (!selectedValue) return true;

                        const column = filter.dataset.tableFilter;
                        const headerIndex = this.headers.findIndex(th => th.dataset.column === column);
                        if (headerIndex === -1) return true;

                        const cell = row.children[headerIndex];
                        if (!cell) return true;

                        return cell.textContent.trim().toLowerCase() === selectedValue.toLowerCase();
                    });
                };

                this.filteredRows = this.rows.filter(row => matchesFilters(row));
            }

            setupRowsPerPage() {
                const rowsPerPageSelect = this.table.querySelector(this.parentInstance.rowsPerPageSelector);
                if (rowsPerPageSelect) {
                    rowsPerPageSelect.addEventListener('change', (e) => {
                        this.rowsPerPage = parseInt(e.target.value);
                        this.update();
                    });
                }
            }

            setupPagination() {
                this.pagination = this.table.querySelector(this.parentInstance.paginationSelector);
            }

            renderTablePage(page) {
                this.tbody.innerHTML = '';
                const start = (page - 1) * this.rowsPerPage;
                const end = start + this.rowsPerPage;
                const pageRows = this.filteredRows.slice(start, end);

                if (pageRows.length === 0) {
                    const columnCount = this.table.querySelectorAll('thead th').length;
                    const messageRow = document.createElement('tr');
                    messageRow.className = 'no-results';
                    messageRow.innerHTML = `<td colspan="${columnCount}" class="text-center text-muted py-3">${this.itemNotFoundMessage}</td>`;
                    this.tbody.appendChild(messageRow);
                } else {
                    pageRows.forEach(row => this.tbody.appendChild(row));
                }
            }

            renderPagination() {
                const ul = document.createElement('ul');
                ul.className = 'pagination pagination-sm pagination-boxed mb-0 justify-content-center';
                this.pagination.innerHTML = '';
                const totalPages = this.totalPages;

                const prev = document.createElement('li');
                prev.className = `page-item ${this.currentPage === 1 ? 'disabled' : ''}`;
                prev.innerHTML = `<a href="#" class="page-link"><i class="ti ti-chevron-left"></i></a>`;
                prev.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (this.currentPage > 1) {
                        this.currentPage--;
                        this.update();
                    }
                });
                ul.appendChild(prev);

                for (let i = 1; i <= totalPages; i++) {
                    const li = document.createElement('li');
                    li.className = `page-item ${this.currentPage === i ? 'active' : ''}`;
                    li.innerHTML = `<a href="#" class="page-link">${i}</a>`;
                    li.addEventListener('click', (e) => {
                        e.preventDefault();
                        this.currentPage = i;
                        this.update();
                    });
                    ul.appendChild(li);
                }

                const next = document.createElement('li');
                next.className = `page-item ${this.currentPage === totalPages ? 'disabled' : ''}`;
                next.innerHTML = `<a href="#" class="page-link"><i class="ti ti-chevron-right"></i></a>`;
                next.addEventListener('click', (e) => {
                    e.preventDefault();
                    if (this.currentPage < this.totalPages) {
                        this.currentPage++;
                        this.update();
                    }
                });
                ul.appendChild(next);

                this.pagination.appendChild(ul);
                this.setupPaginationInfo();
            }

            setupPaginationInfo() {
                this.paginationInfo = this.table.querySelector(this.parentInstance.paginationInfoSelector);
                if (this.paginationInfo) {
                    const startRow = (this.currentPage - 1) * this.rowsPerPage + 1;
                    const endRow = Math.min(this.currentPage * this.rowsPerPage, this.filteredRows.length);
                    this.paginationInfo.innerHTML = `Mostrando <span class="fw-semibold">${startRow}</span> a <span class="fw-semibold">${endRow}</span> de <span class="fw-semibold">${this.filteredRows.length}</span> usuarios`;
                }
            }

            update() {
                this.renderTablePage(this.currentPage);
                if (this.pagination) {
                    const hasRows = this.filteredRows.length > 0;
                    this.pagination.style.display = hasRows ? 'block' : 'none';
                    if (this.paginationInfo) {
                        this.paginationInfo.style.display = hasRows ? 'block' : 'none';
                    }
                    if (hasRows) this.renderPagination();
                }
            }

            setupSort() {
                const sortItems = this.table.querySelectorAll(this.parentInstance.sortSelector);
                sortItems.forEach(header => {
                    header.style.cursor = 'pointer';
                    let icon = header.querySelector('i');
                    if (!icon) {
                        icon = document.createElement('i');
                        icon.className = 'ti ti-arrows-sort fs-xs ms-1';
                        header.appendChild(icon);
                    }

                    header.addEventListener('click', e => {
                        e.preventDefault();
                        const columnIndex = Array.from(header.parentElement.children).indexOf(header);
                        if (columnIndex === -1) return;

                        const currentDirection = header.dataset.direction;
                        const newDirection = currentDirection === 'asc' ? 'desc' : 'asc';
                        header.dataset.direction = newDirection;

                        icon.className = newDirection === 'asc' ? 'ti ti-arrow-up fs-xs ms-1' : 'ti ti-arrow-down fs-xs ms-1';

                        this.filteredRows.sort((a, b) => {
                            const aText = a.children[columnIndex]?.textContent.trim().toLowerCase() || '';
                            const bText = b.children[columnIndex]?.textContent.trim().toLowerCase() || '';
                            return newDirection === 'asc' ? aText.localeCompare(bText) : bText.localeCompare(aText);
                        });

                        this.currentPage = 1;
                        this.update();
                    });
                });
            }
        }

        class CustomTable {
            constructor({
                tableSelector = '[data-table]',
                searchSelector = '[data-table-search]',
                filterSelector = 'select[data-table-filter], input[data-table-filter]',
                rowsPerPageSelector = '[data-table-set-rows-per-page]',
                rowsPerPageAttribute = 'data-table-rows-per-page',
                paginationSelector = '[data-table-pagination]',
                sortSelector = '[data-table-sort]',
                paginationInfoSelector = '[data-table-pagination-info]',
                rowsPerPage = 10,
                currentPage = 1,
            } = {}) {
                this.tableSelector = tableSelector;
                this.searchSelector = searchSelector;
                this.filterSelector = filterSelector;
                this.rowsPerPageSelector = rowsPerPageSelector;
                this.rowsPerPageAttribute = rowsPerPageAttribute;
                this.paginationSelector = paginationSelector;
                this.sortSelector = sortSelector;
                this.paginationInfoSelector = paginationInfoSelector;
                this.rowsPerPage = rowsPerPage;
                this.currentPage = currentPage;
                this.tables = [];
                this.init();
            }

            init() {
                const tableElements = document.querySelectorAll(this.tableSelector);
                tableElements.forEach((table) => {
                    const tableInstance = new Table(table, this);
                    this.tables.push(tableInstance);
                    tableInstance.init();
                });
            }
        }
    </script>
@endsection