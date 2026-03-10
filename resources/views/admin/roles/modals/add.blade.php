<!-- Modal Agregar Rol -->
<div class="modal fade" id="addRoleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            
            <div class="modal-header">
                <h5 class="modal-title">Crear Nuevo Rol</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="addRoleForm" action="{{ route('admin.roles.store') }}" method="POST" novalidate>
                @csrf
                <div class="modal-body">

                    <div class="mb-3">
                        <label for="role_name" class="form-label">
                            Nombre del Rol <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="role_name" 
                               name="name" 
                               placeholder="Ej: Administrador, Editor, Visitante"
                               required>
                        <div class="invalid-feedback">El nombre del rol es obligatorio</div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Guardar Rol
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>