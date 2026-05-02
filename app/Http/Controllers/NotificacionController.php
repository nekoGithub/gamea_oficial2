<?php

namespace App\Http\Controllers;

use App\Models\Notificacion;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;

        $this->middleware('can:admin.notificaciones.index')->only('index');
        $this->middleware('can:admin.notificaciones.show')->only('show');
        $this->middleware('can:admin.notificaciones.update')->only(['marcarEnviada', 'reenviar', 'limpiar']);
        $this->middleware('can:admin.notificaciones.destroy')->only('destroy');
    }

    protected $telegramService;

    public function index()
    {
        $notificaciones = Notificacion::with(['sistemaVersion.sistema', 'usuarioEnviado'])
            ->orderBy('fecha', 'desc')
            ->get();

        $estadisticas = [
            'total' => $notificaciones->count(),
            'pendientes' => $notificaciones->where('estado', 'pendiente')->count(),
            'enviadas' => $notificaciones->where('estado', 'enviado')->count(),
            'fallidas' => $notificaciones->where('estado', 'fallido')->count(),
            'criticas' => $notificaciones->where('tipo', 'critica')->count(),
            'altas' => $notificaciones->where('tipo', 'alta')->count(),
        ];

        return view('admin.notificaciones.index', compact('notificaciones', 'estadisticas'));
    }

    public function show(Notificacion $notificacion)
    {
        $notificacion->load(['sistemaVersion.sistema']);

        // Determinar tipo de severidad del mensaje
        $tipo = 'baja';
        if (str_contains($notificacion->mensaje, '[critica]')) {
            $tipo = 'critica';
        } elseif (str_contains($notificacion->mensaje, '[alta]')) {
            $tipo = 'alta';
        } elseif (str_contains($notificacion->mensaje, '[media]')) {
            $tipo = 'media';
        }

        // Limpiar mensaje
        $mensajeLimpio = preg_replace('/\[(critica|alta|media|baja)\]\s*/i', '', $notificacion->mensaje);

        return view('admin.notificaciones.show', [
            'notificacion' => $notificacion,
            'tipo' => $tipo,
            'mensajeLimpio' => $mensajeLimpio
        ]);
    }

    /**
     * Marcar notificación como enviada
     */
    public function marcarEnviada($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->update(['estado' => 'enviado']);

        return response()->json([
            'success' => true,
            'notificacion' => $notificacion
        ]);
    }

    /**
     * Reenviar notificación a Telegram
     */
    public function reenviar($id)
    {
        $notificacion = Notificacion::findOrFail($id);

        $enviado = $this->telegramService->sendMessage($notificacion->mensaje);

        if ($enviado) {
            $notificacion->update(['estado' => 'enviado']);

            return response()->json([
                'success' => true,
                'message' => 'Notificación reenviada correctamente'
            ]);
        }

        $notificacion->update(['estado' => 'fallido']);

        return response()->json([
            'success' => false,
            'message' => 'No se pudo reenviar la notificación'
        ], 500);
    }

    /**
     * Eliminar notificación
     */
    public function destroy($id)
    {
        $notificacion = Notificacion::findOrFail($id);
        $notificacion->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Limpiar notificaciones antiguas (enviadas hace más de 30 días)
     */
    public function limpiar()
    {
        $eliminadas = Notificacion::where('estado', 'enviado')
            ->where('fecha', '<', now()->subDays(30))
            ->delete();

        return response()->json([
            'success' => true,
            'eliminadas' => $eliminadas
        ]);
    }
}
