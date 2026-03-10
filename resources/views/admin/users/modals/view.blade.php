<!-- Modal Ver Usuario -->
<div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title" id="viewUserModalLabel">Detalles del Usuario</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            
            <div class="modal-body p-4">
                <div class="d-flex flex-column align-items-center text-center">
                    <img id="viewUserAvatar" 
                         src="https://ui-avatars.com/api/?name=Usuario" 
                         alt="Avatar"
                         class="rounded-circle mb-3" 
                         width="120" 
                         height="120">
                    <h4 id="viewUserName" class="mb-1">Nombre Usuario</h4>
                    <p id="viewUserEmail" class="text-muted mb-2">usuario@example.com</p>
                    <span id="viewUserStatus" class="badge badge-soft-success mb-3">Activo</span>
                </div>

                <hr>

                <div class="row text-center">
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">ID</h6>
                        <p id="viewUserId">123</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6 class="text-muted">Fecha de Creación</h6>
                        <p id="viewUserCreatedAt">01 Ene 2025</p>
                    </div>
                </div>

                <div class="text-center mt-3">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
</div>