@extends('layouts.vertical', ['title' => 'Monitoreo en Tiempo Real'])

@section('css')
    <style>
        /* ── Cards servidores/sistemas ── */
        .monitor-card {
            transition: all 0.3s ease;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
        }

        .monitor-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .monitor-card.activo {
            border-color: #28a745;
        }

        .monitor-card.inactivo {
            border-color: #dc3545;
        }

        .monitor-card.desconocido {
            border-color: #6c757d;
        }

        .pulse-activo {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #28a745;
            display: inline-block;
            animation: pulse-green 2s infinite;
        }

        .pulse-inactivo {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #dc3545;
            display: inline-block;
            animation: pulse-red 1s infinite;
        }

        .pulse-desconocido {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #6c757d;
            display: inline-block;
        }

        @keyframes pulse-green {
            0% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.6);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(40, 167, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(40, 167, 69, 0);
            }
        }

        @keyframes pulse-red {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.6);
            }

            70% {
                box-shadow: 0 0 0 8px rgba(220, 53, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }

        /* ── Terminal ── */
        .terminal-wrapper {
            background: #0d0d0d;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.5);
        }

        .terminal-header {
            background: #1e1e1e;
            padding: 10px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            border-bottom: 1px solid #333;
        }

        .terminal-header .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .terminal-header .title {
            color: #ccc;
            font-size: 0.85rem;
            margin-left: 8px;
            font-family: monospace;
        }

        .terminal-body {
            padding: 16px;
            height: 350px;
            overflow-y: auto;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.82rem;
            line-height: 1.7;
            color: #c8c8c8;
            background: #0d0d0d;
        }

        .terminal-body::-webkit-scrollbar {
            width: 6px;
        }

        .terminal-body::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        .terminal-body::-webkit-scrollbar-thumb {
            background: #444;
            border-radius: 3px;
        }

        .terminal-body-modal {
            padding: 16px;
            height: 380px;
            overflow-y: auto;
            font-family: 'Courier New', Courier, monospace;
            font-size: 0.82rem;
            line-height: 1.7;
            color: #c8c8c8;
            background: #0d0d0d;
        }

        .terminal-body-modal::-webkit-scrollbar {
            width: 6px;
        }

        .terminal-body-modal::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        .terminal-body-modal::-webkit-scrollbar-thumb {
            background: #444;
            border-radius: 3px;
        }

        .t-green {
            color: #4ec94e;
        }

        .t-red {
            color: #ff5555;
        }

        .t-yellow {
            color: #f1fa8c;
        }

        .t-cyan {
            color: #8be9fd;
        }

        .t-gray {
            color: #6272a4;
        }

        .t-white {
            color: #f8f8f2;
        }

        .t-orange {
            color: #ffb86c;
        }

        .t-purple {
            color: #bd93f9;
        }

        .cursor {
            display: inline-block;
            width: 8px;
            height: 14px;
            background: #4ec94e;
            animation: blink 1s infinite;
            vertical-align: middle;
            margin-left: 2px;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: 0
            }
        }

        .countdown-bar {
            height: 4px;
            background: #1e1e1e;
            overflow: hidden;
        }

        .countdown-fill {
            height: 100%;
            background: linear-gradient(90deg, #4ec94e, #8be9fd);
            transition: width 1s linear;
        }

        .countdown-fill-web {
            height: 100%;
            background: linear-gradient(90deg, #bd93f9, #8be9fd);
            transition: width 1s linear;
        }

        /* Modal */
        .modal-dark .modal-content {
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 12px;
        }

        .modal-dark .modal-header {
            background: #1e1e1e;
            border-bottom: 1px solid #333;
        }

        .modal-dark .modal-footer {
            background: #1e1e1e;
            border-top: 1px solid #333;
        }

        #conexion-ws {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9999;
            padding: 0.4rem 0.85rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .ip-badge {
            font-size: 0.75rem;
            font-family: monospace;
        }

        .click-hint {
            font-size: 0.7rem;
            color: #9ca3af;
        }

        /* Tabs custom */
        .nav-tabs-monitor .nav-link {
            color: #6c757d;
            font-weight: 600;
            border-radius: 8px 8px 0 0;
            padding: 0.6rem 1.5rem;
        }

        .nav-tabs-monitor .nav-link.active {
            color: #fff;
            background: #6366f1;
            border-color: #6366f1;
        }

        .nav-tabs-monitor .nav-link:hover:not(.active) {
            color: #6366f1;
            background: rgba(99, 102, 241, 0.08);
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item active">Monitoreo</li>
                    </ol>
                </div>
                <h4 class="page-title">
                    <i class="ti ti-activity me-2"></i>Monitoreo en Tiempo Real
                </h4>
            </div>
        </div>
    </div>

    {{-- ── TABS ── --}}
    <ul class="nav nav-tabs nav-tabs-monitor mb-0" id="monitoreoTabs">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#tab-servidores">
                <i class="ti ti-server me-1"></i> Servidores
                <span class="badge bg-success ms-1">{{ $totalActivos }}</span>
                @if ($totalInactivos > 0)
                    <span class="badge bg-danger ms-1">{{ $totalInactivos }}</span>
                @endif
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#tab-web">
                <i class="ti ti-world me-1"></i> Sistemas Web
                <span class="badge bg-success ms-1">{{ $webActivos }}</span>
                @if ($webInactivos > 0)
                    <span class="badge bg-danger ms-1">{{ $webInactivos }}</span>
                @endif
            </a>
        </li>
    </ul>

    <div class="tab-content border border-top-0 rounded-bottom p-3 mb-3" style="background: var(--bs-body-bg)">

        {{-- ════════════════════════════════════════ --}}
        {{-- TAB 1: SERVIDORES                        --}}
        {{-- ════════════════════════════════════════ --}}
        <div class="tab-pane fade show active" id="tab-servidores">

            {{-- Stats --}}
            <div class="row mb-3 g-2">
                <div class="col-md-3">
                    <div class="card border-0 bg-success text-white mb-0" style="border-radius:10px">
                        <div class="card-body py-2 d-flex align-items-center gap-2">
                            <i class="ti ti-server fs-3"></i>
                            <div>
                                <div class="fs-4 fw-bold" id="stat-srv-activos">{{ $totalActivos }}</div>
                                <div class="small">Activos</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-danger text-white mb-0" style="border-radius:10px">
                        <div class="card-body py-2 d-flex align-items-center gap-2">
                            <i class="ti ti-server-off fs-3"></i>
                            <div>
                                <div class="fs-4 fw-bold" id="stat-srv-inactivos">{{ $totalInactivos }}</div>
                                <div class="small">Inactivos</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-secondary text-white mb-0" style="border-radius:10px">
                        <div class="card-body py-2 d-flex align-items-center gap-2">
                            <i class="ti ti-help fs-3"></i>
                            <div>
                                <div class="fs-4 fw-bold" id="stat-srv-desconocidos">{{ $totalDesconocidos }}</div>
                                <div class="small">Sin IP Externa</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-primary text-white mb-0" style="border-radius:10px">
                        <div class="card-body py-2 d-flex align-items-center gap-2">
                            <i class="ti ti-clock fs-3"></i>
                            <div>
                                <div class="fw-bold" id="srv-proximo-ping">30s</div>
                                <div class="small">Próximo ping</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Terminal servidores --}}
            <div class="mb-3">
                <div class="terminal-wrapper">
                    <div class="terminal-header">
                        <span class="dot" style="background:#ff5f57"></span>
                        <span class="dot" style="background:#febc2e"></span>
                        <span class="dot" style="background:#28c840"></span>
                        <span class="title">
                            <i class="ti ti-terminal me-1"></i>
                            GAMEA — Monitor Servidores &nbsp;|&nbsp;
                            <span class="t-gray">Intervalo: 30s &nbsp;|&nbsp; Total: {{ $servidores->count() }}</span>
                        </span>
                    </div>
                    <div class="terminal-body" id="terminal-servidores">
                        <div class="t-gray">──────────────────────────────────────────────────────────</div>
                        <div><span class="t-cyan">GAMEA</span> <span class="t-gray">— Monitor de Servidores</span></div>
                        <div><span class="t-gray">Total: </span><span class="t-yellow">{{ $servidores->count() }}
                                servidores</span></div>
                        <div class="t-gray">──────────────────────────────────────────────────────────</div>
                        <div><span class="t-green">✓</span> <span class="t-white">Esperando primer ciclo...</span></div>
                        <div>&nbsp;</div>
                        <div id="terminal-srv-content"></div>
                        <div><span class="t-green">$</span> <span class="cursor"></span></div>
                    </div>
                    <div class="countdown-bar">
                        <div class="countdown-fill" id="srv-countdown-fill" style="width:100%"></div>
                    </div>
                </div>
            </div>

            {{-- Cards servidores --}}
            <div class="row g-3" id="servidores-container">
                @foreach ($servidores as $servidor)
                    @php $estadoI = strtolower($servidor->disponibilidad_interna ?? 'desconocido'); @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6" id="col-srv-{{ $servidor->id }}">
                        <div class="card monitor-card {{ $estadoI }} h-100" id="card-srv-{{ $servidor->id }}"
                            data-id="{{ $servidor->id }}" data-nombre="{{ $servidor->nombre }}"
                            data-ip-interna="{{ $servidor->ip_interna }}" data-ip-externa="{{ $servidor->ip_externa }}"
                            data-tipo="servidor" onclick="abrirTerminalPing(this)">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <span class="pulse-{{ $estadoI }}" id="pulse-srv-{{ $servidor->id }}"></span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size:0.85rem">{{ $servidor->nombre }}</h6>
                                <div class="click-hint mb-2"><i class="ti ti-terminal me-1"></i>Click para ping en vivo
                                </div>
                                <div class="mb-2">
                                    <div class="ip-badge text-muted"><i
                                            class="ti ti-network me-1"></i>{{ $servidor->ip_interna }}</div>
                                    @if ($servidor->ip_externa)
                                        <div class="ip-badge text-muted"><i
                                                class="ti ti-world me-1"></i>{{ $servidor->ip_externa }}</div>
                                    @endif
                                </div>
                                <div class="d-flex gap-2 justify-content-center mb-2">
                                    <div class="text-center">
                                        <div style="font-size:0.6rem" class="text-muted mb-1">INTERNA</div>
                                        <span id="badge-srv-interno-{{ $servidor->id }}"
                                            class="badge {{ $servidor->disponibilidad_interna === 'ACTIVO' ? 'bg-success' : ($servidor->disponibilidad_interna === 'INACTIVO' ? 'bg-danger' : 'bg-secondary') }}"
                                            style="font-size:0.65rem; min-width:75px">
                                            {{ $servidor->disponibilidad_interna ?? 'DESCONOCIDO' }}
                                        </span>
                                    </div>
                                    <div class="text-center">
                                        <div style="font-size:0.6rem" class="text-muted mb-1">EXTERNA</div>
                                        <span id="badge-srv-externo-{{ $servidor->id }}"
                                            class="badge {{ $servidor->disponibilidad_externa === 'ACTIVO' ? 'bg-success' : ($servidor->disponibilidad_externa === 'INACTIVO' ? 'bg-danger' : 'bg-secondary') }}"
                                            style="font-size:0.65rem; min-width:75px">
                                            {{ $servidor->disponibilidad_externa ?? 'DESCONOCIDO' }}
                                        </span>
                                    </div>
                                </div>
                                <div style="font-size:0.7rem; color:#9ca3af" id="ultima-srv-{{ $servidor->id }}">
                                    <i class="ti ti-clock me-1"></i>
                                    {{ $servidor->ultima_verificacion ? $servidor->ultima_verificacion->format('H:i:s') : 'Sin verificar' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ════════════════════════════════════════ --}}
        {{-- TAB 2: SISTEMAS WEB                      --}}
        {{-- ════════════════════════════════════════ --}}
        <div class="tab-pane fade" id="tab-web">

            {{-- Stats --}}
            <div class="row mb-3 g-2">
                <div class="col-md-3">
                    <div class="card border-0 bg-success text-white mb-0" style="border-radius:10px">
                        <div class="card-body py-2 d-flex align-items-center gap-2">
                            <i class="ti ti-world fs-3"></i>
                            <div>
                                <div class="fs-4 fw-bold" id="stat-web-activos">{{ $webActivos }}</div>
                                <div class="small">Activos</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-danger text-white mb-0" style="border-radius:10px">
                        <div class="card-body py-2 d-flex align-items-center gap-2">
                            <i class="ti ti-world-off fs-3"></i>
                            <div>
                                <div class="fs-4 fw-bold" id="stat-web-inactivos">{{ $webInactivos }}</div>
                                <div class="small">Inactivos</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-secondary text-white mb-0" style="border-radius:10px">
                        <div class="card-body py-2 d-flex align-items-center gap-2">
                            <i class="ti ti-help fs-3"></i>
                            <div>
                                <div class="fs-4 fw-bold" id="stat-web-desconocidos">{{ $webDesconocidos }}</div>
                                <div class="small">Sin verificar</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 mb-0 text-white" style="border-radius:10px; background:#7c3aed">
                        <div class="card-body py-2 d-flex align-items-center gap-2">
                            <i class="ti ti-clock fs-3"></i>
                            <div>
                                <div class="fw-bold" id="web-proximo-ping">60s</div>
                                <div class="small">Próximo ping</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Terminal sistemas web --}}
            <div class="mb-3">
                <div class="terminal-wrapper">
                    <div class="terminal-header">
                        <span class="dot" style="background:#ff5f57"></span>
                        <span class="dot" style="background:#febc2e"></span>
                        <span class="dot" style="background:#28c840"></span>
                        <span class="title">
                            <i class="ti ti-terminal me-1"></i>
                            GAMEA — Monitor Sistemas Web &nbsp;|&nbsp;
                            <span class="t-gray">Intervalo: 60s &nbsp;|&nbsp; Total: {{ $sistemas->count() }}</span>
                        </span>
                    </div>
                    <div class="terminal-body" id="terminal-web">
                        <div class="t-gray">──────────────────────────────────────────────────────────</div>
                        <div><span class="t-purple">GAMEA</span> <span class="t-gray">— Monitor de Sistemas Web</span>
                        </div>
                        <div><span class="t-gray">Total: </span><span class="t-yellow">{{ $sistemas->count() }}
                                dominios</span></div>
                        <div class="t-gray">──────────────────────────────────────────────────────────</div>
                        <div><span class="t-green">✓</span> <span class="t-white">Esperando primer ciclo...</span></div>
                        <div>&nbsp;</div>
                        <div id="terminal-web-content"></div>
                        <div><span class="t-purple">$</span> <span class="cursor"></span></div>
                    </div>
                    <div class="countdown-bar">
                        <div class="countdown-fill-web" id="web-countdown-fill" style="width:100%"></div>
                    </div>
                </div>
            </div>

            {{-- Cards sistemas web --}}
            <div class="row g-3" id="sistemas-container">
                @foreach ($sistemas as $sistema)
                    @php $estadoW = strtolower($sistema->disponibilidad_web ?? 'desconocido'); @endphp
                    <div class="col-lg-3 col-md-4 col-sm-6" id="col-web-{{ $sistema->id }}">
                        <div class="card monitor-card {{ $estadoW }} h-100" id="card-web-{{ $sistema->id }}"
                            data-id="{{ $sistema->id }}" data-nombre="{{ $sistema->nombre }}"
                            data-dominio="{{ $sistema->dominio }}" data-tipo="sistema"
                            onclick="abrirTerminalPing(this)">
                            <div class="card-body text-center">
                                <div class="mb-2">
                                    <span class="pulse-{{ $estadoW }}" id="pulse-web-{{ $sistema->id }}"></span>
                                </div>
                                <h6 class="fw-bold mb-1" style="font-size:0.8rem">
                                    {{ $sistema->sigla ?? Str::limit($sistema->nombre, 25) }}
                                </h6>
                                <div class="click-hint mb-2"><i class="ti ti-terminal me-1"></i>Click para ping en vivo
                                </div>
                                <div class="mb-2">
                                    <div class="ip-badge text-muted">
                                        <i class="ti ti-world me-1"></i>{{ $sistema->dominio }}
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <span id="badge-web-{{ $sistema->id }}"
                                        class="badge {{ $sistema->disponibilidad_web === 'ACTIVO' ? 'bg-success' : ($sistema->disponibilidad_web === 'INACTIVO' ? 'bg-danger' : 'bg-secondary') }}"
                                        style="font-size:0.7rem; min-width:90px">
                                        {{ $sistema->disponibilidad_web ?? 'DESCONOCIDO' }}
                                        @if ($sistema->tiempo_respuesta)
                                            {{ $sistema->tiempo_respuesta }}ms
                                        @endif
                                    </span>
                                </div>
                                <div style="font-size:0.7rem; color:#9ca3af" id="ultima-web-{{ $sistema->id }}">
                                    <i class="ti ti-clock me-1"></i>
                                    {{ $sistema->ultima_verificacion_web ? $sistema->ultima_verificacion_web->format('H:i:s') : 'Sin verificar' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    {{-- ════════════════════════════════════════════ --}}
    {{-- MODAL TERMINAL PING                          --}}
    {{-- ════════════════════════════════════════════ --}}
    <div class="modal fade" id="modalPingTerminal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-dark" style="background:#1a1a1a; border:1px solid #333; border-radius:12px">
                <div class="modal-header" style="background:#1e1e1e; border-bottom:1px solid #333">
                    <div>
                        <h5 class="modal-title text-white mb-0" id="modal-ping-nombre">
                            <i class="ti ti-terminal me-2"></i>Ping en vivo
                        </h5>
                        <small class="text-muted" id="modal-ping-subtitle"></small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span id="modal-ping-badge" class="badge bg-secondary">Iniciando...</span>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>
                <div class="modal-body p-0">
                    <div class="terminal-wrapper">
                        <div class="terminal-header">
                            <span class="dot" style="background:#ff5f57"></span>
                            <span class="dot" style="background:#febc2e"></span>
                            <span class="dot" style="background:#28c840"></span>
                            <span class="title" id="modal-ping-title">ping —</span>
                        </div>
                        <div class="terminal-body-modal" id="modal-terminal">
                            <div id="modal-terminal-content"></div>
                            <div><span class="t-green">$</span> <span class="cursor"></span></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="background:#1e1e1e; border-top:1px solid #333">
                    <div class="d-flex align-items-center gap-3 w-100">
                        <div class="text-white small">
                            <i class="ti ti-clock me-1 text-muted"></i>
                            Próximo ping: <span id="modal-countdown" class="fw-bold" style="color:#f1fa8c">5s</span>
                        </div>
                        <div class="ms-auto d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-pausar-ping">
                                <i class="ti ti-player-pause me-1"></i>Pausar
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" data-bs-dismiss="modal">
                                <i class="ti ti-x me-1"></i>Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <span id="conexion-ws" class="badge bg-warning">
        <i class="ti ti-loader me-1"></i> Conectando...
    </span>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const csrf = document.querySelector('meta[name="csrf-token"]').content;

            // ── Countdowns ────────────────────────────
            let srvCountdown = 30;
            let webCountdown = 60;
            let ultimoGrupoSrv = null;
            let ultimoGrupoWeb = null;

            setInterval(() => {
                srvCountdown--;
                if (srvCountdown <= 0) srvCountdown = 30;
                document.getElementById('srv-proximo-ping').textContent = srvCountdown + 's';
                document.getElementById('srv-countdown-fill').style.width = ((srvCountdown / 30) * 100) +
                    '%';
            }, 1000);

            setInterval(() => {
                webCountdown--;
                if (webCountdown <= 0) webCountdown = 60;
                document.getElementById('web-proximo-ping').textContent = webCountdown + 's';
                document.getElementById('web-countdown-fill').style.width = ((webCountdown / 60) * 100) +
                    '%';
            }, 1000);

            // ── Helpers terminal ──────────────────────
            function addLine(terminalId, html) {
                const content = document.getElementById(terminalId);
                if (!content) return;
                const line = document.createElement('div');
                line.innerHTML = html;
                content.appendChild(line);
                while (content.children.length > 200) content.removeChild(content.firstChild);
                const body = content.closest('.terminal-body');
                if (body) body.scrollTop = body.scrollHeight;
            }

            function formatMs(ms) {
                if (!ms && ms !== 0) return '<span class="t-gray">—</span>';
                if (ms < 10) return `<span class="t-green">${ms}ms</span>`;
                if (ms < 50) return `<span class="t-yellow">${ms}ms</span>`;
                if (ms < 100) return `<span class="t-orange">${ms}ms</span>`;
                return `<span class="t-red">${ms}ms</span>`;
            }

            function icono(estado) {
                return estado === 'ACTIVO' ?
                    '<span class="t-green">✓</span>' :
                    estado === 'INACTIVO' ?
                    '<span class="t-red">✗</span>' :
                    '<span class="t-gray">?</span>';
            }

            // ── WebSocket estado ──────────────────────
            window.Echo.connector.pusher.connection.bind('connected', () => {
                const ws = document.getElementById('conexion-ws');
                ws.className = 'badge bg-success';
                ws.innerHTML = '<i class="ti ti-broadcast me-1"></i> WebSocket Activo';
            });

            window.Echo.connector.pusher.connection.bind('disconnected', () => {
                const ws = document.getElementById('conexion-ws');
                ws.className = 'badge bg-danger';
                ws.innerHTML = '<i class="ti ti-broadcast-off me-1"></i> Desconectado';
            });

            // ── Canal: Servidores ─────────────────────
            window.Echo.channel('monitoreo-servidores')
                .listen('.estado.actualizado', (data) => {
                    const ahora = new Date().toLocaleTimeString('es-BO');

                    if (ultimoGrupoSrv !== data.ultima_verificacion) {
                        ultimoGrupoSrv = data.ultima_verificacion;
                        srvCountdown = 30;
                        addLine('terminal-srv-content', `&nbsp;`);
                        addLine('terminal-srv-content',
                            `<span class="t-gray">──────────────────────────────────────────────────────────</span>`
                            );
                        addLine('terminal-srv-content',
                            `<span class="t-cyan">Ping</span> <span class="t-gray">[${ahora}]</span> <span class="t-white">Verificando {{ $servidores->count() }} servidores...</span>`
                            );
                        addLine('terminal-srv-content',
                            `<span class="t-gray">──────────────────────────────────────────────────────────</span>`
                            );
                    }

                    const msI = formatMs(data.ms_interna);
                    const msE = data.disponibilidad_externa !== 'DESCONOCIDO' && data.ip_externa ?
                        formatMs(data.ms_externa) :
                        '<span class="t-gray">N/A</span>';

                    addLine('terminal-srv-content',
                        `${icono(data.disponibilidad_interna)} ` +
                        `<span class="t-white">${(data.nombre || '').padEnd(22)}</span> ` +
                        `<span class="t-gray">INT</span> <span class="t-cyan">${(data.ip_interna || '').padEnd(16)}</span> ${msI}` +
                        (data.ip_externa ?
                            ` <span class="t-gray">EXT</span> <span class="t-cyan">${(data.ip_externa || '').padEnd(16)}</span> ${msE}` :
                            '')
                    );

                    actualizarCardServidor(data);
                    actualizarStatsServidores();
                });

            // ── Canal: Sistemas Web ───────────────────
            window.Echo.channel('monitoreo-web')
                .listen('.estado.web.actualizado', (data) => {
                    const ahora = new Date().toLocaleTimeString('es-BO');

                    if (ultimoGrupoWeb !== data.ultima_verificacion_web) {
                        ultimoGrupoWeb = data.ultima_verificacion_web;
                        webCountdown = 60;
                        addLine('terminal-web-content', `&nbsp;`);
                        addLine('terminal-web-content',
                            `<span class="t-gray">──────────────────────────────────────────────────────────</span>`
                            );
                        addLine('terminal-web-content',
                            `<span class="t-purple">Ping</span> <span class="t-gray">[${ahora}]</span> <span class="t-white">Verificando {{ $sistemas->count() }} dominios...</span>`
                            );
                        addLine('terminal-web-content',
                            `<span class="t-gray">──────────────────────────────────────────────────────────</span>`
                            );
                    }

                    const ms = formatMs(data.tiempo_respuesta);
                    addLine('terminal-web-content',
                        `${icono(data.disponibilidad_web)} ` +
                        `<span class="t-white">${(data.sigla || data.nombre || '').padEnd(15)}</span> ` +
                        `<span class="t-gray">→</span> <span class="t-cyan">${(data.dominio || '').padEnd(35)}</span> ${ms}`
                    );

                    actualizarCardWeb(data);
                    actualizarStatsWeb();
                });

            // ── Actualizar cards ──────────────────────
            function actualizarCardServidor(data) {
                const id = data.id;
                const interno = data.disponibilidad_interna;
                const externo = data.disponibilidad_externa;
                const card = document.getElementById(`card-srv-${id}`);
                const pulse = document.getElementById(`pulse-srv-${id}`);
                const badgeInt = document.getElementById(`badge-srv-interno-${id}`);
                const badgeExt = document.getElementById(`badge-srv-externo-${id}`);
                const ultima = document.getElementById(`ultima-srv-${id}`);

                if (!card) return;

                card.className = `card monitor-card ${interno.toLowerCase()} h-100`;
                pulse.className = `pulse-${interno.toLowerCase()}`;

                const clsI = interno === 'ACTIVO' ? 'bg-success' : (interno === 'INACTIVO' ? 'bg-danger' :
                    'bg-secondary');
                const clsE = externo === 'ACTIVO' ? 'bg-success' : (externo === 'INACTIVO' ? 'bg-danger' :
                    'bg-secondary');

                badgeInt.className = `badge ${clsI}`;
                badgeInt.style.cssText = 'font-size:0.65rem; min-width:75px';
                badgeInt.textContent = interno + (interno === 'ACTIVO' && data.ms_interna ?
                    ` ${data.ms_interna}ms` : '');

                badgeExt.className = `badge ${clsE}`;
                badgeExt.style.cssText = 'font-size:0.65rem; min-width:75px';
                badgeExt.textContent = externo + (externo === 'ACTIVO' && data.ms_externa ?
                    ` ${data.ms_externa}ms` : '');

                if (ultima && data.ultima_verificacion) {
                    ultima.innerHTML =
                        `<i class="ti ti-clock me-1"></i>${(data.ultima_verificacion.split(' ')[1] ?? data.ultima_verificacion)}`;
                }

                card.style.transform = 'translateY(-3px)';
                setTimeout(() => card.style.transform = '', 400);
            }

            function actualizarCardWeb(data) {
                const id = data.id;
                const estado = data.disponibilidad_web;
                const card = document.getElementById(`card-web-${id}`);
                const pulse = document.getElementById(`pulse-web-${id}`);
                const badge = document.getElementById(`badge-web-${id}`);
                const ultima = document.getElementById(`ultima-web-${id}`);

                if (!card) return;

                card.className = `card monitor-card ${estado.toLowerCase()} h-100`;
                pulse.className = `pulse-${estado.toLowerCase()}`;

                const cls = estado === 'ACTIVO' ? 'bg-success' : (estado === 'INACTIVO' ? 'bg-danger' :
                    'bg-secondary');
                badge.className = `badge ${cls}`;
                badge.style.cssText = 'font-size:0.7rem; min-width:90px';
                badge.textContent = estado + (estado === 'ACTIVO' && data.tiempo_respuesta ?
                    ` ${data.tiempo_respuesta}ms` : '');

                if (ultima && data.ultima_verificacion_web) {
                    ultima.innerHTML = `<i class="ti ti-clock me-1"></i>${data.ultima_verificacion_web}`;
                }

                card.style.transform = 'translateY(-3px)';
                setTimeout(() => card.style.transform = '', 400);
            }

            // ── Estadísticas ──────────────────────────
            function actualizarStatsServidores() {
                let a = 0,
                    i = 0,
                    d = 0;
                document.querySelectorAll('#servidores-container .monitor-card').forEach(c => {
                    if (c.classList.contains('activo')) a++;
                    else if (c.classList.contains('inactivo')) i++;
                    else d++;
                });
                document.getElementById('stat-srv-activos').textContent = a;
                document.getElementById('stat-srv-inactivos').textContent = i;
                document.getElementById('stat-srv-desconocidos').textContent = d;
            }

            function actualizarStatsWeb() {
                let a = 0,
                    i = 0,
                    d = 0;
                document.querySelectorAll('#sistemas-container .monitor-card').forEach(c => {
                    if (c.classList.contains('activo')) a++;
                    else if (c.classList.contains('inactivo')) i++;
                    else d++;
                });
                document.getElementById('stat-web-activos').textContent = a;
                document.getElementById('stat-web-inactivos').textContent = i;
                document.getElementById('stat-web-desconocidos').textContent = d;
            }

            // ── Terminal Ping Individual ──────────────
            let pingInterval = null;
            let pingPausado = false;
            let targetActual = null;
            let pingCount = 0;
            const PING_INT = 5000;

            window.abrirTerminalPing = function(cardEl) {
                const tipo = cardEl.dataset.tipo;

                targetActual = {
                    tipo: tipo,
                    id: cardEl.dataset.id,
                    nombre: cardEl.dataset.nombre,
                    ipInterna: cardEl.dataset.ipInterna,
                    ipExterna: cardEl.dataset.ipExterna,
                    dominio: cardEl.dataset.dominio,
                };

                pingCount = 0;
                pingPausado = false;
                document.getElementById('modal-terminal-content').innerHTML = '';
                document.getElementById('modal-ping-nombre').innerHTML =
                    `<i class="ti ti-terminal me-2"></i>${targetActual.nombre}`;
                document.getElementById('modal-ping-subtitle').textContent = tipo === 'servidor' ?
                    `INT: ${targetActual.ipInterna}${targetActual.ipExterna ? ' | EXT: ' + targetActual.ipExterna : ''}` :
                    `Dominio: ${targetActual.dominio}`;
                document.getElementById('modal-ping-title').textContent =
                    tipo === 'servidor' ? `ping ${targetActual.ipInterna}` : `ping ${targetActual.dominio}`;
                document.getElementById('modal-ping-badge').className = 'badge bg-secondary';
                document.getElementById('modal-ping-badge').textContent = 'Iniciando...';
                document.getElementById('btn-pausar-ping').innerHTML =
                    '<i class="ti ti-player-pause me-1"></i>Pausar';

                const modal = new bootstrap.Modal(document.getElementById('modalPingTerminal'));
                modal.show();
                ejecutarPingIndividual();
                iniciarIntervaloPing();
            };

            function iniciarIntervaloPing() {
                clearInterval(pingInterval);
                let mc = PING_INT / 1000;
                const cntEl = document.getElementById('modal-countdown');
                pingInterval = setInterval(() => {
                    if (!pingPausado) {
                        mc--;
                        cntEl.textContent = mc + 's';
                        if (mc <= 0) {
                            ejecutarPingIndividual();
                            mc = PING_INT / 1000;
                        }
                    }
                }, 1000);
            }

            document.getElementById('modalPingTerminal').addEventListener('hidden.bs.modal', () => {
                clearInterval(pingInterval);
                pingInterval = null;
                targetActual = null;
            });

            document.getElementById('btn-pausar-ping').addEventListener('click', function() {
                pingPausado = !pingPausado;
                this.innerHTML = pingPausado ?
                    '<i class="ti ti-player-play me-1"></i>Reanudar' :
                    '<i class="ti ti-player-pause me-1"></i>Pausar';
            });

            function ejecutarPingIndividual() {
                if (!targetActual) return;
                pingCount++;

                const url = targetActual.tipo === 'servidor' ?
                    `/admin/monitoreo/servidores/${targetActual.id}/ping` :
                    `/admin/monitoreo/sistemas/${targetActual.id}/ping`;

                addModalLine(
                    `<span class="t-gray">──────────────────────────────────────────────────────────</span>`);
                addModalLine(
                    `<span class="t-cyan">Ping #${pingCount}</span> <span class="t-gray">[${new Date().toLocaleTimeString('es-BO')}]</span> <span class="t-white">→ ${targetActual.nombre}</span>`
                    );

                fetch(url, {
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        const res = data.resultado;

                        Object.entries(res).forEach(([key, info]) => {
                            const labelMap = {
                                interna: 'Red Interna',
                                externa: 'Red Externa',
                                dominio: 'Dominio',
                            };
                            const label = labelMap[key] ?? key;

                            addModalLine(`&nbsp;`);
                            addModalLine(
                                `<span class="t-yellow">Haciendo ping a ${info.ip} [${label}] con 32 bytes de datos:</span>`
                                );

                            info.output.forEach(linea => {
                                if (linea.match(/bytes from|time=/i)) {
                                    const msMatch = linea.match(/time[=<]([\d.]+)\s*ms/i);
                                    const ms = msMatch ? parseFloat(msMatch[1]) : null;
                                    const colorMs = !ms ? 't-red' : ms < 10 ? 't-green' : ms <
                                        50 ? 't-yellow' : 't-orange';
                                    addModalLine(
                                        `<span class="t-green">  Respuesta desde ${info.ip}: </span><span class="${colorMs}">${linea.replace(/.*time[=<]/, 'tiempo=')}</span>`
                                        );
                                } else if (linea.match(
                                        /Request timeout|100% packet loss|Unreachable/i)) {
                                    addModalLine(`<span class="t-red">  ✗ ${linea}</span>`);
                                } else if (linea.match(
                                    /min\/avg\/max|rtt|packets transmitted/i)) {
                                    addModalLine(`<span class="t-gray">  ${linea}</span>`);
                                }
                            });

                            const ms = info.ms;
                            addModalLine(info.estado === 'ACTIVO' ?
                                `<span class="t-green">  ✓ ${label}: ACTIVO</span>` +
                                (ms ?
                                    ` <span class="t-gray">— tiempo: </span><span class="${ms < 10 ? 't-green' : ms < 50 ? 't-yellow' : 't-orange'}">${ms}ms</span>` :
                                    '') :
                                `<span class="t-red">  ✗ ${label}: INACTIVO — Sin respuesta</span>`
                            );
                        });

                        const estadoGral = Object.values(res)[0]?.estado ?? 'DESCONOCIDO';
                        const badge = document.getElementById('modal-ping-badge');
                        badge.className =
                            `badge ${estadoGral === 'ACTIVO' ? 'bg-success' : estadoGral === 'INACTIVO' ? 'bg-danger' : 'bg-secondary'}`;
                        badge.textContent = estadoGral;
                        document.getElementById('modal-countdown').textContent = (PING_INT / 1000) + 's';
                    })
                    .catch(err => addModalLine(`<span class="t-red">  ✗ Error: ${err.message}</span>`));
            }

            function addModalLine(html) {
                const content = document.getElementById('modal-terminal-content');
                const line = document.createElement('div');
                line.innerHTML = html;
                content.appendChild(line);
                while (content.children.length > 200) content.removeChild(content.firstChild);
                const terminal = document.getElementById('modal-terminal');
                terminal.scrollTop = terminal.scrollHeight;
            }

        });
    </script>
@endsection
