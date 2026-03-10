<?php

namespace App\Console\Commands;

use App\Models\BaseDato;
use App\Models\Credencial;
use App\Models\Notificacion;
use App\Models\Servidor;
use App\Models\Sistema;
use App\Models\SistemaVersion;
use App\Models\Ssl;
use App\Models\Tecnologia;
use App\Services\TelegramService;
use Illuminate\Console\Command;

class CheckIntegridadSistema extends Command
{
    protected $signature = 'integridad:verificar 
                            {--tipo=all : Tipo de verificación: all, servidores, bases-datos, sistemas, versiones, ssl}
                            {--telegram : Enviar resumen a Telegram}';

    protected $description = 'Verificar integridad del sistema y detectar problemas';

    protected $alertasDetectadas = [];
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        parent::__construct();
        $this->telegramService = $telegramService;
    }

    public function handle()
    {
        $tipo = $this->option('tipo');
        $this->info("🔍 Verificando integridad del sistema: {$tipo}");
        $this->newLine();

        // Ejecutar verificaciones según el tipo
        if ($tipo === 'all' || $tipo === 'servidores') {
            $this->verificarServidores();
        }

        if ($tipo === 'all' || $tipo === 'bases-datos') {
            $this->verificarBasesDatos();
        }

        if ($tipo === 'all' || $tipo === 'sistemas') {
            $this->verificarSistemas();
        }

        if ($tipo === 'all' || $tipo === 'versiones') {
            $this->verificarVersiones();
        }

        if ($tipo === 'all' || $tipo === 'ssl') {
            $this->verificarSSL();
        }

        if ($tipo === 'all' || $tipo === 'tecnologias') {
            $this->verificarTecnologias();
        }

        if ($tipo === 'all' || $tipo === 'credenciales') {
            $this->verificarCredenciales();
        }

        // Mostrar resumen
        $this->mostrarResumen();

        // Enviar a Telegram si se solicita
        if ($this->option('telegram') && count($this->alertasDetectadas) > 0) {
            $this->enviarTelegram();
        }

        return 0;
    }

    // ========== VERIFICADORES ==========

    protected function verificarServidores()
    {
        $this->info('📡 Verificando Servidores...');

        // 1. Servidores activos sin sistemas
        $servidoresSinSistemas = Servidor::where('estado', 'activo')
            ->whereDoesntHave('versiones')
            ->get();

        foreach ($servidoresSinSistemas as $servidor) {
            $this->crearAlerta(
                tipo: 'servidor',
                severidad: 'alta',
                titulo: "Servidor sin sistemas asociados",
                mensaje: "El servidor '{$servidor->nombre}' ({$servidor->ip}) está activo pero no tiene sistemas desplegados.",
                entidad_id: null,
                metadata: [
                    'servidor_id' => $servidor->id,
                    'servidor_nombre' => $servidor->nombre,
                    'servidor_ip' => $servidor->ip_interna,
                ]
            );
        }

        // 2. Servidores inactivos con sistemas en uso
        $servidoresInactivosConSistemas = Servidor::where('estado', 'inactivo')
            ->whereHas('versiones', function ($q) {
                $q->where('estado', 'estable');
            })
            ->get();

        foreach ($servidoresInactivosConSistemas as $servidor) {
            $sistemasCount = $servidor->versiones()->where('estado', 'estable')->count();

            $this->crearAlerta(
                tipo: 'servidor',
                severidad: 'critica',
                titulo: "Servidor inactivo con sistemas en producción",
                mensaje: "El servidor '{$servidor->nombre}' está marcado como inactivo pero tiene {$sistemasCount} sistema(s) en estado estable.",
                entidad_id: null,
                metadata: [
                    'servidor_id' => $servidor->id,
                    'servidor_nombre' => $servidor->nombre,
                    'sistemas_afectados' => $sistemasCount,
                ]
            );
        }

        // 3. Servidores sin IP asignada
        $servidoresSinIP = Servidor::where('estado', 'activo')
            ->where(function ($q) {
                $q->whereNull('ip_interna')
                    ->orWhere('ip_interna', '');
            })
            ->get();

        foreach ($servidoresSinIP as $servidor) {
            $this->crearAlerta(
                tipo: 'servidor',
                severidad: 'media',
                titulo: "Servidor sin IP asignada",
                mensaje: "El servidor '{$servidor->nombre}' no tiene dirección IP configurada.",
                entidad_id: null,
                metadata: [
                    'servidor_id' => $servidor->id,
                    'servidor_nombre' => $servidor->nombre,
                ]
            );
        }

        $this->line("  ✅ Servidores verificados");
    }

    protected function verificarBasesDatos()
    {
        $this->info('🗄️  Verificando Bases de Datos...');

        // 1. Bases de datos activas sin sistemas
        $bdSinSistemas = BaseDato::where('estado', 'activo')
            ->whereDoesntHave('versiones')
            ->get();

        foreach ($bdSinSistemas as $bd) {
            $this->crearAlerta(
                tipo: 'base_datos',
                severidad: 'alta',
                titulo: "Base de datos sin sistemas asociados",
                mensaje: "La base de datos '{$bd->nombre}' ({$bd->gestor}) está activa pero no tiene sistemas asociados.",
                entidad_id: null,
                metadata: [
                    'bd_id' => $bd->id,
                    'bd_nombre' => $bd->nombre,
                    'bd_gestor' => $bd->gestor,
                ]
            );
        }

        // 2. Bases de datos inactivas con sistemas en uso
        $bdInactivasConSistemas = BaseDato::where('estado', 'inactivo')
            ->whereHas('versiones', function ($q) {
                $q->where('estado', 'estable');
            })
            ->get();

        foreach ($bdInactivasConSistemas as $bd) {
            $sistemasCount = $bd->versiones()->where('estado', 'estable')->count();

            $this->crearAlerta(
                tipo: 'base_datos',
                severidad: 'critica',
                titulo: "Base de datos inactiva con sistemas en producción",
                mensaje: "La base de datos '{$bd->nombre}' está marcada como inactiva pero tiene {$sistemasCount} sistema(s) en estado estable.",
                entidad_id: null,
                metadata: [
                    'bd_id' => $bd->id,
                    'bd_nombre' => $bd->nombre,
                    'sistemas_afectados' => $sistemasCount,
                ]
            );
        }

        $this->line("  ✅ Bases de datos verificadas");
    }

    protected function verificarSistemas()
    {
        $this->info('💻 Verificando Sistemas...');

        // 1. Sistemas sin versiones
        $sistemasSinVersiones = Sistema::whereDoesntHave('versiones')->get();

        foreach ($sistemasSinVersiones as $sistema) {
            $this->crearAlerta(
                tipo: 'sistema',
                severidad: 'alta',
                titulo: "Sistema sin versiones",
                mensaje: "El sistema '{$sistema->nombre}' no tiene ninguna versión registrada.",
                entidad_id: null,
                metadata: [
                    'sistema_id' => $sistema->id,
                    'sistema_nombre' => $sistema->nombre,
                ]
            );
        }

        // 2. Sistemas sin versión actual (pero con versiones)
        $sistemasSinVersionActual = Sistema::whereHas('versiones')
            ->whereDoesntHave('versiones', function ($q) {
                $q->where('es_actual', true);
            })
            ->get();

        foreach ($sistemasSinVersionActual as $sistema) {
            $versionesCount = $sistema->versiones()->count();

            $this->crearAlerta(
                tipo: 'sistema',
                severidad: 'media',
                titulo: "Sistema sin versión actual marcada",
                mensaje: "El sistema '{$sistema->nombre}' tiene {$versionesCount} versión(es) pero ninguna está marcada como actual.",
                entidad_id: null,
                metadata: [
                    'sistema_id' => $sistema->id,
                    'sistema_nombre' => $sistema->nombre,
                    'total_versiones' => $versionesCount,
                ]
            );
        }

        // 3. Sistemas sin unidad asignada
        $sistemasSinUnidad = Sistema::whereNull('unidad_id')->get();

        foreach ($sistemasSinUnidad as $sistema) {
            $this->crearAlerta(
                tipo: 'sistema',
                severidad: 'baja',
                titulo: "Sistema sin unidad asignada",
                mensaje: "El sistema '{$sistema->nombre}' no tiene unidad organizacional asignada.",
                entidad_id: null,
                metadata: [
                    'sistema_id' => $sistema->id,
                    'sistema_nombre' => $sistema->nombre,
                ]
            );
        }

        $this->line("  ✅ Sistemas verificados");
    }

    protected function verificarVersiones()
    {
        $this->info('📦 Verificando Versiones...');

        // 1. Versiones sin código fuente
        $versionesSinCodigo = SistemaVersion::whereNull('codigo_fuente')
            ->orWhere('codigo_fuente', '')
            ->with('sistema')
            ->get();

        foreach ($versionesSinCodigo as $version) {
            $this->crearAlerta(
                tipo: 'version',
                severidad: 'alta',
                titulo: "Versión sin código fuente",
                mensaje: "La versión {$version->numero_version} del sistema '{$version->sistema->nombre}' no tiene código fuente almacenado.",
                entidad_id: $version->id,
                metadata: [
                    'version_id' => $version->id,
                    'sistema_nombre' => $version->sistema->nombre,
                    'numero_version' => $version->numero_version,
                ]
            );
        }

        // 2. Versiones sin manual técnico
        $versionesSinManualTecnico = SistemaVersion::where(function ($q) {
            $q->whereNull('manual_tecnico')
                ->orWhere('manual_tecnico', '');
        })
            ->with('sistema')
            ->get();

        foreach ($versionesSinManualTecnico as $version) {
            $this->crearAlerta(
                tipo: 'version',
                severidad: 'media',
                titulo: "Versión sin manual técnico",
                mensaje: "La versión {$version->numero_version} del sistema '{$version->sistema->nombre}' no tiene manual técnico.",
                entidad_id: $version->id,
                metadata: [
                    'version_id' => $version->id,
                    'sistema_nombre' => $version->sistema->nombre,
                    'numero_version' => $version->numero_version,
                ]
            );
        }

        // 3. Versiones deprecated que están marcadas como actuales
        $versionesDeprecatedActuales = SistemaVersion::where('estado', 'deprecated')
            ->where('es_actual', true)
            ->with('sistema')
            ->get();

        foreach ($versionesDeprecatedActuales as $version) {
            $this->crearAlerta(
                tipo: 'version',
                severidad: 'critica',
                titulo: "Versión obsoleta marcada como actual",
                mensaje: "La versión {$version->numero_version} del sistema '{$version->sistema->nombre}' está deprecated pero marcada como actual.",
                entidad_id: $version->id,
                metadata: [
                    'version_id' => $version->id,
                    'sistema_nombre' => $version->sistema->nombre,
                    'numero_version' => $version->numero_version,
                ]
            );
        }

        // 4. Versiones antiguas (>1 año sin actualizar)
        $versionesAntiguas = SistemaVersion::where('es_actual', true)
            ->where('fecha_lanzamiento', '<', now()->subYear())
            ->with('sistema')
            ->get();

        foreach ($versionesAntiguas as $version) {
            $mesesAntiguedad = now()->diffInMonths($version->fecha_lanzamiento);

            $this->crearAlerta(
                tipo: 'version',
                severidad: 'baja',
                titulo: "Versión antigua sin actualizar",
                mensaje: "La versión {$version->numero_version} del sistema '{$version->sistema->nombre}' tiene {$mesesAntiguedad} meses sin actualizar.",
                entidad_id: $version->id,
                metadata: [
                    'version_id' => $version->id,
                    'sistema_nombre' => $version->sistema->nombre,
                    'numero_version' => $version->numero_version,
                    'meses_antiguedad' => $mesesAntiguedad,
                ]
            );
        }

        $this->line("  ✅ Versiones verificadas");
    }

    protected function verificarSSL()
    {
        $this->info('🔒 Verificando Certificados SSL...');

        // 1. SSL vencidos
        $sslVencidos = Ssl::where('fecha_expiracion', '<', now())
            ->where('estado', 'vencido')
            ->get();

        foreach ($sslVencidos as $ssl) {
            $diasVencido = now()->diffInDays($ssl->fecha_expiracion);

            $this->crearAlerta(
                tipo: 'ssl',
                severidad: 'critica',
                titulo: "Certificado SSL vencido",
                mensaje: "El certificado '{$ssl->nombre}' venció hace {$diasVencido} días.",
                entidad_id: null,
                metadata: [
                    'ssl_id' => $ssl->id,
                    'ssl_nombre' => $ssl->nombre,
                    'fecha_expiracion' => $ssl->fecha_expiracion->format('d/m/Y'),
                    'dias_vencido' => $diasVencido,
                ]
            );
        }

        // 2. SSL próximos a vencer (30 días)
        $sslProximosVencer = Ssl::whereBetween('fecha_expiracion', [now(), now()->addDays(30)])
            ->where('estado', 'proximo_vencer')
            ->get();

        foreach ($sslProximosVencer as $ssl) {
            $diasRestantes = now()->diffInDays($ssl->fecha_expiracion);

            $this->crearAlerta(
                tipo: 'ssl',
                severidad: 'alta',
                titulo: "Certificado SSL próximo a vencer",
                mensaje: "El certificado '{$ssl->nombre}' vence en {$diasRestantes} días.",
                entidad_id: null,
                metadata: [
                    'ssl_id' => $ssl->id,
                    'ssl_nombre' => $ssl->nombre,
                    'fecha_expiracion' => $ssl->fecha_expiracion->format('d/m/Y'),
                    'dias_restantes' => $diasRestantes,
                ]
            );
        }

        $this->line("  ✅ Certificados SSL verificados");
    }

    protected function verificarTecnologias()
    {
        $this->info('🔧 Verificando Tecnologías...');

        // 1. Tecnologías obsoletas en uso
        $tecnologiasObsoletas = Tecnologia::where('estado', 'inactivo')
            ->whereHas('versiones', function ($q) {
                $q->where('estado', 'estable');
            })
            ->get();

        foreach ($tecnologiasObsoletas as $tecnologia) {
            $sistemasCount = $tecnologia->versiones()->where('estado', 'estable')->count();

            $this->crearAlerta(
                tipo: 'tecnologia',
                severidad: 'media',
                titulo: "Tecnología obsoleta en uso",
                mensaje: "La tecnología '{$tecnologia->nombre}' está obsoleta pero se usa en {$sistemasCount} sistema(s) estable(s).",
                entidad_id: null,
                metadata: [
                    'tecnologia_id' => $tecnologia->id,
                    'tecnologia_nombre' => $tecnologia->nombre,
                    'sistemas_afectados' => $sistemasCount,
                ]
            );
        }

        // 2. Tecnologías sin sistemas asociados
        $tecnologiasSinUso = Tecnologia::where('estado', 'activo')
            ->whereDoesntHave('versiones')
            ->get();

        foreach ($tecnologiasSinUso as $tecnologia) {
            $this->crearAlerta(
                tipo: 'tecnologia',
                severidad: 'baja',
                titulo: "Tecnología sin uso",
                mensaje: "La tecnología '{$tecnologia->nombre}' está activa pero no se usa en ningún sistema.",
                entidad_id: null,
                metadata: [
                    'tecnologia_id' => $tecnologia->id,
                    'tecnologia_nombre' => $tecnologia->nombre,
                ]
            );
        }

        $this->line("  ✅ Tecnologías verificadas");
    }

    protected function verificarCredenciales()
    {
        $this->info('🔐 Verificando Credenciales...');

        // 1. Credenciales activas sin uso
        $credencialesSinUso = Credencial::where('estado', 'activo')
            ->whereDoesntHave('versiones')
            ->get();

        foreach ($credencialesSinUso as $credencial) {
            $this->crearAlerta(
                tipo: 'credencial',
                severidad: 'baja',
                titulo: "Credencial sin uso",
                mensaje: "La credencial '{$credencial->usuario}' está activa pero no se usa en ningún sistema.",
                entidad_id: null,
                metadata: [
                    'credencial_id' => $credencial->id,
                    'credencial_usuario' => $credencial->usuario,
                ]
            );
        }

        // 2. Credenciales inactivas en uso
        $credencialesInactivasEnUso = Credencial::where('estado', 'inactivo')
            ->whereHas('versiones', function ($q) {
                $q->where('estado', 'estable');
            })
            ->get();

        foreach ($credencialesInactivasEnUso as $credencial) {
            $sistemasCount = $credencial->versiones()->where('estado', 'estable')->count();

            $this->crearAlerta(
                tipo: 'credencial',
                severidad: 'alta',
                titulo: "Credencial inactiva en uso",
                mensaje: "La credencial '{$credencial->usuario}' está inactiva pero se usa en {$sistemasCount} sistema(s) estable(s).",
                entidad_id: null,
                metadata: [
                    'credencial_id' => $credencial->id,
                    'credencial_usuario' => $credencial->usuario,
                    'sistemas_afectados' => $sistemasCount,
                ]
            );
        }

        $this->line("  ✅ Credenciales verificadas");
    }

    // ========== HELPERS ==========

    protected function crearAlerta($tipo, $severidad, $titulo, $mensaje, $entidad_id = null, $metadata = [])
    {
        // Guardar en BD (tabla notificaciones)
        $notificacion = Notificacion::create([
            'sistema_version_id' => $entidad_id, // Usamos este campo aunque sea polimórfico
            'fecha' => now(),
            'estado' => 'pendiente',
            'mensaje' => "[{$severidad}] {$titulo}: {$mensaje}",
            'usuario_enviado' => null, // Sistema automático
        ]);

        // Agregar a array para resumen
        $this->alertasDetectadas[] = [
            'tipo' => $tipo,
            'severidad' => $severidad,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'metadata' => $metadata,
            'notificacion_id' => $notificacion->id,
        ];

        // Mostrar en consola
        $icon = match ($severidad) {
            'critica' => '🔴',
            'alta' => '🟠',
            'media' => '🟡',
            'baja' => '🟢',
            default => '🔵',
        };

        $this->line("  {$icon} {$titulo}");
    }

    protected function mostrarResumen()
    {
        $this->newLine();
        $this->info('📊 RESUMEN DE VERIFICACIÓN:');
        $this->newLine();

        $porSeveridad = collect($this->alertasDetectadas)->groupBy('severidad');

        $criticas = $porSeveridad->get('critica', collect())->count();
        $altas = $porSeveridad->get('alta', collect())->count();
        $medias = $porSeveridad->get('media', collect())->count();
        $bajas = $porSeveridad->get('baja', collect())->count();

        $this->line("  🔴 Críticas: {$criticas}");
        $this->line("  🟠 Altas: {$altas}");
        $this->line("  🟡 Medias: {$medias}");
        $this->line("  🟢 Bajas: {$bajas}");
        $this->newLine();
        $this->line("  Total: " . count($this->alertasDetectadas) . " alertas detectadas");
        $this->newLine();
    }

    protected function enviarTelegram()
    {
        $this->info('📱 Enviando resumen a Telegram...');

        $porSeveridad = collect($this->alertasDetectadas)->groupBy('severidad');

        $criticas = $porSeveridad->get('critica', collect());
        $altas = $porSeveridad->get('alta', collect());
        $medias = $porSeveridad->get('media', collect());
        $bajas = $porSeveridad->get('baja', collect());

        // ========== ENCABEZADO ==========
        $mensaje = "⚠️ <b>Verificación de integridad del sistema</b>\n\n";
        $mensaje .= "━━━━━━━━━━━━━━\n\n";

        // ========== RESUMEN CON EMOJIS DE COLORES ==========
        $mensaje .= "<b>Resumen de alertas</b>\n";
        $mensaje .= "🔴 Críticas: <b>" . $criticas->count() . "</b>\n";
        $mensaje .= "🟠 Altas: <b>" . $altas->count() . "</b>\n";
        $mensaje .= "🟡 Medias: <b>" . $medias->count() . "</b>\n";
        $mensaje .= "🟢 Bajas: <b>" . $bajas->count() . "</b>\n\n";

        $mensaje .= "━━━━━━━━━━━━━━\n\n";

        // ========== ALERTAS CRÍTICAS ==========
        if ($criticas->count() > 0) {
            $mensaje .= "🔴 <b>Alertas críticas</b>\n";
            foreach ($criticas->take(5) as $alerta) {
                $mensaje .= "• {$alerta['titulo']}\n";
            }
            if ($criticas->count() > 5) {
                $mensaje .= "• ... y " . ($criticas->count() - 5) . " más\n";
            }
            $mensaje .= "\n";
        }

        // ========== ALERTAS ALTAS ==========
        if ($altas->count() > 0) {
            $mensaje .= "🟠 <b>Alertas de alta prioridad</b>\n";
            foreach ($altas->take(5) as $alerta) {
                $mensaje .= "• {$alerta['titulo']}\n";
            }
            if ($altas->count() > 5) {
                $mensaje .= "• ... y " . ($altas->count() - 5) . " más\n";
            }
            $mensaje .= "\n";
        }

        // ========== ALERTAS MEDIAS (OPCIONAL) ==========
        if ($medias->count() > 0) {
            $mensaje .= "🟡 <b>Alertas de prioridad media</b>\n";
            foreach ($medias->take(3) as $alerta) {
                $mensaje .= "• {$alerta['titulo']}\n";
            }
            if ($medias->count() > 3) {
                $mensaje .= "• ... y " . ($medias->count() - 3) . " más\n";
            }
            $mensaje .= "\n";
        }

        // ========== FOOTER ==========
        $mensaje .= "━━━━━━━━━━━━━━\n\n";
        $mensaje .= "Verificado: " . now()->format('d/m/Y H:i');

        // Enviar
        $enviado = $this->telegramService->sendMessage($mensaje);

        if ($enviado) {
            $this->info('✅ Resumen enviado a Telegram');

            // Marcar notificaciones como enviadas
            foreach ($this->alertasDetectadas as $alerta) {
                Notificacion::find($alerta['notificacion_id'])->update(['estado' => 'enviado']);
            }
        } else {
            $this->error('❌ Error al enviar a Telegram');
        }
    }
}
