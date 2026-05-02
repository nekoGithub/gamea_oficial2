@extends('layouts.vertical', ['title' => 'Test WebSocket'])

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">
                    <i class="ti ti-broadcast me-2"></i>Test WebSocket - Reverb
                </h4>
            </div>
            <div class="card-body">
                <div id="estado-conexion" class="alert alert-warning">
                    <i class="ti ti-loader me-1"></i> Conectando...
                </div>
                <div id="mensajes" class="border rounded p-3" style="min-height:150px; background:#f8f9fa;">
                    <p class="text-muted text-center mt-4">Esperando mensajes...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    console.log('🔌 Conectando a Reverb...');
    console.log('Echo:', window.Echo);

    if (!window.Echo) {
        document.getElementById('estado-conexion').className = 'alert alert-danger';
        document.getElementById('estado-conexion').innerHTML = '❌ Echo no está disponible';
        return;
    }

    window.Echo.connector.pusher.connection.bind('connected', () => {
        console.log('✅ Conectado a Reverb');
        document.getElementById('estado-conexion').className = 'alert alert-success';
        document.getElementById('estado-conexion').innerHTML = '✅ Conectado a Reverb correctamente';
    });

    window.Echo.connector.pusher.connection.bind('error', (err) => {
        console.error('❌ Error:', err);
        document.getElementById('estado-conexion').className = 'alert alert-danger';
        document.getElementById('estado-conexion').innerHTML = '❌ Error de conexión: ' + JSON.stringify(err);
    });

    window.Echo.channel('test-canal')
        .listen('.test.evento', (data) => {
            console.log('📨 Mensaje recibido:', data);
            const div = document.getElementById('mensajes');
            div.innerHTML = `
                <div class="alert alert-success">
                    <i class="ti ti-check me-1"></i>
                    <strong>Mensaje recibido:</strong> ${data.mensaje}
                    <small class="d-block text-muted">${new Date().toLocaleTimeString()}</small>
                </div>`;
        });
});
</script>
@endsection