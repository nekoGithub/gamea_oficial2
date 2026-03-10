<!-- Modal Detalle de Unidad -->
<div class="modal fade" id="unidadDetailModal" tabindex="-1" aria-labelledby="unidadDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-purple text-white">
                <h5 class="modal-title d-flex align-items-center" id="unidadDetailModalLabel">
                    <i class="ti ti-building fs-3 me-2"></i>
                    <div>
                        <div class="fw-bold" id="unidadNombreTitle">Cargando...</div>
                        <small class="opacity-75">Información de la Unidad Organizacional</small>
                    </div>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-4">
                
                <div id="unidadDetailContent">
                    <!-- Loading state -->
                    <div class="text-center py-5" id="unidadLoading">
                        <div class="spinner-border text-purple" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="text-muted mt-2">Cargando información...</p>
                    </div>

                    <!-- Content loaded -->
                    <div class="d-none" id="unidadContent">
                        
                        <!-- Información General -->
                        <div class="card border-purple border-opacity-25 mb-3">
                            <div class="card-body">
                                <h6 class="text-purple mb-3">
                                    <i class="ti ti-info-circle me-2"></i>
                                    Información General
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="text-muted small">Nombre</label>
                                        <div class="fw-semibold" id="unidadNombre">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">Código</label>
                                        <div class="fw-semibold" id="unidadCodigo">—</div>
                                    </div>
                                    <div class="col-md-12">
                                        <label class="text-muted small">Descripción</label>
                                        <div class="text-secondary" id="unidadDescripcion">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">Estado</label>
                                        <div id="unidadEstado">—</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="text-muted small">Fecha de Creación</label>
                                        <div id="unidadFecha">—</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Responsables -->
                        <div class="card border-purple border-opacity-25">
                            <div class="card-body">
                                <h6 class="text-purple mb-3">
                                    <i class="ti ti-users me-2"></i>
                                    Responsables Asignados
                                </h6>
                                <div id="unidadResponsables">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i>
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

<style>
    .bg-purple {
        background-color: #6f42c1 !important;
    }
    
    .text-purple {
        color: #6f42c1 !important;
    }
    
    .border-purple {
        border-color: #6f42c1 !important;
    }
    
    .btn-outline-purple {
        color: #6f42c1;
        border-color: #6f42c1;
    }
    
    .btn-outline-purple:hover {
        color: #fff;
        background-color: #6f42c1;
        border-color: #6f42c1;
    }
    
    .badge-purple {
        background-color: #6f42c1;
        color: white;
    }
    
    .responsable-card {
        transition: all 0.3s ease;
        border-left: 3px solid #6f42c1;
    }
    
    .responsable-card:hover {
        background-color: #f8f5ff;
        transform: translateX(5px);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const unidadModal = document.getElementById('unidadDetailModal');
    
    if (unidadModal) {
        unidadModal.addEventListener('show.bs.modal', async function(event) {
            const button = event.relatedTarget;
            const unidadId = button.getAttribute('data-unidad-id');
            const unidadNombre = button.getAttribute('data-unidad-nombre');
            
            // Actualizar título
            document.getElementById('unidadNombreTitle').textContent = unidadNombre;
            
            // Mostrar loading
            document.getElementById('unidadLoading').classList.remove('d-none');
            document.getElementById('unidadContent').classList.add('d-none');
            
            try {
                const response = await fetch(`/admin/unidades/${unidadId}/detalle`);
                const data = await response.json();
                
                if (data.success) {
                    const unidad = data.unidad;
                    
                    // Llenar información general
                    document.getElementById('unidadNombre').textContent = unidad.nombre || '—';
                    document.getElementById('unidadCodigo').innerHTML = unidad.sigla ? `<code class="text-purple">${unidad.sigla}</code>` : '—';
                    document.getElementById('unidadDescripcion').textContent = unidad.descripcion || 'Sin descripción';
                    
                    // Estado
                    const estadoBadge = unidad.estado === 'activo' 
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-secondary">Inactivo</span>';
                    document.getElementById('unidadEstado').innerHTML = estadoBadge;
                    
                    // Fecha
                    const fecha = new Date(unidad.created_at).toLocaleDateString('es-ES', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    document.getElementById('unidadFecha').textContent = fecha;
                    
                    // Responsables
                    const responsablesContainer = document.getElementById('unidadResponsables');
                    if (data.responsables && data.responsables.length > 0) {
                        let responsablesHtml = '<div class="row g-3">';
                        
                        data.responsables.forEach(resp => {
                            responsablesHtml += `
                                <div class="col-md-6">
                                    <div class="card responsable-card border-0 shadow-sm">
                                        <div class="card-body p-3">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-purple bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                                    <i class="ti ti-user fs-4 text-purple"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold text-dark">${resp.nombre}</div>
                                                    <small class="text-muted">
                                                        <i class="ti ti-briefcase me-1"></i>
                                                        ${resp.cargo || 'Sin cargo'}
                                                    </small>
                                                </div>
                                            </div>
                                            ${resp.email ? `
                                                <div class="mt-2 pt-2 border-top">
                                                    <small class="text-muted">
                                                        <i class="ti ti-mail me-1"></i>
                                                        ${resp.email}
                                                    </small>
                                                </div>
                                            ` : ''}
                                            ${resp.telefono ? `
                                                <div class="mt-1">
                                                    <small class="text-muted">
                                                        <i class="ti ti-phone me-1"></i>
                                                        ${resp.telefono}
                                                    </small>
                                                </div>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        
                        responsablesHtml += '</div>';
                        responsablesContainer.innerHTML = responsablesHtml;
                    } else {
                        responsablesContainer.innerHTML = `
                            <div class="text-center py-4">
                                <i class="ti ti-user-off fs-1 text-muted opacity-50"></i>
                                <p class="text-muted mb-0 mt-2">No hay responsables asignados</p>
                            </div>
                        `;
                    }
                    
                    // Mostrar contenido
                    document.getElementById('unidadLoading').classList.add('d-none');
                    document.getElementById('unidadContent').classList.remove('d-none');
                } else {
                    throw new Error('Error al cargar datos');
                }
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('unidadLoading').innerHTML = `
                    <div class="text-center py-4">
                        <i class="ti ti-alert-circle fs-1 text-danger"></i>
                        <p class="text-danger mt-2">Error al cargar la información</p>
                    </div>
                `;
            }
        });
    }
});
</script>