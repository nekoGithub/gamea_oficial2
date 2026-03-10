{{-- MODAL VER DETALLE --}}
<div class="modal fade" id="showAuditoriaModal" tabindex="-1" aria-labelledby="showAuditoriaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="showAuditoriaModalLabel">
                    <i class="ti ti-file-description me-2"></i>Detalle de Auditoría
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">

                {{-- Información General --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">ID</label>
                        <p id="detalle-id" class="form-control-plaintext">—</p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Usuario</label>
                        <p id="detalle-usuario" class="form-control-plaintext">—</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Acción</label>
                        <p id="detalle-accion" class="form-control-plaintext">—</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Módulo</label>
                        <p id="detalle-modulo" class="form-control-plaintext">—</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Entidad ID</label>
                        <p id="detalle-entidad-id" class="form-control-plaintext">—</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">Descripción</label>
                        <p id="detalle-descripcion" class="form-control-plaintext">—</p>
                    </div>
                </div>

                <hr>

                {{-- Información Técnica --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">IP Address</label>
                        <p id="detalle-ip" class="form-control-plaintext"><code>—</code></p>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Fecha/Hora</label>
                        <p id="detalle-fecha" class="form-control-plaintext">—</p>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">User Agent</label>
                        <p id="detalle-user-agent" class="form-control-plaintext text-muted small">—</p>
                    </div>
                </div>

                <hr>

                {{-- Valores Anteriores y Nuevos --}}
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Valores Anteriores</label>
                        <pre id="detalle-valores-anteriores" class="bg-light p-3 rounded" style="max-height: 200px; overflow-y: auto;">—</pre>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Valores Nuevos</label>
                        <pre id="detalle-valores-nuevos" class="bg-light p-3 rounded" style="max-height: 200px; overflow-y: auto;">—</pre>
                    </div>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>