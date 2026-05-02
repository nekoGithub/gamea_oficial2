<style>
    .bg-soft {
        background-color: #fcfcfd;
        border: 1px solid #eef0f4;
        box-shadow: 0 12px 30px rgba(0, 0, 0, .05);
    }
</style>

<!-- Modal Editar Sistema -->
<div class="modal fade" id="editSistemaModal" tabindex="-1" aria-labelledby="editSistemaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="editSistemaModalLabel">
                    <i class="ti ti-edit me-2"></i>
                    Editar Sistema
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="editSistemaForm" action="#" method="POST" novalidate>
                @csrf
                @method('PUT')
                <input type="hidden" id="editSistemaId" name="id">

                <div class="modal-body">

                    <div class="mb-3">
                        <small class="text-muted">
                            <span class="text-danger">*</span> Los campos marcados son obligatorios
                        </small>
                    </div>

                    <div class="row">
                        <!-- Columna Izquierda: Formulario -->
                        <div class="col-md-7">
                            <div class="row g-3">

                                <!-- Nombre del Sistema -->
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">
                                        Nombre del Sistema <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ti ti-server"></i>
                                        </span>
                                        <input type="text" class="form-control" name="nombre" id="editNombre"
                                            placeholder="Ej. Portal Institucional" required maxlength="150">
                                    </div>
                                    <div class="invalid-feedback">El nombre del sistema es obligatorio.</div>
                                </div>

                                <!-- Sigla (Opcional) -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Sigla <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ti ti-tag"></i>
                                        </span>
                                        <input type="text" class="form-control" name="sigla" id="editSigla"
                                            placeholder="Ej. GAMEA" maxlength="20" style="text-transform: uppercase;">
                                    </div>
                                    <small class="text-muted">Abreviatura del sistema</small>
                                    <div class="invalid-feedback">La sigla debe ser única.</div>
                                </div>

                                <!-- Dominio -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Dominio <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ti ti-world"></i>
                                        </span>
                                        <input type="text" class="form-control" name="dominio" id="editDominio"
                                            placeholder="Ej. sistema.miempresa.com" required maxlength="150">
                                    </div>
                                    <div class="invalid-feedback">El dominio es obligatorio y debe ser único.</div>
                                </div>

                                <!-- Tipo de Sistema -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold d-block mb-2">
                                        Tipo de Sistema <span class="text-danger">*</span>
                                    </label>

                                    <div class="d-flex gap-3" id="editTipoCheckboxContainer">
                                        <!-- Checkbox Interno -->
                                        <div class="form-check">
                                            <input class="form-check-input edit-tipo-checkbox" type="checkbox"
                                                name="tipo[]" value="interno" id="editTipoInterno">
                                            <label class="form-check-label" for="editTipoInterno">
                                                <i class="ti ti-lock text-info me-1"></i>
                                                <strong>Interno</strong>
                                            </label>
                                        </div>

                                        <!-- Checkbox Externo -->
                                        <div class="form-check">
                                            <input class="form-check-input edit-tipo-checkbox" type="checkbox"
                                                name="tipo[]" value="externo" id="editTipoExterno">
                                            <label class="form-check-label" for="editTipoExterno">
                                                <i class="ti ti-world text-warning me-1"></i>
                                                <strong>Externo</strong>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Mensaje de error -->
                                    <small class="text-danger" id="editTipoError" style="display: none;">
                                        Debes seleccionar al menos un tipo de sistema.
                                    </small>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold d-block">
                                        Estado <span class="text-danger">*</span>
                                    </label>

                                    <div class="btn-group w-100" role="group" aria-label="Estado">
                                        <input type="radio" class="btn-check" name="estado" id="editEstadoActivo"
                                            value="activo">
                                        <label class="btn btn-outline-secondary" for="editEstadoActivo">
                                            <i class="ti ti-check me-1"></i> Activo
                                        </label>

                                        <input type="radio" class="btn-check" name="estado"
                                            id="editEstadoInactivo" value="inactivo">
                                        <label class="btn btn-outline-danger" for="editEstadoInactivo">
                                            <i class="ti ti-ban me-1"></i> Inactivo
                                        </label>
                                    </div>
                                </div>

                                <!-- Unidad -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Unidad Organizacional <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ti ti-building"></i>
                                        </span>
                                        <select class="form-select" name="unidad_id" id="editUnidadId" required>
                                            <option value="">Seleccionar unidad organizacional...</option>
                                            @foreach ($unidades as $unidad)
                                                <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="invalid-feedback">Debes seleccionar una unidad organizacional.</div>
                                </div>

                                <!-- SSL (Opcional) -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Certificado SSL
                                        <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ti ti-shield-check"></i>
                                        </span>
                                        <select class="form-select" name="ssl_id" id="editSslId">
                                            <option value="">Sin certificado SSL</option>
                                            @foreach ($ssls as $ssl)
                                                <option value="{{ $ssl->id }}"
                                                    data-fecha-expiracion="{{ $ssl->fecha_expiracion }}"
                                                    data-emisor="{{ $ssl->emisor }}">
                                                    {{ $ssl->emisor }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <small class="text-muted">Asociar un certificado SSL existente</small>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">
                                        Descripción <span class="text-muted">(Opcional)</span>
                                    </label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ti ti-align-left"></i>
                                        </span>
                                        <textarea class="form-control" name="descripcion" id="editDescripcion" rows="3"
                                            placeholder="Ej. Sistema para gestión de usuarios internos..."></textarea>
                                    </div>

                                    <small class="text-muted">Descripción breve del sistema</small>
                                </div>

                            </div>
                        </div>

                        <!-- Columna Derecha: Previews -->
                        <div class="col-md-5">
                            <div class="border rounded p-3 bg-soft h-100">
                                <h6 class="text-muted mb-3">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Vista Previa
                                </h6>

                                <!-- Preview Unidad -->
                                <div id="editUnidadPreview">
                                    <div class="text-center text-muted py-4">
                                        <i class="ti ti-building-off fs-1 opacity-50"></i>
                                        <p class="mb-0 mt-2 small">Selecciona una unidad para ver sus responsables</p>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <!-- Preview SSL -->
                                <div id="editSslPreview">
                                    <div class="text-center text-muted py-4">
                                        <i class="ti ti-shield-off fs-1 opacity-50"></i>
                                        <p class="mb-0 mt-2 small">Selecciona un SSL para ver su estado</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        Actualizar Sistema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        function limpiarDominio(valor) {
            return valor
                .replace(/^https?:\/\//i, '')
                .replace(/^\/\//, '')
                .trim();
        }

        document.getElementById('editDominio')?.addEventListener('input', function() {
            this.value = limpiarDominio(this.value);
        });
        document.getElementById('editDominio')?.addEventListener('paste', function(e) {
            e.preventDefault();
            this.value = limpiarDominio((e.clipboardData || window.clipboardData).getData('text'));
        });

        // Auto-uppercase en sigla EDIT
        document.getElementById('editSigla')?.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });


        // ================= PREVIEW DE UNIDAD EN EDIT =================
        document.getElementById('editUnidadId')?.addEventListener('change', async function() {
            const unidadId = this.value;
            const previewContainer = document.getElementById('editUnidadPreview');

            if (!unidadId) {
                previewContainer.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="ti ti-building-off fs-1 opacity-50"></i>
                <p class="mb-0 mt-2 small">Selecciona una unidad organizacional para ver sus responsables</p>
            </div>
        `;
                return;
            }

            previewContainer.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-purple" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `;

            try {
                const response = await fetch(`/admin/unidades/${unidadId}/detalle`);
                const data = await response.json();

                if (data.success) {
                    const unidad = data.unidad;
                    let html = `
                <div class="mb-3">
                    <label class="text-muted small">Unidad Organizacional Seleccionada</label>
                    <div class="fw-semibold text-dark">${unidad.nombre}</div>
                    ${unidad.codigo ? `<small class="text-muted">${unidad.codigo}</small>` : ''}
                </div>
            `;

                    if (data.responsables && data.responsables.length > 0) {
                        html += `
                    <label class="text-muted small mb-2">Responsables (${data.responsables.length})</label>
                    <div class="d-flex flex-wrap gap-2">
                `;

                        data.responsables.forEach(resp => {
                            html += `
                        <span class="badge bg-dark">                        
                            ${resp.nombre}
                        </span>
                    `;
                        });

                        html += `</div>`;
                    } else {
                        html += `
                    <div class="alert alert-warning py-2 mb-0">
                        <small><i class="ti ti-alert-triangle me-1"></i>Sin responsables asignados</small>
                    </div>
                `;
                    }

                    previewContainer.innerHTML = html;
                }
            } catch (error) {
                console.error('Error:', error);
                previewContainer.innerHTML = `
            <div class="alert alert-danger py-2">
                <small>Error al cargar información</small>
            </div>
        `;
            }
        });

        // ================= PREVIEW DE SSL EN EDIT =================
        document.getElementById('editSslId')?.addEventListener('change', async function() {
            const sslId = this.value;
            const previewContainer = document.getElementById('editSslPreview');

            if (!sslId) {
                previewContainer.innerHTML = `
            <div class="text-center text-muted py-4">
                <i class="ti ti-shield-off fs-1 opacity-50"></i>
                <p class="mb-0 mt-2 small">Selecciona un SSL para ver su estado</p>
            </div>
        `;
                return;
            }

            previewContainer.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border spinner-border-sm text-success" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `;

            try {
                const response = await fetch(`/admin/ssls/${sslId}/detalle`);
                const data = await response.json();

                if (data.success) {
                    const ssl = data.ssl;
                    const fechaVenc = new Date(ssl.fecha_expiracion);
                    const hoy = new Date();
                    const diasRestantes = Math.floor((fechaVenc - hoy) / (1000 * 60 * 60 * 24));

                    let estadoClass, estadoIcon, estadoTexto, estadoBadge;

                    if (diasRestantes < 0) {
                        estadoClass = 'danger';
                        estadoIcon = 'ti-shield-x';
                        estadoTexto = 'Vencido';
                        estadoBadge = 'bg-danger';
                    } else if (diasRestantes <= 7) {
                        estadoClass = 'danger';
                        estadoIcon = 'ti-alert-triangle';
                        estadoTexto = 'Crítico';
                        estadoBadge = 'bg-danger';
                    } else if (diasRestantes <= 30) {
                        estadoClass = 'warning';
                        estadoIcon = 'ti-alert-circle';
                        estadoTexto = 'Por vencer';
                        estadoBadge = 'bg-warning';
                    } else {
                        estadoClass = 'success';
                        estadoIcon = 'ti-shield-check';
                        estadoTexto = 'Válido';
                        estadoBadge = 'bg-success';
                    }

                    const fechaFormateada = fechaVenc.toLocaleDateString('es-ES', {
                        day: 'numeric',
                        month: 'short',
                        year: 'numeric'
                    });

                    let html = `
                <div class="mb-3">
                    <label class="text-muted small">Certificado SSL</label>
                    <div class="fw-semibold">${ssl.emisor}</div>
                </div>
                
                <div class="card border-${estadoClass} bg-${estadoClass} bg-opacity-10 mb-2">
                    <div class="card-body p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <i class="ti ${estadoIcon} text-${estadoClass} fs-4 me-2"></i>
                                <div>
                                    <span class="badge ${estadoBadge}">${estadoTexto}</span>
                                    <div class="small text-muted mt-1">Vence: ${fechaFormateada}</div>
                                </div>
                            </div>
                            <div class="text-end">
                                <div class="fs-3 fw-bold text-${estadoClass}">${Math.abs(diasRestantes)}</div>
                                <small class="text-muted">${diasRestantes < 0 ? 'días vencido' : 'días restantes'}</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                    if (diasRestantes <= 30 && diasRestantes >= 0) {
                        html += `
                    <div class="alert alert-warning py-2 mb-0">
                        <small><i class="ti ti-info-circle me-1"></i>Considera renovar pronto</small>
                    </div>
                `;
                    } else if (diasRestantes < 0) {
                        html += `
                    <div class="alert alert-danger py-2 mb-0">
                        <small><i class="ti ti-alert-triangle me-1"></i>Requiere renovación inmediata</small>
                    </div>
                `;
                    }

                    previewContainer.innerHTML = html;
                }
            } catch (error) {
                console.error('Error:', error);
                previewContainer.innerHTML = `
            <div class="alert alert-danger py-2">
                <small>Error al cargar información</small>
            </div>
        `;
            }
        });
    });
</script>
