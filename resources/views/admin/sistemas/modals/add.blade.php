<style>
    .bg-soft {
        background-color: #fcfcfd;
        border: 1px solid #eef0f4;
        box-shadow: 0 12px 30px rgba(0, 0, 0, .05);
    }

    [data-bs-theme="dark"] .bg-soft,
    .dark .bg-soft,
    [class*="dark"] .bg-soft {
        background-color: #1a1d21 !important;
        border-color: #2d3035 !important;
        box-shadow: 0 12px 30px rgba(0, 0, 0, .2) !important;
    }
</style>

<!-- Modal Agregar Sistema -->
<div class="modal fade" id="addSistemaModal" tabindex="-1" aria-labelledby="addSistemaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addSistemaModalLabel">
                    <i class="ti ti-server me-2"></i>
                    Agregar Sistema
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <form id="addSistemaForm" action="{{ route('admin.sistemas.store') }}" method="POST" novalidate>
                @csrf
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
                                        <input type="text" class="form-control" name="nombre"
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
                                        <input type="text" class="form-control" name="sigla"
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
                                        <input type="text" class="form-control" name="dominio" id="addDominio"
                                            placeholder="Ej. sistema.miempresa.com" required maxlength="150">
                                    </div>
                                    <small class="text-muted">Sin http:// ni https://</small>
                                    <div class="invalid-feedback">El dominio es obligatorio y debe ser único.</div>
                                </div>

                                <!-- Tipo de Sistema -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold d-block mb-2">
                                        Tipo de Sistema <span class="text-danger">*</span>
                                    </label>
                                    <div class="d-flex gap-3" id="tipoCheckboxContainer">
                                        <div class="form-check">
                                            <input class="form-check-input tipo-checkbox" type="checkbox" name="tipo[]"
                                                value="interno" id="tipo-interno" checked>
                                            <label class="form-check-label" for="tipo-interno">
                                                <i class="ti ti-lock text-info me-1"></i>
                                                <strong>Interno</strong>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input tipo-checkbox" type="checkbox" name="tipo[]"
                                                value="externo" id="tipo-externo">
                                            <label class="form-check-label" for="tipo-externo">
                                                <i class="ti ti-world text-warning me-1"></i>
                                                <strong>Externo</strong>
                                            </label>
                                        </div>
                                    </div>
                                    <small class="text-danger" id="tipoError" style="display: none;">
                                        Debes seleccionar al menos un tipo de sistema.
                                    </small>
                                </div>

                                <!-- Estado -->
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold d-block">
                                        Estado <span class="text-danger">*</span>
                                    </label>
                                    <div class="btn-group w-100" role="group" aria-label="Estado">
                                        <input type="radio" class="btn-check" name="estado" id="estado-activo"
                                            value="activo" checked>
                                        <label class="btn btn-outline-secondary" for="estado-activo">
                                            <i class="ti ti-check me-1"></i> Activo
                                        </label>
                                        <input type="radio" class="btn-check" name="estado" id="estado-inactivo"
                                            value="inactivo">
                                        <label class="btn btn-outline-danger" for="estado-inactivo">
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
                                        <select class="form-select" name="unidad_id" id="addUnidadId" required>
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
                                        Certificado SSL <span class="text-muted">(Opcional)</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ti ti-shield-check"></i>
                                        </span>
                                        <select class="form-select" name="ssl_id" id="addSslId">
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
                                        <textarea class="form-control" name="descripcion" rows="3"
                                            placeholder="Ej. Sistema para gestión de usuarios internos..."></textarea>
                                    </div>

                                    <small class="text-muted">Descripción breve del sistema</small>
                                </div>

                            </div>
                        </div>

                        <!-- Columna Derecha: Previews -->
                        <div class="col-md-5">
                            <div class="border rounded-4 p-3 bg-soft h-100">
                                <h6 class="text-muted mb-3">
                                    <i class="ti ti-info-circle me-1"></i>
                                    Vista Previa
                                </h6>
                                <div id="addUnidadPreview">
                                    <div class="text-center text-muted py-4">
                                        <i class="ti ti-building-off fs-1 opacity-50"></i>
                                        <p class="mb-0 mt-2 small">Selecciona una unidad para ver sus responsables</p>
                                    </div>
                                </div>
                                <hr class="my-3">
                                <div id="addSslPreview">
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
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ti ti-device-floppy me-1"></i>
                        Guardar Sistema
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // ── Helper: quitar protocolo del dominio ──────────────────────────────
        function limpiarDominio(valor) {
            return valor
                .replace(/^https?:\/\//i, '')
                .replace(/^\/\//, '')
                .trim();
        }

        // ── Auto-uppercase en sigla ───────────────────────────────────────────
        document.querySelector('[name="sigla"]')?.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });

        // ── Limpiar protocolo al escribir (addDominio) ────────────────────────
        document.getElementById('addDominio')?.addEventListener('input', function() {
            const pos = this.selectionStart;
            this.value = limpiarDominio(this.value);
            // Restaurar cursor si es posible
            try {
                this.setSelectionRange(pos, pos);
            } catch (e) {}
        });

        document.getElementById('addDominio')?.addEventListener('paste', function(e) {
            e.preventDefault();
            const texto = (e.clipboardData || window.clipboardData).getData('text');
            this.value = limpiarDominio(texto);
        });

        // ── Preview de Unidad al seleccionar ─────────────────────────────────
        document.getElementById('addUnidadId')?.addEventListener('change', async function() {
            const unidadId = this.value;
            const previewContainer = document.getElementById('addUnidadPreview');

            if (!unidadId) {
                previewContainer.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="ti ti-building-off fs-1 opacity-50"></i>
                    <p class="mb-0 mt-2 small">Selecciona una unidad organizacional para ver sus responsables</p>
                </div>`;
                return;
            }

            previewContainer.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-lg text-black" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>`;

            try {
                const response = await fetch(`/admin/unidades/${unidadId}/detalle`);
                const data = await response.json();

                if (data.success) {
                    const unidad = data.unidad;
                    let html = `
                    <div class="mb-3">
                        <span class="text-muted small">Unidad Organizacional seleccionada</span>
                        <div class="fw-semibold">${unidad.nombre}</div>
                        ${unidad.codigo ? `<div class="text-muted small">${unidad.codigo}</div>` : ''}
                    </div>`;

                    if (data.responsables?.length) {
                        html += `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">Responsables</span>
                        <span class="badge bg-secondary-subtle text-secondary">${data.responsables.length}</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">`;
                        data.responsables.forEach(resp => {
                            html += `<span class="badge bg-dark">${resp.nombre}</span>`;
                        });
                        html += `</div>`;
                    } else {
                        html += `
                    <div class="bg-body-tertiary border rounded-3 p-3 text-center text-muted small">
                        <i class="ti ti-user-off d-block mb-1"></i>
                        Sin responsables asignados
                    </div>`;
                    }

                    previewContainer.innerHTML = html;
                }
            } catch (error) {
                console.error(error);
                previewContainer.innerHTML = `
                <div class="bg-body-tertiary border rounded-3 p-3 text-center text-muted small">
                    <i class="ti ti-alert-circle d-block mb-1"></i>
                    No se pudo cargar la información
                </div>`;
            }
        });

        // ── Preview de SSL al seleccionar ─────────────────────────────────────
        document.getElementById('addSslId')?.addEventListener('change', async function() {
            const sslId = this.value;
            const previewContainer = document.getElementById('addSslPreview');

            if (!sslId) {
                previewContainer.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="ti ti-shield-off fs-1 opacity-50"></i>
                    <p class="mb-0 mt-2 small">Selecciona un SSL para ver su estado</p>
                </div>`;
                return;
            }

            previewContainer.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-lg text-dark" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </div>`;

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
                    </div>`;

                    if (diasRestantes <= 30 && diasRestantes >= 0) {
                        html += `<div class="alert alert-warning py-2 mb-0">
                        <small><i class="ti ti-info-circle me-1"></i>Considera renovar pronto</small>
                    </div>`;
                    } else if (diasRestantes < 0) {
                        html += `<div class="alert alert-danger py-2 mb-0">
                        <small><i class="ti ti-alert-triangle me-1"></i>Requiere renovación inmediata</small>
                    </div>`;
                    }

                    previewContainer.innerHTML = html;
                }
            } catch (error) {
                console.error('Error:', error);
                previewContainer.innerHTML = `
                <div class="alert alert-danger py-2">
                    <small>Error al cargar información</small>
                </div>`;
            }
        });

    });
</script>
