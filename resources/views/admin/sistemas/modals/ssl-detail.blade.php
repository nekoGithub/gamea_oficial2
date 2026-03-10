<!-- Modal Detalle de SSL -->
<div class="modal fade" id="sslDetailModal" tabindex="-1" aria-labelledby="sslDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header bg-gradient" id="sslModalHeader">
                <h5 class="modal-title d-flex align-items-center text-white" id="sslDetailModalLabel">
                    <i class="ti ti-shield-check fs-3 me-2"></i>
                    <div>
                        <div class="fw-bold">Certificado SSL</div>
                        <small class="opacity-75" id="sslDominioTitle">Cargando...</small>
                    </div>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body p-4">
                
                <!-- Estado del Certificado -->
                <div class="text-center mb-4">
                    <div id="sslEstadoIcon" class="mb-3">
                        <!-- Se llenará dinámicamente -->
                    </div>
                    <h4 class="mb-1" id="sslEstadoTexto">—</h4>
                    <p class="text-muted mb-0" id="sslEstadoDescripcion">—</p>
                </div>

                <!-- Información del Certificado -->
                <div class="card bg-light border-0 mb-3">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="text-muted small mb-1">
                                    <i class="ti ti-world me-1"></i>
                                    Dominio
                                </label>
                                <div class="fw-semibold" id="sslDominio">—</div>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small mb-1">
                                    <i class="ti ti-calendar me-1"></i>
                                    Fecha de Emisión
                                </label>
                                <div id="sslFechaEmision">—</div>
                            </div>
                            <div class="col-6">
                                <label class="text-muted small mb-1">
                                    <i class="ti ti-calendar-event me-1"></i>
                                    Fecha de Vencimiento
                                </label>
                                <div id="sslFechaVencimiento">—</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tiempo Restante -->
                <div class="card border-0" id="sslTiempoCard">
                    <div class="card-body text-center">
                        <div class="display-4 fw-bold mb-2" id="sslDiasRestantes">—</div>
                        <p class="text-muted mb-0" id="sslDiasTexto">—</p>
                    </div>
                </div>

                <!-- Información Adicional -->
                <div class="mt-3" id="sslInfoAdicional">
                    <!-- Se llenará dinámicamente -->
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
    .bg-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .pulse-icon {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
            opacity: 1;
        }
        50% {
            transform: scale(1.1);
            opacity: 0.8;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sslModal = document.getElementById('sslDetailModal');
    
    if (sslModal) {
        sslModal.addEventListener('show.bs.modal', async function(event) {
            const button = event.relatedTarget;
            const sslId = button.getAttribute('data-ssl-id');
            const sslEmisor = button.getAttribute('data-ssl-emisor');
            const sslExpiracion = button.getAttribute('data-ssl-expiracion');
            const diasRestantes = parseInt(button.getAttribute('data-ssl-dias'));
            
            // Actualizar título
            document.getElementById('sslDominioTitle').textContent = sslEmisor;
            
            try {
                const response = await fetch(`/admin/ssls/${sslId}/detalle`);
                const data = await response.json();
                
                if (data.success) {
                    const ssl = data.ssl;
                    const dias = diasRestantes;
                    
                    // Determinar estado
                    let estado, estadoTexto, estadoDesc, iconHtml, headerClass, cardClass;
                    
                    if (dias < 0) {
                        estado = 'vencido';
                        estadoTexto = 'Certificado Vencido';
                        estadoDesc = 'Este certificado ha expirado y debe ser renovado';
                        iconHtml = '<i class="ti ti-shield-x text-danger pulse-icon" style="font-size: 5rem;"></i>';
                        headerClass = 'bg-danger';
                        cardClass = 'border-danger';
                    } else if (dias <= 7) {
                        estado = 'critico';
                        estadoTexto = 'Vencimiento Crítico';
                        estadoDesc = 'El certificado está por vencer muy pronto';
                        iconHtml = '<i class="ti ti-alert-triangle text-danger pulse-icon" style="font-size: 5rem;"></i>';
                        headerClass = 'bg-danger';
                        cardClass = 'border-danger';
                    } else if (dias <= 30) {
                        estado = 'advertencia';
                        estadoTexto = 'Próximo a Vencer';
                        estadoDesc = 'Considera renovar el certificado pronto';
                        iconHtml = '<i class="ti ti-alert-circle text-warning" style="font-size: 5rem;"></i>';
                        headerClass = 'bg-warning';
                        cardClass = 'border-warning';
                    } else {
                        estado = 'valido';
                        estadoTexto = 'Certificado Válido';
                        estadoDesc = 'El certificado está activo y funcionando correctamente';
                        iconHtml = '<i class="ti ti-shield-check text-success" style="font-size: 5rem;"></i>';
                        headerClass = 'bg-success';
                        cardClass = 'border-success';
                    }
                    
                    // Actualizar header
                    const header = document.getElementById('sslModalHeader');
                    header.className = `modal-header ${headerClass} bg-opacity-75`;
                    
                    // Actualizar estado
                    document.getElementById('sslEstadoIcon').innerHTML = iconHtml;
                    document.getElementById('sslEstadoTexto').textContent = estadoTexto;
                    document.getElementById('sslEstadoDescripcion').textContent = estadoDesc;
                    
                    // Información del certificado
                    document.getElementById('sslDominio').innerHTML = `<code class="fs-6">${ssl.emisor}</code>`;
                    
                    const fechaEmision = new Date(ssl.fecha_emision).toLocaleDateString('es-ES', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    document.getElementById('sslFechaEmision').innerHTML = `<strong>${fechaEmision}</strong>`;
                    
                    const fechaVenc = new Date(ssl.fecha_expiracion).toLocaleDateString('es-ES', {
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric'
                    });
                    document.getElementById('sslFechaVencimiento').innerHTML = `<strong>${fechaVenc}</strong>`;
                    
                    // Tiempo restante
                    const tiempoCard = document.getElementById('sslTiempoCard');
                    tiempoCard.className = `card ${cardClass} bg-opacity-10`;
                    
                    if (dias < 0) {
                        const diasVencidos = Math.abs(dias);
                        document.getElementById('sslDiasRestantes').innerHTML = `<span class="text-danger">${diasVencidos}</span>`;
                        document.getElementById('sslDiasTexto').innerHTML = `<strong>días vencido</strong>`;
                    } else {
                        const colorClass = dias <= 7 ? 'text-danger' : (dias <= 30 ? 'text-warning' : 'text-success');
                        document.getElementById('sslDiasRestantes').innerHTML = `<span class="${colorClass}">${dias}</span>`;
                        document.getElementById('sslDiasTexto').innerHTML = `<strong>días restantes</strong>`;
                    }
                    
                    // Información adicional
                    let infoHtml = '';
                    if (ssl.proveedor) {
                        infoHtml += `
                            <div class="alert alert-info border-0 d-flex align-items-center">
                                <i class="ti ti-building-store fs-4 me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Proveedor</small>
                                    <strong>${ssl.proveedor}</strong>
                                </div>
                            </div>
                        `;
                    }
                    
                    if (ssl.tipo) {
                        infoHtml += `
                            <div class="alert alert-secondary border-0 d-flex align-items-center mt-2">
                                <i class="ti ti-certificate fs-4 me-2"></i>
                                <div>
                                    <small class="text-muted d-block">Tipo de Certificado</small>
                                    <strong>${ssl.tipo}</strong>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Recomendación
                    if (dias <= 30 && dias >= 0) {
                        infoHtml += `
                            <div class="alert alert-warning border-warning border-opacity-25 mt-2">
                                <div class="d-flex align-items-start">
                                    <i class="ti ti-bulb fs-4 me-2 mt-1"></i>
                                    <div>
                                        <strong>Recomendación</strong>
                                        <p class="mb-0 small mt-1">
                                            Es momento de iniciar el proceso de renovación del certificado para evitar interrupciones en el servicio.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                    } else if (dias < 0) {
                        infoHtml += `
                            <div class="alert alert-danger border-danger border-opacity-25 mt-2">
                                <div class="d-flex align-items-start">
                                    <i class="ti ti-alert-triangle fs-4 me-2 mt-1"></i>
                                    <div>
                                        <strong>Acción Requerida</strong>
                                        <p class="mb-0 small mt-1">
                                            El certificado ha vencido. Renueva inmediatamente para mantener la seguridad del sitio.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                    
                    document.getElementById('sslInfoAdicional').innerHTML = infoHtml;
                    
                } else {
                    throw new Error('Error al cargar datos');
                }
                
            } catch (error) {
                console.error('Error:', error);
                document.getElementById('sslEstadoIcon').innerHTML = `
                    <i class="ti ti-alert-circle text-danger" style="font-size: 5rem;"></i>
                `;
                document.getElementById('sslEstadoTexto').textContent = 'Error al cargar';
                document.getElementById('sslEstadoDescripcion').textContent = 'No se pudo obtener la información del certificado';
            }
        });
    }
});
</script>