<!DOCTYPE html>
<html lang="en" @yield('html_attribute')>

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('layouts.partials/title-meta')

    @include('layouts.partials/head-css')
</head>

<style>
    /* Tabs con Cyan Institucional El Alto */
    .nav-tabs .nav-link.active {
        color: #26C6DA !important;
        /* Cyan institucional */
        background-color: transparent;

        /* Borde inferior cyan */
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link.active i {
        color: #26C6DA !important;
        /* Icono cyan */
    }

    .nav-tabs .nav-link {
        color: #6c757d;
        /* Gris cuando inactivo */
        border: 1px solid transparent;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        color: #26C6DA;
        /* Cyan al pasar mouse */
        border-color: #e9ecef #e9ecef #dee2e6;
    }

    .nav-tabs .nav-link i {
        color: #6c757d;
        /* Icono gris cuando inactivo */
    }

    .nav-tabs .nav-link:hover i {
        color: #26C6DA;
        /* Icono cyan al pasar mouse */
    }


    
</style>

<body>
    <div class="wrapper">

        @include('layouts.partials/menu')

        <div class="content-page">

            <div class="container-fluid">

                @yield('content')

            </div>

            @include('layouts.partials/footer')

        </div>

    </div>

    @include('layouts.partials/customizer')

    @include('layouts.partials/footer-scripts')

    {{-- resources/views/layouts/vertical.blade.php o app.blade.php --}}

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // SCRIPT OPTIMIZADO PARA NOTIFICACIONES - SIN VIOLACIONES DE RENDIMIENTO
        // Reemplaza el script completo en layouts/vertical.blade.php

        (function() {
            'use strict';

            // Variables en scope del módulo
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            let notificacionActualId = null;
            let modalInstance = null;

            // Cache de elementos del DOM
            const elements = {};

            // Función para cachear elementos del DOM
            function cacheElements() {
                elements.modalElement = document.getElementById('notificacionDetalleModal');
                elements.modalHeader = document.getElementById('modal-header-notif');
                elements.modalIcono = document.getElementById('modal-icono-severidad');
                elements.modalSistema = document.getElementById('modal-sistema-nombre');
                elements.modalFecha = document.getElementById('modal-fecha');
                elements.modalMensaje = document.getElementById('modal-mensaje');
                elements.badgeSeveridad = document.getElementById('modal-badge-severidad');
                elements.badgeEstado = document.getElementById('modal-badge-estado');
                elements.btnReenviar = document.getElementById('reenviar-notificacion-modal-btn');
                elements.btnEliminar = document.getElementById('eliminar-notificacion-modal-btn');
            }

            // Función para limpiar backdrops residuales (optimizada)
            function cleanupBackdrops() {
                // Usar requestAnimationFrame para evitar forced reflow
                requestAnimationFrame(() => {
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(backdrop => backdrop.remove());
                });
            }

            // Función para configurar el header del modal según tipo
            function setupModalHeader(tipo) {
                if (!elements.modalHeader || !elements.modalIcono) return;

                // Limpiar todas las clases de una vez (batch)
                elements.modalHeader.className = 'modal-header';

                const configs = {
                    critica: {
                        class: 'bg-danger-subtle',
                        icon: '<div class="avatar-sm bg-danger rounded-circle d-flex align-items-center justify-content-center"><i class="ti ti-alert-triangle text-white fs-4"></i></div>'
                    },
                    alta: {
                        class: 'bg-warning-subtle',
                        icon: '<div class="avatar-sm bg-warning rounded-circle d-flex align-items-center justify-content-center"><i class="ti ti-alert-circle text-white fs-4"></i></div>'
                    },
                    media: {
                        class: 'bg-info-subtle',
                        icon: '<div class="avatar-sm bg-info rounded-circle d-flex align-items-center justify-content-center"><i class="ti ti-info-circle text-white fs-4"></i></div>'
                    },
                    baja: {
                        class: 'bg-success-subtle',
                        icon: '<div class="avatar-sm bg-success rounded-circle d-flex align-items-center justify-content-center"><i class="ti ti-check text-white fs-4"></i></div>'
                    }
                };

                const config = configs[tipo] || configs.baja;

                // Aplicar cambios en batch
                requestAnimationFrame(() => {
                    elements.modalHeader.classList.add(config.class);
                    elements.modalIcono.innerHTML = config.icon;
                });
            }

            // Función para configurar badges
            function setupBadges(tipo, estado) {
                if (!elements.badgeSeveridad || !elements.badgeEstado) return;

                const severidadConfigs = {
                    critica: {
                        class: 'badge bg-danger',
                        html: '<i class="ti ti-alert-triangle me-1"></i>Crítica'
                    },
                    alta: {
                        class: 'badge bg-warning',
                        html: '<i class="ti ti-alert-circle me-1"></i>Alta'
                    },
                    media: {
                        class: 'badge bg-info',
                        html: '<i class="ti ti-info-circle me-1"></i>Media'
                    },
                    baja: {
                        class: 'badge bg-success',
                        html: '<i class="ti ti-check me-1"></i>Baja'
                    }
                };

                const estadoConfigs = {
                    enviado: {
                        class: 'badge bg-success',
                        html: '<i class="ti ti-check me-1"></i>Enviado'
                    },
                    pendiente: {
                        class: 'badge bg-warning',
                        html: '<i class="ti ti-clock me-1"></i>Pendiente'
                    },
                    fallido: {
                        class: 'badge bg-danger',
                        html: '<i class="ti ti-x me-1"></i>Fallido'
                    }
                };

                const severidadConfig = severidadConfigs[tipo] || severidadConfigs.baja;
                const estadoConfig = estadoConfigs[estado] || estadoConfigs.fallido;

                // Aplicar cambios en batch
                requestAnimationFrame(() => {
                    elements.badgeSeveridad.className = severidadConfig.class;
                    elements.badgeSeveridad.innerHTML = severidadConfig.html;
                    elements.badgeEstado.className = estadoConfig.class;
                    elements.badgeEstado.innerHTML = estadoConfig.html;
                });
            }

            // Función para abrir el modal (optimizada)
            function openModal(data) {
                // Cachear datos
                notificacionActualId = data.id;

                // Configurar modal
                setupModalHeader(data.tipo);

                // Actualizar contenido (batch update)
                requestAnimationFrame(() => {
                    if (elements.modalSistema) elements.modalSistema.textContent = data.sistema;
                    if (elements.modalFecha) elements.modalFecha.textContent = data.fecha;
                    if (elements.modalMensaje) elements.modalMensaje.textContent = data.mensaje;
                });

                // Configurar badges
                setupBadges(data.tipo, data.estado);

                // Mostrar/ocultar botón de reenviar
                if (elements.btnReenviar) {
                    elements.btnReenviar.style.display = data.estado !== 'enviado' ? 'inline-block' : 'none';
                }

                // Limpiar backdrops residuales
                cleanupBackdrops();

                // Abrir modal
                if (elements.modalElement) {
                    // Destruir instancia anterior si existe
                    if (modalInstance) {
                        modalInstance.dispose();
                        modalInstance = null;
                    }

                    // Crear nueva instancia
                    modalInstance = new bootstrap.Modal(elements.modalElement, {
                        backdrop: true,
                        keyboard: true,
                        focus: true
                    });

                    modalInstance.show();

                    // Asegurar clase en body
                    requestAnimationFrame(() => {
                        document.body.classList.add('modal-open');
                    });
                }
            }

            // Función para cerrar dropdown
            function closeDropdown() {
                const dropdownElement = document.querySelector('.topbar-item [data-bs-toggle="dropdown"]');
                if (dropdownElement) {
                    const dropdownInstance = bootstrap.Dropdown.getInstance(dropdownElement);
                    if (dropdownInstance) {
                        dropdownInstance.hide();
                    }
                }
            }

            // Handler para click en notificación (optimizado)
            function handleNotificationClick(e) {
                const notifBtn = e.target.closest('.ver-notificacion-btn');
                if (!notifBtn) return;

                e.preventDefault();
                e.stopPropagation();

                // Cerrar dropdown
                closeDropdown();

                // Extraer datos
                const data = {
                    id: notifBtn.dataset.id,
                    fecha: notifBtn.dataset.fecha,
                    sistema: notifBtn.dataset.sistema,
                    mensaje: notifBtn.dataset.mensaje,
                    estado: notifBtn.dataset.estado,
                    tipo: notifBtn.dataset.tipo
                };

                // Delay para que el dropdown se cierre
                setTimeout(() => openModal(data), 100);
            }

            // Handler para reenviar notificación
            async function handleReenviar() {
                if (!notificacionActualId || !csrf) return;

                const confirm = await Swal.fire({
                    title: '¿Reenviar a Telegram?',
                    text: 'Se enviará nuevamente esta notificación',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, reenviar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#198754'
                });

                if (!confirm.isConfirmed) return;

                Swal.fire({
                    title: 'Reenviando...',
                    text: 'Por favor espera',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                try {
                    const res = await fetch(`/admin/notificaciones/${notificacionActualId}/reenviar`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();

                    if (data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: '¡Reenviado!',
                            text: 'Notificación reenviada correctamente',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        if (modalInstance) {
                            modalInstance.hide();
                        }

                        setTimeout(() => window.location.reload(), 2000);
                    } else {
                        Swal.fire('Error', data.message || 'No se pudo reenviar', 'error');
                    }
                } catch (error) {
                    console.error('Error al reenviar:', error);
                    Swal.fire('Error', 'Error al reenviar la notificación', 'error');
                }
            }

            // Handler para eliminar notificación
            async function handleEliminar() {
                if (!notificacionActualId || !csrf) return;

                const confirm = await Swal.fire({
                    title: '¿Eliminar notificación?',
                    text: 'Esta acción no se puede deshacer',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#dc3545'
                });

                if (!confirm.isConfirmed) return;

                try {
                    const res = await fetch(`/admin/notificaciones/${notificacionActualId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();

                    if (data.success) {
                        await Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: 'Notificación eliminada correctamente',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        if (modalInstance) {
                            modalInstance.hide();
                        }

                        setTimeout(() => window.location.reload(), 2000);
                    }
                } catch (error) {
                    console.error('Error al eliminar:', error);
                    Swal.fire('Error', 'No se pudo eliminar', 'error');
                }
            }

            // Handler para limpieza al cerrar modal
            function handleModalHidden() {
                cleanupBackdrops();

                // Limpiar clases del body si no hay modales abiertos
                if (!document.querySelector('.modal.show')) {
                    requestAnimationFrame(() => {
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    });
                }
            }

            // Inicialización usando requestIdleCallback para mejor rendimiento
            function init() {
                // Cachear elementos
                cacheElements();

                // Event delegation para clicks en notificaciones
                document.addEventListener('click', handleNotificationClick);

                // Listeners para botones del modal
                if (elements.btnReenviar) {
                    elements.btnReenviar.addEventListener('click', handleReenviar);
                }

                if (elements.btnEliminar) {
                    elements.btnEliminar.addEventListener('click', handleEliminar);
                }

                // Listener para limpieza al cerrar modal
                if (elements.modalElement) {
                    elements.modalElement.addEventListener('hidden.bs.modal', handleModalHidden);
                }
            }

            // Usar requestIdleCallback si está disponible, sino DOMContentLoaded
            if ('requestIdleCallback' in window) {
                requestIdleCallback(init, {
                    timeout: 2000
                });
            } else {
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', init);
                } else {
                    init();
                }
            }
        })();
    </script>
</body>

</html>
