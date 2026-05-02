@extends('layouts.vertical', ['title' => 'Monitoreo de Servidores'])

@section('css')
<style>
    .server-card {
        transition: all 0.4s ease;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
    }
    .server-card.activo    { border-color: #28a745; }
    .server-card.inactivo  { border-color: #dc3545; }
    .server-card.desconocido { border-color: #6c757d; }

    .pulse-activo {
        width: 12px; height: 12px; border-radius: 50%;
        background: #28a745; display: inline-block;
        animation: pulse-green 2s infinite;
    }
    .pulse-inactivo {
        width: 12px; height: 12px; border-radius: 50%;
        background: #dc3545; display: inline-block;
        animation: pulse-red 1s infinite;
    }
    .pulse-desconocido {
        width: 12px; height: 12px; border-radius: 50%;
        background: #6c757d; display: inline-block;
    }
    @keyframes pulse-green {
        0%   { box-shadow: 0 0 0 0 rgba(40,167,69,0.6); }
        70%  { box-shadow: 0 0 0 8px rgba(40,167,69,0); }
        100% { box-shadow: 0 0 0 0 rgba(40,167,69,0); }
    }
    @keyframes pulse-red {
        0%   { box-shadow: 0 0 0 0 rgba(220,53,69,0.6); }
        70%  { box-shadow: 0 0 0 8px rgba(220,53,69,0); }
        100% { box-shadow: 0 0 0 0 rgba(220,53,69,0); }
    }

    .stat-card { border-radius: 12px; border: none; }
    .ultima-verificacion { font-size: 0.75rem; color: #9ca3af; }
    .ip-badge { font-size: 0.75rem; font-family: monospace; }

    #conexion-ws {
        position: fixed; bottom: 20px; right: 20px;
        z-index: 9999; padding: 0.4rem 0.85rem;
        border-radius: 20px; font-size: 0.8rem; font-weight: 600;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.sistemas.index') }}">Inicio</a></li>
                    <li class="breadcrumb-item active">Monitoreo</li>
                </ol>
            </div>
            <h4 class="page-title">
                <i class="ti ti-activity me-2"></i>Monitoreo de Servidores en Tiempo Real
            </h4>
        </div>
    </div>
</div>

{{-- Estadísticas --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card bg-success text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="ti ti-server fs-1"></i>
                <div>
                    <div class="fs-2 fw-bold" id="stat-activos">{{ $totalActivos }}</div>
                    <div class="small">Servidores Activos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-danger text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="ti ti-server-off fs-1"></i>
                <div>
                    <div class="fs-2 fw-bold" id="stat-inactivos">{{ $totalInactivos }}</div>
                    <div class="small">Servidores Inactivos</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-secondary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="ti ti-help fs-1"></i>
                <div>
                    <div class="fs-2 fw-bold" id="stat-desconocidos">{{ $totalDesconocidos }}</div>
                    <div class="small">Sin verificar</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-primary text-white">
            <div class="card-body d-flex align-items-center gap-3">
                <i class="ti ti-clock fs-1"></i>
                <div>
                    <div class="fs-5 fw-bold" id="ultimo-update">Esperando...</div>
                    <div class="small">Última actualización</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tarjetas de servidores --}}
<div class="row g-3" id="servidores-container">
    @foreach ($servidores as $servidor)
        @php
            $estadoInterno = strtolower($servidor->disponibilidad_interna ?? 'desconocido');
            $estadoExterno = strtolower($servidor->disponibilidad_externa ?? 'desconocido');
        @endphp
        <div class="col-lg-4 col-md-6" id="col-servidor-{{ $servidor->id }}">
            <div class="card server-card {{ $estadoInterno }}" id="card-servidor-{{ $servidor->id }}">
                <div class="card-body">

                    {{-- Header --}}
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h5 class="mb-0 fw-bold">{{ $servidor->nombre }}</h5>
                            <small class="text-muted">{{ $servidor->tipo_servidor ?? 'Servidor' }}</small>
                        </div>
                        <span class="pulse-{{ $estadoInterno }}" id="pulse-{{ $servidor->id }}"></span>
                    </div>

                    {{-- IPs --}}
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small"><i class="ti ti-network me-1"></i>IP Interna</span>
                            <span class="ip-badge badge bg-light text-dark">{{ $servidor->ip_interna }}</span>
                        </div>
                        @if ($servidor->ip_externa)
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small"><i class="ti ti-world me-1"></i>IP Externa</span>
                            <span class="ip-badge badge bg-light text-dark">{{ $servidor->ip_externa }}</span>
                        </div>
                        @endif
                    </div>

                    {{-- Estado --}}
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted mb-1">Red Interna</div>
                                <span id="badge-interno-{{ $servidor->id }}"
                                    class="badge {{ $servidor->disponibilidad_interna === 'ACTIVO' ? 'bg-success' : ($servidor->disponibilidad_interna === 'INACTIVO' ? 'bg-danger' : 'bg-secondary') }} w-100">
                                    {{ $servidor->disponibilidad_interna ?? 'DESCONOCIDO' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border rounded p-2 text-center">
                                <div class="small text-muted mb-1">Red Externa</div>
                                <span id="badge-externo-{{ $servidor->id }}"
                                    class="badge {{ $servidor->disponibilidad_externa === 'ACTIVO' ? 'bg-success' : ($servidor->disponibilidad_externa === 'INACTIVO' ? 'bg-danger' : 'bg-secondary') }} w-100">
                                    {{ $servidor->disponibilidad_externa ?? 'DESCONOCIDO' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Última verificación --}}
                    <div class="mt-2 ultima-verificacion text-end" id="ultima-{{ $servidor->id }}">
                        <i class="ti ti-clock me-1"></i>
                        {{ $servidor->ultima_verificacion ? $servidor->ultima_verificacion->format('d/m/Y H:i:s') : 'Sin verificar' }}
                    </div>

                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Badge conexión WS --}}
<span id="conexion-ws" class="badge bg-warning">
    <i class="ti ti-loader me-1"></i> Conectando...
</span>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Estado de conexión WebSocket
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

    // Escuchar eventos de monitoreo
    window.Echo.channel('monitoreo-servidores')
        .listen('.estado.actualizado', (data) => {
            console.log('📡 Servidor actualizado:', data);
            actualizarServidor(data);
            actualizarEstadisticas();
            document.getElementById('ultimo-update').textContent = new Date().toLocaleTimeString('es-BO');
        });

    function actualizarServidor(data) {
        const id        = data.id;
        const interno   = data.disponibilidad_interna;
        const externo   = data.disponibilidad_externa;
        const card      = document.getElementById(`card-servidor-${id}`);
        const pulse     = document.getElementById(`pulse-${id}`);
        const badgeInt  = document.getElementById(`badge-interno-${id}`);
        const badgeExt  = document.getElementById(`badge-externo-${id}`);
        const ultima    = document.getElementById(`ultima-${id}`);

        if (!card) return;

        // Actualizar clase del card
        card.className = `card server-card ${interno.toLowerCase()}`;

        // Actualizar pulse
        pulse.className = `pulse-${interno.toLowerCase()}`;

        // Actualizar badges
        const claseInterno = interno === 'ACTIVO' ? 'bg-success' : (interno === 'INACTIVO' ? 'bg-danger' : 'bg-secondary');
        const claseExterno = externo === 'ACTIVO' ? 'bg-success' : (externo === 'INACTIVO' ? 'bg-danger' : 'bg-secondary');
        badgeInt.className = `badge ${claseInterno} w-100`;
        badgeInt.textContent = interno;
        badgeExt.className = `badge ${claseExterno} w-100`;
        badgeExt.textContent = externo;

        // Actualizar última verificación
        if (data.ultima_verificacion) {
            ultima.innerHTML = `<i class="ti ti-clock me-1"></i> ${data.ultima_verificacion}`;
        }

        // Animación flash
        card.style.transform = 'scale(1.02)';
        setTimeout(() => card.style.transform = 'scale(1)', 300);
    }

    function actualizarEstadisticas() {
        let activos = 0, inactivos = 0, desconocidos = 0;
        document.querySelectorAll('.server-card').forEach(card => {
            if (card.classList.contains('activo'))       activos++;
            else if (card.classList.contains('inactivo')) inactivos++;
            else                                          desconocidos++;
        });
        document.getElementById('stat-activos').textContent     = activos;
        document.getElementById('stat-inactivos').textContent   = inactivos;
        document.getElementById('stat-desconocidos').textContent = desconocidos;
    }

});
</script>
@endsection
