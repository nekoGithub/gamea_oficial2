 <style>
     .logo {
         padding: 0.56rem 0 !important;
         /* Más compacto - antes 0.75rem */
         display: flex;
         justify-content: center;
     }

     .logo-text {
         display: flex;
         flex-direction: column;
         line-height: 1.1;
         /* Más compacto - antes 1.2 */
     }

     /* Modo Light - Colores Institucionales El Alto */
     .logo-light .logo-title {
         font-size: 19px;
         /* Más pequeño - antes 21px */
         font-weight: 700;
         color: #D32F2F;
         /* Rojo institucional de El Alto */
         letter-spacing: 0.8px;
         font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
     }

     .logo-light .logo-subtitle {
         font-size: 9px;
         /* Más pequeño - antes 10px */
         font-weight: 600;
         color: #26C6DA;
         /* Cyan institucional de El Alto */
         text-transform: uppercase;
         letter-spacing: 0.5px;
         margin-top: 1px;
         /* Menos espacio - antes 2px */
     }

     /* Modo Dark - Colores brillantes pero institucionales */
     .logo-dark .logo-title {
         font-size: 19px;
         /* Más pequeño - antes 21px */
         font-weight: 700;
         color: #EF5350;
         /* Rojo más brillante para dark mode */
         letter-spacing: 0.8px;
         font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
     }

     .logo-dark .logo-subtitle {
         font-size: 9px;
         /* Más pequeño - antes 10px */
         font-weight: 600;
         color: #26C6DA;
         /* Cyan más brillante para dark mode */
         text-transform: uppercase;
         letter-spacing: 0.5px;
         margin-top: 1px;
         /* Menos espacio - antes 2px */
     }

     /* SIN hover - los colores NO cambian */
     .logo .logo-title,
     .logo .logo-subtitle {
         transition: none;
         /* Sin animación */
     }
 </style>
 <!-- Sidenav Menu Start -->
 <div class="sidenav-menu">
     <a class="logo" href="">
         <span class="logo logo-light">
             <!-- Logo Grande con Texto -->
             <span class="logo-lg">
                 <div class="d-flex align-items-center gap-2 px-2">
                     <img alt="logo" src="/img/logo.png" style="height: 34px; width: auto;" />
                     <div class="logo-text">
                         <span class="logo-title">GAMEA</span>
                         <span class="logo-subtitle">Gestión de Sistemas</span>
                     </div>
                 </div>
             </span>
             <!-- Logo Pequeño (solo icono) -->
             <span class="logo-sm">
                 <img alt="small logo" src="/img/logo.png" style="height: 30px; width: auto;" />
             </span>
         </span>
         <span class="logo logo-dark">
             <!-- Logo Grande con Texto (Dark) -->
             <span class="logo-lg">
                 <div class="d-flex align-items-center gap-2 px-2">
                     <img alt="dark logo" src="/img/logo.png" style="height: 34px; width: auto;" />
                     <div class="logo-text">
                         <span class="logo-title">GAMEA</span>
                         <span class="logo-subtitle">Gestión de Sistemas</span>
                     </div>
                 </div>
             </span>
             <!-- Logo Pequeño (solo icono) -->
             <span class="logo-sm">
                 <img alt="small logo" src="/img/logo.png" style="height: 30px; width: auto;" />
             </span>
         </span>
     </a>

     <!-- Sidebar Hover Menu Toggle Button -->
     <button class="button-on-hover">
         <i class="ti ti-menu-4 fs-22 align-middle"></i>
     </button>

     <!-- Full Sidebar Menu Close Button -->
     <button class="button-close-offcanvas">
         <i class="ti ti-x align-middle"></i>
     </button>

     <div class="scrollbar" data-simplebar="">

         <!-- Usuario en Sidebar -->
         <div class="sidenav-user">
             <div class="d-flex align-items-center gap-2">
                 @if (auth()->check())
                     <a class="link-reset d-flex align-items-center gap-2 flex-grow-1" href="/">
                         <!-- Avatar del Usuario (más pequeño) -->
                         @if (auth()->user()->profile_photo_path)
                             <img alt="{{ auth()->user()->name }}" class="rounded-circle"
                                 src="{{ asset('storage/avatars/' . auth()->user()->profile_photo_path) }}"
                                 style="width: 40px; height: 40px; object-fit: cover;" />
                         @else
                             <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 40px; height: 40px; background-color: #D32F2F;">
                                 <span class="fw-bold text-white small">
                                     {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name)[1] ?? auth()->user()->name, 0, 1)) }}
                                 </span>
                             </div>
                         @endif

                         <!-- Info del Usuario -->
                         <div class="flex-grow-1 overflow-hidden">
                             <!-- Nombre -->
                             <div class="fw-semibold text-truncate mb-0 small">{{ auth()->user()->name }}</div>

                             <!-- Email -->
                             <div class="text-muted text-truncate mb-0" style="font-size: 11px;">
                                 {{ auth()->user()->email }}
                             </div>

                             <!-- Rol (badge pequeño) -->
                             <span class="badge badge-soft-info" style="font-size: 10px; padding: 1px 6px;">
                                 {{ auth()->user()->getRoleNames()->first() ?? 'Usuario' }}
                             </span>
                         </div>
                     </a>

                     <!-- Icono Settings -->
                     <div class="flex-shrink-0">
                         <a aria-expanded="false" aria-haspopup="false"
                             class="dropdown-toggle drop-arrow-none link-reset" data-bs-offset="0,12"
                             data-bs-toggle="dropdown" href="#!">
                             <i class="ti ti-settings fs-20 settings-icon"></i>
                         </a>
                         <div class="dropdown-menu">
                             <!-- Header -->
                             <div class="dropdown-header noti-title">
                                 <h6 class="text-overflow m-0 small">¡Bienvenido de nuevo!</h6>
                                 <small class="text-muted">{{ auth()->user()->email }}</small>
                             </div>

                             <!-- Mi Perfil -->
                             <a class="dropdown-item" href="/">
                                 <i class="ti ti-user-circle me-2 fs-17 align-middle"></i>
                                 <span class="align-middle">Mi Perfil</span>
                             </a>

                             <!-- Notificaciones -->
                             <a class="dropdown-item" href="/">
                                 <i class="ti ti-bell-ringing me-2 fs-17 align-middle"></i>
                                 <span class="align-middle">Notificaciones</span>
                             </a>

                             <!-- Configuración -->
                             <a class="dropdown-item" href="/">
                                 <i class="ti ti-settings-2 me-2 fs-17 align-middle"></i>
                                 <span class="align-middle">Configuración de Cuenta</span>
                             </a>

                             <!-- Centro de Ayuda -->
                             <a class="dropdown-item" href="/">
                                 <i class="ti ti-headset me-2 fs-17 align-middle"></i>
                                 <span class="align-middle">Centro de Ayuda</span>
                             </a>

                             <!-- Divider -->
                             <div class="dropdown-divider"></div>

                             <!-- Bloquear Pantalla -->
                             <a class="dropdown-item" href="/">
                                 <i class="ti ti-lock me-2 fs-17 align-middle"></i>
                                 <span class="align-middle">Bloquear Pantalla</span>
                             </a>

                             <!-- Cerrar Sesión -->
                             <form method="POST" action="{{ route('logout') }}">
                                 @csrf
                                 <a class="dropdown-item fw-semibold text-danger" href="javascript:void(0);"
                                     onclick="this.closest('form').submit();">
                                     <i class="ti ti-logout-2 me-2 fs-17 align-middle"></i>
                                     <span class="align-middle">Cerrar Sesión</span>
                                 </a>
                             </form>
                         </div>
                     </div>
                 @else
                     <div class="text-center w-100 py-3">
                         <p class="text-muted mb-2">No autenticado</p>
                         <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Iniciar Sesión</a>
                     </div>
                 @endif
             </div>
         </div>

         <!--- Sidenav Menu -->
         <ul class="side-nav">

             <!-- ========== DASHBOARD ========== -->
             <li class="side-nav-title mt-2">Principal</li>
             @can('dashboard')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('dashboard') }}">
                         <span class="menu-icon"><i data-lucide="gauge"></i></span>
                         <span class="menu-text">Panel de Control</span>
                     </a>
                 </li>
             @endcan

             <!-- ========== SISTEMAS ========== -->
             @canany(['admin.sistemas.index', 'admin.servidores.index', 'admin.tecnologias.index',
                 'admin.sistemas-operativos.index', 'admin.bases-datos.index', 'admin.documentos.index'])
                 <li class="side-nav-title mt-2">Sistemas</li>
             @endcanany

             @can('admin.sistemas.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.sistemas.index') }}">
                         <span class="menu-icon"><i data-lucide="layout-dashboard"></i></span>
                         <span class="menu-text">Sistemas</span>
                     </a>
                 </li>
             @endcan

             @can('admin.servidores.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.servidores.index') }}">
                         <span class="menu-icon"><i data-lucide="server"></i></span>
                         <span class="menu-text">Servidores</span>
                     </a>
                 </li>
             @endcan

             @can('admin.tecnologias.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.tecnologias.index') }}">
                         <span class="menu-icon"><i data-lucide="cpu"></i></span>
                         <span class="menu-text">Tecnologías</span>
                     </a>
                 </li>
             @endcan

             @can('admin.sistemas-operativos.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.sistemas-operativos.index') }}">
                         <span class="menu-icon"><i data-lucide="terminal"></i></span>
                         <span class="menu-text">Sistemas Operativos</span>
                     </a>
                 </li>
             @endcan

             @can('admin.bases-datos.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.bases-datos.index') }}">
                         <span class="menu-icon"><i data-lucide="database"></i></span>
                         <span class="menu-text">Bases de Datos</span>
                     </a>
                 </li>
             @endcan

             @can('admin.documentos.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.documentos.index') }}">
                         <span class="menu-icon"><i data-lucide="files"></i></span>
                         <span class="menu-text">Documentos</span>
                     </a>
                 </li>
             @endcan

             <!-- ========== ORGANIZACIÓN ========== -->
             @canany(['admin.unidades.index', 'admin.responsables.index'])
                 <li class="side-nav-title mt-2">Organización</li>
             @endcanany

             @can('admin.unidades.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.unidades.index') }}">
                         <span class="menu-icon"><i data-lucide="building"></i></span>
                         <span class="menu-text">Unidades</span>
                     </a>
                 </li>
             @endcan

             @can('admin.responsables.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.responsables.index') }}">
                         <span class="menu-icon"><i data-lucide="user-check"></i></span>
                         <span class="menu-text">Responsables</span>
                     </a>
                 </li>
             @endcan

             <!-- ========== GESTIÓN ========== -->
             @canany(['admin.users.index', 'profile.show', 'admin.roles.index', 'admin.uploads.index'])
                 <li class="side-nav-title mt-2">Gestión</li>
             @endcanany

             @canany(['admin.users.index', 'profile.show', 'admin.roles.index'])
                 <li class="side-nav-item">
                     <a aria-controls="sidebarUsuarios" aria-expanded="false" class="side-nav-link"
                         data-bs-toggle="collapse" href="#sidebarUsuarios">
                         <span class="menu-icon"><i data-lucide="users"></i></span>
                         <span class="menu-text">Usuarios</span>
                         <span class="menu-arrow"></span>
                     </a>
                     <div class="collapse" id="sidebarUsuarios">
                         <ul class="sub-menu">
                             @can('admin.users.index')
                                 <li class="side-nav-item">
                                     <a class="side-nav-link" href="{{ route('admin.users.index') }}">
                                         <span class="menu-text">Lista de Usuarios</span>
                                     </a>
                                 </li>
                             @endcan
                             @can('profile.show')
                                 <li class="side-nav-item">
                                     <a class="side-nav-link" href="{{ route('profile.show') }}">
                                         <span class="menu-text">Mi Perfil</span>
                                     </a>
                                 </li>
                             @endcan
                             @can('admin.roles.index')
                                 <li class="side-nav-item">
                                     <a class="side-nav-link" href="{{ route('admin.roles.index') }}">
                                         <span class="menu-text">Roles y Permisos</span>
                                     </a>
                                 </li>
                             @endcan
                         </ul>
                     </div>
                 </li>
             @endcanany

             @can('admin.uploads.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.uploads.index') }}">
                         <span class="menu-icon"><i data-lucide="upload-cloud"></i></span>
                         <span class="menu-text">Uploads</span>
                         @php
                             $uploadsPendientes = \App\Models\VersionUpload::where('user_id', auth()->id())
                                 ->whereIn('estado', ['pendiente', 'procesando'])
                                 ->count();
                         @endphp
                         @if ($uploadsPendientes > 0)
                             <span class="badge bg-warning rounded-pill ms-auto">{{ $uploadsPendientes }}</span>
                         @endif
                     </a>
                 </li>
             @endcan

             <!-- ========== SEGURIDAD ========== -->
             @canany(['admin.ssls.index', 'admin.credenciales.index'])
                 <li class="side-nav-title mt-2">Seguridad</li>
             @endcanany

             @can('admin.ssls.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.ssls.index') }}">
                         <span class="menu-icon"><i data-lucide="shield-check"></i></span>
                         <span class="menu-text">Certificados SSL</span>
                     </a>
                 </li>
             @endcan

             @can('admin.credenciales.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.credenciales.index') }}">
                         <span class="menu-icon"><i data-lucide="key-round"></i></span>
                         <span class="menu-text">Credenciales</span>
                     </a>
                 </li>
             @endcan

             <!-- ========== ALERTAS Y SEGUIMIENTO ========== -->
             @canany(['admin.notificaciones.index', 'admin.auditorias.index'])
                 <li class="side-nav-title mt-2">Alertas y Seguimiento</li>
             @endcanany

             @can('admin.notificaciones.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.notificaciones.index') }}">
                         <span class="menu-icon"><i data-lucide="bell"></i></span>
                         <span class="menu-text">Notificaciones</span>
                         @php
                             $pendientes = \App\Models\Notificacion::where('estado', 'pendiente')->count();
                         @endphp
                         @if ($pendientes > 0)
                             <span class="badge bg-danger rounded-pill ms-auto">{{ $pendientes }}</span>
                         @endif
                     </a>
                 </li>
             @endcan

             @can('admin.auditorias.index')
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.auditorias.index') }}">
                         <span class="menu-icon"><i data-lucide="file-search"></i></span>
                         <span class="menu-text">Auditorías</span>
                     </a>
                 </li>
             @endcan

             <li class="side-nav-item">
                 <a class="side-nav-link" href="{{ route('admin.monitoreo.index') }}">
                     <span class="menu-icon"><i data-lucide="activity"></i></span>
                     <span class="menu-text">Monitoreo</span>
                     @php
                         $servidoresCaidos = \App\Models\Servidor::where('estado', 'activo')
                             ->where('disponibilidad_interna', 'INACTIVO')
                             ->count();
                     @endphp
                     @if ($servidoresCaidos > 0)
                         <span class="badge bg-danger rounded-pill ms-auto">{{ $servidoresCaidos }}</span>
                     @endif
                 </a>
             </li>

             <!-- ========== REPORTES ========== -->
             @can('admin.reportes.index')
                 <li class="side-nav-title mt-2">Reportes</li>
                 <li class="side-nav-item">
                     <a class="side-nav-link" href="{{ route('admin.reportes.index') }}">
                         <span class="menu-icon"><i data-lucide="file-bar-chart"></i></span>
                         <span class="menu-text">Reportes</span>
                     </a>
                 </li>
             @endcan

             <!-- ========== SALIR ========== -->
             <li class="side-nav-item">
                 <form id="logout-form" method="POST" action="{{ route('logout') }}">
                     @csrf
                     <a href="javascript:void(0);" class="side-nav-link text-danger" onclick="confirmLogout()">
                         <span class="menu-icon"><i data-lucide="log-out"></i></span>
                         <span class="menu-text">Salir</span>
                     </a>
                 </form>
             </li>

         </ul>
     </div>
 </div>

 <script>
     function confirmLogout() {
         Swal.fire({
             title: '¿Cerrar sesión?',
             text: 'Tu sesión actual se cerrará',
             icon: 'warning',
             showCancelButton: true,
             confirmButtonColor: '#d33',
             cancelButtonColor: '#6c757d',
             confirmButtonText: 'Sí, salir',
             cancelButtonText: 'Cancelar'
         }).then((result) => {
             if (result.isConfirmed) {
                 document.getElementById('logout-form').submit();
             }
         });
     }
 </script>

 <!-- Sidenav Menu End -->
