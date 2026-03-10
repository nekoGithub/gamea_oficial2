<!-- Topbar Start -->
<header class="app-topbar">
    <div class="container-fluid topbar-menu">
        <div class="d-flex align-items-center gap-2">
            <!-- Logo GAMEA -->
            <div class="logo-topbar">
                <a class="logo-light" href="/">
                    <span class="logo-lg">
                        <img alt="Logo GAMEA" src="/img/logo.png" height="40" />
                    </span>
                    <span class="logo-sm">
                        <img alt="Logo GAMEA" src="/img/logo.png" height="32" />
                    </span>
                </a>
                <a class="logo-dark" href="/">
                    <span class="logo-lg">
                        <img alt="Logo GAMEA" src="/img/logo.png" height="40" />
                    </span>
                    <span class="logo-sm">
                        <img alt="Logo GAMEA" src="/img/logo.png" height="32" />
                    </span>
                </a>
            </div>

            <!-- Botón Toggle Sidebar -->
            <button class="sidenav-toggle-button btn btn-default btn-icon">
                <i class="ti ti-menu-4 fs-22"></i>
            </button>

            <!-- Botón Toggle Horizontal Menu -->
            <button class="topnav-toggle-button px-2" data-bs-target="#topnav-menu-content" data-bs-toggle="collapse">
                <i class="ti ti-menu-4 fs-22"></i>
            </button>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Búsqueda -->
            <div class="app-search d-none d-xl-flex me-2">
                <form action="/" method="GET">
                    <input class="form-control topbar-search rounded-pill" name="q"
                        placeholder="Buscar en GAMEA..." type="search" />
                    <i class="app-search-icon text-muted" data-lucide="search"></i>
                </form>
            </div>

            <!-- Notificaciones Dropdown -->
            <div class="topbar-item">
                <div class="dropdown">
                    @php
                        $notificacionesPendientes = \App\Models\Notificacion::with(['sistemaVersion.sistema'])
                            ->where('estado', 'pendiente')
                            ->orderBy('fecha', 'desc')
                            ->limit(5)
                            ->get();

                        $totalPendientes = \App\Models\Notificacion::where('estado', 'pendiente')->count();
                    @endphp

                    <button aria-expanded="false" aria-haspopup="false"
                        class="topbar-link dropdown-toggle drop-arrow-none position-relative"
                        data-bs-auto-close="outside" data-bs-offset="0,24" data-bs-toggle="dropdown" type="button">
                        <i class="fs-xxl" data-lucide="bell"></i>
                        @if ($totalPendientes > 0)
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                {{ $totalPendientes > 9 ? '9+' : $totalPendientes }}
                                <span class="visually-hidden">notificaciones pendientes</span>
                            </span>
                        @endif
                    </button>

                    <div class="dropdown-menu p-0 dropdown-menu-end dropdown-menu-lg">
                        {{-- HEADER --}}
                        <div class="px-3 py-2 border-bottom">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="m-0 fs-md fw-semibold">
                                        <i class="ti ti-bell me-1"></i> Notificaciones
                                    </h6>
                                </div>
                                <div class="col text-end">
                                    @if ($totalPendientes > 0)
                                        <span class="badge badge-soft-danger badge-label py-1">
                                            {{ $totalPendientes }} {{ $totalPendientes === 1 ? 'Nueva' : 'Nuevas' }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-secondary badge-label py-1">Sin pendientes</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- CONTENIDO - ENLACES A PÁGINA DEDICADA --}}
                        <div data-simplebar="" style="max-height: 350px;">
                            @forelse($notificacionesPendientes as $notif)
                                @php
                                    // Determinar tipo de severidad
                                    $tipo = 'baja';
                                    if (str_contains($notif->mensaje, '[critica]')) {
                                        $tipo = 'critica';
                                    } elseif (str_contains($notif->mensaje, '[alta]')) {
                                        $tipo = 'alta';
                                    } elseif (str_contains($notif->mensaje, '[media]')) {
                                        $tipo = 'media';
                                    }

                                    // Limpiar mensaje
                                    $mensajeLimpio = preg_replace(
                                        '/\[(critica|alta|media|baja)\]\s*/i',
                                        '',
                                        $notif->mensaje,
                                    );
                                @endphp

                                {{-- ENLACE DIRECTO A PÁGINA DE DETALLE --}}
                                <a href="{{ route('admin.notificaciones.show', $notif) }}"
                                    class="dropdown-item notify-item border-bottom">

                                    <div class="d-flex align-items-start">
                                        {{-- Icono según severidad --}}
                                        <div class="flex-shrink-0 me-2">
                                            @if ($tipo === 'critica')
                                                <div
                                                    class="avatar-sm bg-danger-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-alert-triangle text-danger fs-4"></i>
                                                </div>
                                            @elseif($tipo === 'alta')
                                                <div
                                                    class="avatar-sm bg-warning-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-alert-circle text-warning fs-4"></i>
                                                </div>
                                            @elseif($tipo === 'media')
                                                <div
                                                    class="avatar-sm bg-info-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-info-circle text-info fs-4"></i>
                                                </div>
                                            @else
                                                <div
                                                    class="avatar-sm bg-success-subtle rounded-circle d-flex align-items-center justify-content-center">
                                                    <i class="ti ti-check text-success fs-4"></i>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Contenido --}}
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fs-sm">
                                                @if ($notif->sistemaVersion && $notif->sistemaVersion->sistema)
                                                    {{ $notif->sistemaVersion->sistema->nombre }}
                                                @else
                                                    Alerta del Sistema
                                                @endif
                                            </h6>
                                            <p class="mb-1 text-muted fs-xs">
                                                {{ Str::limit($mensajeLimpio, 80) }}
                                            </p>
                                            <small class="text-muted">
                                                <i class="ti ti-clock me-1"></i>
                                                {{ $notif->fecha->diffForHumans() }}
                                            </small>
                                        </div>

                                        {{-- Badge de severidad --}}
                                        <div class="flex-shrink-0 ms-2">
                                            @if ($tipo === 'critica')
                                                <span class="badge badge-soft-danger badge-label">Crítica</span>
                                            @elseif($tipo === 'alta')
                                                <span class="badge badge-soft-warning badge-label">Alta</span>
                                            @elseif($tipo === 'media')
                                                <span class="badge badge-soft-info badge-label">Media</span>
                                            @else
                                                <span class="badge badge-soft-success badge-label">Baja</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="dropdown-item text-center py-4 text-muted">
                                    <i class="ti ti-bell-off fs-1 mb-2 d-block"></i>
                                    <p class="mb-0">No hay notificaciones pendientes</p>
                                    <small class="text-muted">¡Todo está en orden!</small>
                                </div>
                            @endforelse
                        </div>

                        {{-- FOOTER --}}
                        <a class="dropdown-item text-center text-primary border-top border-light py-2 fw-semibold"
                            href="{{ route('admin.notificaciones.index') }}">
                            <i class="ti ti-arrow-right me-1"></i>
                            Ver Todas las Notificaciones
                            @if ($totalPendientes > 5)
                                <span class="badge bg-primary ms-1">+{{ $totalPendientes - 5 }}</span>
                            @endif
                        </a>
                    </div>
                </div>
            </div>

            <!-- Modo Claro/Oscuro -->
            <div class="topbar-item">
                <div class="dropdown">
                    <button aria-expanded="false" aria-haspopup="false" class="topbar-link" data-bs-offset="0,24"
                        data-bs-toggle="dropdown" type="button">
                        <i class="fs-xxl" data-lucide="sun"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end thememode-dropdown">
                        <li>
                            <label class="dropdown-item">
                                <i class="align-middle me-1 fs-16" data-lucide="sun"></i>
                                <span class="align-middle">Claro</span>
                                <input class="form-check-input" name="data-bs-theme" type="radio"
                                    value="light" />
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item">
                                <i class="align-middle me-1 fs-16" data-lucide="moon"></i>
                                <span class="align-middle">Oscuro</span>
                                <input class="form-check-input" name="data-bs-theme" type="radio"
                                    value="dark" />
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item">
                                <i class="align-middle me-1 fs-16" data-lucide="monitor-cog"></i>
                                <span class="align-middle">Sistema</span>
                                <input class="form-check-input" name="data-bs-theme" type="radio"
                                    value="system" />
                            </label>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Pantalla Completa -->
            <div class="topbar-item d-none d-sm-flex">
                <button class="topbar-link" data-toggle="fullscreen" type="button">
                    <i class="fs-xxl fullscreen-off" data-lucide="maximize"></i>
                    <i class="fs-xxl fullscreen-on" data-lucide="minimize"></i>
                </button>
            </div>

            <!-- Modo Monocromático -->
            <div class="topbar-item d-none d-sm-flex">
                <button class="topbar-link" id="monochrome-mode" type="button">
                    <i class="fs-xxl" data-lucide="palette"></i>
                </button>
            </div>

            <!-- Usuario Dropdown -->
            <div class="topbar-item nav-user">
                <div class="dropdown">
                    @if (auth()->check())
                        <a aria-expanded="false" aria-haspopup="false"
                            class="topbar-link dropdown-toggle drop-arrow-none px-2" data-bs-offset="0,19"
                            data-bs-toggle="dropdown" href="#!">
                            <!-- Avatar del Usuario -->
                            @if (auth()->user()->avatar)
                                <img alt="{{ auth()->user()->name }}" class="rounded-circle me-lg-2 d-flex"
                                    src="{{ asset('storage/' . auth()->user()->avatar) }}" width="32" />
                            @else
                                <div class="avatar-sm rounded-circle d-flex align-items-center justify-content-center me-lg-2"
                                    style="background-color: #D32F2F; width: 32px; height: 32px;">
                                    <span class="fw-bold text-white"
                                        style="font-size: 13px;">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', auth()->user()->name)[1] ?? auth()->user()->name, 0, 1)) }}</span>
                                </div>
                            @endif

                            <!-- Nombre y Rol -->
                            <div class="d-lg-flex align-items-start flex-column gap-0 d-none">
                                <h5 class="my-0 fs-sm">{{ auth()->user()->name }}</h5>
                                <small class="text-muted fs-xs">
                                    {{ auth()->user()->getRoleNames()->first() ?? 'Usuario' }}
                                </small>
                            </div>
                            <i class="ti ti-chevron-down align-middle ms-1"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- Header -->
                            <div class="dropdown-header noti-title">
                                <h6 class="text-overflow m-0">¡Bienvenido de nuevo! 👋</h6>
                                <small class="text-muted">{{ auth()->user()->email }}</small>
                            </div>

                            <!-- Mi Perfil -->
                            <a class="dropdown-item" href="/">
                                <i class="ti ti-user-circle me-1 fs-17 align-middle"></i>
                                <span class="align-middle">Mi Perfil</span>
                            </a>

                            <!-- Notificaciones -->
                            <a class="dropdown-item" href="{{ route('admin.notificaciones.index') }}">
                                <i class="ti ti-bell-ringing me-1 fs-17 align-middle"></i>
                                <span class="align-middle">Notificaciones</span>
                            </a>

                            <!-- Configuración -->
                            <a class="dropdown-item" href="/">
                                <i class="ti ti-settings-2 me-1 fs-17 align-middle"></i>
                                <span class="align-middle">Configuración</span>
                            </a>

                            <!-- Centro de Ayuda -->
                            <a class="dropdown-item" href="/">
                                <i class="ti ti-headset me-1 fs-17 align-middle"></i>
                                <span class="align-middle">Centro de Ayuda</span>
                            </a>

                            <div class="dropdown-divider"></div>

                            <!-- Bloquear Pantalla -->
                            <a class="dropdown-item" href="/">
                                <i class="ti ti-lock me-1 fs-17 align-middle"></i>
                                <span class="align-middle">Bloquear Pantalla</span>
                            </a>

                            <!-- Cerrar Sesión -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a class="dropdown-item fw-semibold text-danger" href="javascript:void(0);"
                                    onclick="this.closest('form').submit();">
                                    <i class="ti ti-logout-2 me-1 fs-17 align-middle"></i>
                                    <span class="align-middle">Cerrar Sesión</span>
                                </a>
                            </form>
                        </div>
                    @else
                        <div class="text-center w-100 py-3">
                            <p class="text-muted mb-2">No autenticado</p>
                            <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Iniciar Sesión</a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Botón Configuración de Tema -->
            <div class="topbar-item d-none d-sm-flex">
                <button class="topbar-link" data-bs-target="#theme-settings-offcanvas" data-bs-toggle="offcanvas"
                    type="button">
                    <i class="ti ti-settings icon-spin fs-24"></i>
                </button>
            </div>
        </div>
    </div>
</header>
<!-- Topbar End -->
