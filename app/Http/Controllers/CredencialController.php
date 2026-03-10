<?php

namespace App\Http\Controllers;

use App\Models\Credencial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class CredencialController extends Controller
{
    public function index()
    {
        $credenciales = Credencial::orderBy('id', 'desc')->get();
        $credencialesEliminadas = Credencial::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();

        return view('admin.credenciales.index', compact(
            'credenciales',
            'credencialesEliminadas'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'usuario' => 'required|string|max:150',
            'password' => 'required|string|min:6',
            'url_acceso' => 'required|url|max:255',
            'estado' => 'required|in:activo,inactivo',
        ], [
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.max' => 'El usuario no puede exceder 150 caracteres.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'url_acceso.required' => 'La URL de acceso es obligatoria.',
            'url_acceso.url' => 'La URL debe ser válida.',
            'url_acceso.max' => 'La URL no puede exceder 255 caracteres.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
        ]);

        // Encriptar la contraseña
        $validated['password_encrypted'] = Crypt::encryptString($validated['password']);
        unset($validated['password']);

        $credencial = Credencial::create($validated);

        return response()->json([
            'success' => true,
            'credencial' => $credencial
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Credencial $credenciale)
    {
        // No devolvemos la contraseña por seguridad
        return response()->json([
            'credencial' => $credenciale->only(['id', 'usuario', 'url_acceso', 'estado'])
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Credencial $credenciale)
    {
        $validated = $request->validate([
            'usuario' => 'required|string|max:150',
            'password' => 'nullable|string|min:6',
            'url_acceso' => 'required|url|max:255',
            'estado' => 'required|in:activo,inactivo',
            'current_password' => 'required|string',
        ], [
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.max' => 'El usuario no puede exceder 150 caracteres.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
            'url_acceso.required' => 'La URL de acceso es obligatoria.',
            'url_acceso.url' => 'La URL debe ser válida.',
            'url_acceso.max' => 'La URL no puede exceder 255 caracteres.',
            'estado.required' => 'El estado es obligatorio.',
            'estado.in' => 'El estado debe ser activo o inactivo.',
            'current_password.required' => 'Debes ingresar tu contraseña para actualizar.',
        ]);

        // Verificar bloqueo por intentos fallidos
        $lockKey = 'credencial_update_lock_' . Auth::id();
        $attemptsKey = 'credencial_update_attempts_' . Auth::id();

        if (cache()->has($lockKey)) {
            $remainingTime = cache()->get($lockKey) - now()->timestamp;
            return response()->json([
                'success' => false,
                'locked' => true,
                'message' => 'Has excedido el número de intentos. Intenta nuevamente en ' . ceil($remainingTime / 60) . ' minutos.',
                'remaining_seconds' => $remainingTime
            ], 429);
        }

        // Verificar contraseña actual
        if (!Hash::check($validated['current_password'], Auth::user()->password)) {
            $attempts = cache()->get($attemptsKey, 0) + 1;
            cache()->put($attemptsKey, $attempts, now()->addMinutes(15));

            if ($attempts >= 3) {
                // Bloquear por 15 minutos
                cache()->put($lockKey, now()->addMinutes(15)->timestamp, now()->addMinutes(15));
                cache()->forget($attemptsKey);

                // Desloguear al usuario
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'success' => false,
                    'locked' => true,
                    'logout' => true,
                    'message' => 'Has excedido el número de intentos permitidos. Tu sesión ha sido cerrada por seguridad.',
                ], 429);
            }

            return response()->json([
                'success' => false,
                'message' => 'La contraseña es incorrecta.',
                'attempts_remaining' => 3 - $attempts
            ], 401);
        }

        // Contraseña correcta, limpiar intentos
        cache()->forget($attemptsKey);

        // Si se proporciona nueva contraseña, encriptarla
        if (!empty($validated['password'])) {
            $validated['password_encrypted'] = Crypt::encryptString($validated['password']);
        }
        unset($validated['password']);
        unset($validated['current_password']);

        $credenciale->update($validated);

        return response()->json([
            'success' => true,
            'credencial' => $credenciale->fresh()
        ]);
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Credencial $credenciale)
    {
        $credenciale->delete();

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Restore the specified resource from trash.
     */
    public function restore($id)
    {
        $credencial = Credencial::onlyTrashed()->findOrFail($id);
        $credencial->restore();

        return response()->json([
            'success' => true,
            'credencial' => $credencial
        ]);
    }

    /**
     * Verificar contraseña del usuario actual y revelar credencial
     */
    public function verPassword(Request $request, Credencial $credenciale)
    {
        $request->validate([
            'current_password' => 'required|string'
        ], [
            'current_password.required' => 'Debes ingresar tu contraseña.'
        ]);

        // Verificar bloqueo por intentos fallidos
        $lockKey = 'credencial_view_lock_' . Auth::id();
        $attemptsKey = 'credencial_view_attempts_' . Auth::id();

        if (cache()->has($lockKey)) {
            $remainingTime = cache()->get($lockKey) - now()->timestamp;
            return response()->json([
                'success' => false,
                'locked' => true,
                'message' => 'Has excedido el número de intentos. Intenta nuevamente en ' . ceil($remainingTime / 60) . ' minutos.',
                'remaining_seconds' => $remainingTime
            ], 429);
        }

        // Verificar que la contraseña ingresada coincida con la del usuario logueado
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            $attempts = cache()->get($attemptsKey, 0) + 1;
            cache()->put($attemptsKey, $attempts, now()->addMinutes(15));

            if ($attempts >= 3) {
                // Bloquear por 15 minutos
                cache()->put($lockKey, now()->addMinutes(15)->timestamp, now()->addMinutes(15));
                cache()->forget($attemptsKey);

                // Desloguear al usuario
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'success' => false,
                    'locked' => true,
                    'logout' => true,
                    'message' => 'Has excedido el número de intentos permitidos. Tu sesión ha sido cerrada por seguridad.',
                ], 429);
            }

            return response()->json([
                'success' => false,
                'message' => 'La contraseña es incorrecta.',
                'attempts_remaining' => 3 - $attempts
            ], 401);
        }

        // Contraseña correcta, limpiar intentos
        cache()->forget($attemptsKey);

        // Desencriptar y devolver la contraseña
        try {
            $passwordDesencriptado = Crypt::decryptString($credenciale->password_encrypted);

            return response()->json([
                'success' => true,
                'password' => $passwordDesencriptado
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al desencriptar la contraseña.'
            ], 500);
        }
    }
}
