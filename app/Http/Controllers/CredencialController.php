<?php

namespace App\Http\Controllers;

use App\Models\Credencial;
use App\Models\Sistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Auth;

class CredencialController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.credenciales.index')->only('index');
        $this->middleware('can:admin.credenciales.store')->only('store');
        $this->middleware('can:admin.credenciales.show')->only('verPassword');
        $this->middleware('can:admin.credenciales.edit')->only('edit');
        $this->middleware('can:admin.credenciales.update')->only('update');
        $this->middleware('can:admin.credenciales.destroy')->only('destroy');
        $this->middleware('can:admin.credenciales.restore')->only('restore');
    }

    public function index()
    {
        $credenciales = Credencial::with('sistema')->orderBy('id', 'desc')->get();
        $credencialesEliminadas = Credencial::with('sistema')->onlyTrashed()->orderBy('deleted_at', 'desc')->get();
        $sistemas = Sistema::where('estado', 'activo')->whereNull('deleted_at')->orderBy('nombre')->get();

        return view('admin.credenciales.index', compact(
            'credenciales',
            'credencialesEliminadas',
            'sistemas'
        ));
    }

    /**
     * Buscar sistemas por dominio o sigla (para el autocomplete AJAX)
     */
    public function buscarSistema(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        /* $sistemas = Sistema::where(function ($q) use ($query) {
            $q->where('nombre', 'like', "%{$query}%")
                ->orWhere('url', 'like', "%{$query}%"); */
        $sistemas = Sistema::where(function ($q) use ($query) {
            $q->where('dominio', 'like', "%{$query}%")
                ->orWhere('sigla', 'like', "%{$query}%");
        })
            ->where('estado', 'activo')
            ->whereNull('deleted_at')
            ->select('id', 'dominio', 'sigla')
            ->limit(10)
            ->get()
            ->map(fn($s) => [
                'id'    => $s->id,
                'texto' => $s->nombre . ($s->url ? ' — ' . $s->sigla : ''),
                'nombre' => $s->nombre,
                'dominio'   => $s->url ?? '',
            ]);

        return response()->json($sistemas);
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sistema_id' => 'required|exists:sistemas,id',
            'usuario'    => 'required|string|max:150',
            'password'   => 'required|string|min:6|max:20',
            'estado'     => 'required|in:activo,inactivo',
        ], [
            'sistema_id.required' => 'Debes seleccionar un sistema.',
            'sistema_id.exists'   => 'El sistema seleccionado no es válido.',
            'usuario.required'    => 'El usuario es obligatorio.',
            'usuario.max'         => 'El usuario no puede exceder 150 caracteres.',
            'password.required'   => 'La contraseña es obligatoria.',
            'password.min'        => 'La contraseña debe tener al menos 6 caracteres.',
            'password.max'        => 'La contraseña no puede exceder 20 caracteres.',
            'estado.required'     => 'El estado es obligatorio.',
            'estado.in'           => 'El estado debe ser activo o inactivo.',
        ]);

        $validated['password_encrypted'] = Crypt::encryptString($validated['password']);
        unset($validated['password']);

        $credencial = Credencial::create($validated);

        return response()->json([
            'success'    => true,
            'credencial' => $credencial->load('sistema'),
        ]);
    }

    /**
     * Edit
     */
    public function edit(Credencial $credenciale)
    {
        return response()->json([
            'credencial' => [
                'id'         => $credenciale->id,
                'sistema_id' => $credenciale->sistema_id,
                'sistema_texto' => $credenciale->sistema
                    ? $credenciale->sistema->nombre . ($credenciale->sistema->url ? ' — ' . $credenciale->sistema->url : '')
                    : '',
                'usuario'    => $credenciale->usuario,
                'estado'     => $credenciale->estado,
            ]
        ]);
    }

    /**
     * Update
     */
    public function update(Request $request, Credencial $credenciale)
    {
        $validated = $request->validate([
            'sistema_id'       => 'required|exists:sistemas,id',
            'usuario'          => 'required|string|max:150',
            'password'         => 'nullable|string|min:6|max:20',
            'estado'           => 'required|in:activo,inactivo',
            'current_password' => 'required|string',
        ], [
            'sistema_id.required'       => 'Debes seleccionar un sistema.',
            'sistema_id.exists'         => 'El sistema seleccionado no es válido.',
            'usuario.required'          => 'El usuario es obligatorio.',
            'usuario.max'               => 'El usuario no puede exceder 150 caracteres.',
            'password.min'              => 'La contraseña debe tener al menos 6 caracteres.',
            'password.max'              => 'La contraseña no puede exceder 20 caracteres.',
            'estado.required'           => 'El estado es obligatorio.',
            'estado.in'                 => 'El estado debe ser activo o inactivo.',
            'current_password.required' => 'Debes ingresar tu contraseña para actualizar.',
        ]);

        // Bloqueo por intentos
        $lockKey     = 'credencial_update_lock_' . Auth::id();
        $attemptsKey = 'credencial_update_attempts_' . Auth::id();

        if (cache()->has($lockKey)) {
            $remainingTime = cache()->get($lockKey) - now()->timestamp;
            return response()->json([
                'success'          => false,
                'locked'           => true,
                'message'          => 'Has excedido el número de intentos. Intenta nuevamente en ' . ceil($remainingTime / 60) . ' minutos.',
                'remaining_seconds' => $remainingTime
            ], 429);
        }

        if (!Hash::check($validated['current_password'], Auth::user()->password)) {
            $attempts = cache()->get($attemptsKey, 0) + 1;
            cache()->put($attemptsKey, $attempts, now()->addMinutes(15));

            if ($attempts >= 3) {
                cache()->put($lockKey, now()->addMinutes(15)->timestamp, now()->addMinutes(15));
                cache()->forget($attemptsKey);
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'success' => false,
                    'locked'  => true,
                    'logout'  => true,
                    'message' => 'Has excedido el número de intentos. Tu sesión ha sido cerrada por seguridad.',
                ], 429);
            }

            return response()->json([
                'success'           => false,
                'message'           => 'La contraseña es incorrecta.',
                'attempts_remaining' => 3 - $attempts
            ], 401);
        }

        cache()->forget($attemptsKey);

        if (!empty($validated['password'])) {
            $validated['password_encrypted'] = Crypt::encryptString($validated['password']);
        }
        unset($validated['password'], $validated['current_password']);

        $credenciale->update($validated);

        return response()->json([
            'success'    => true,
            'credencial' => $credenciale->fresh()->load('sistema'),
        ]);
    }

    /**
     * Destroy (soft delete)
     */
    public function destroy(Credencial $credenciale)
    {
        $credenciale->delete();
        return response()->json(['success' => true]);
    }

    /**
     * Restore
     */
    public function restore($id)
    {
        $credencial = Credencial::onlyTrashed()->findOrFail($id);
        $credencial->restore();
        return response()->json(['success' => true, 'credencial' => $credencial]);
    }

    /**
     * Ver contraseña desencriptada
     */
    public function verPassword(Request $request, Credencial $credenciale)
    {
        $request->validate([
            'current_password' => 'required|string'
        ], [
            'current_password.required' => 'Debes ingresar tu contraseña.'
        ]);

        $lockKey     = 'credencial_view_lock_' . Auth::id();
        $attemptsKey = 'credencial_view_attempts_' . Auth::id();

        if (cache()->has($lockKey)) {
            $remainingTime = cache()->get($lockKey) - now()->timestamp;
            return response()->json([
                'success'          => false,
                'locked'           => true,
                'message'          => 'Has excedido el número de intentos. Intenta nuevamente en ' . ceil($remainingTime / 60) . ' minutos.',
                'remaining_seconds' => $remainingTime
            ], 429);
        }

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            $attempts = cache()->get($attemptsKey, 0) + 1;
            cache()->put($attemptsKey, $attempts, now()->addMinutes(15));

            if ($attempts >= 3) {
                cache()->put($lockKey, now()->addMinutes(15)->timestamp, now()->addMinutes(15));
                cache()->forget($attemptsKey);
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'success' => false,
                    'locked'  => true,
                    'logout'  => true,
                    'message' => 'Has excedido el número de intentos. Tu sesión ha sido cerrada por seguridad.',
                ], 429);
            }

            return response()->json([
                'success'           => false,
                'message'           => 'La contraseña es incorrecta.',
                'attempts_remaining' => 3 - $attempts
            ], 401);
        }

        cache()->forget($attemptsKey);

        try {
            $passwordDesencriptado = Crypt::decryptString($credenciale->password_encrypted);
            return response()->json(['success' => true, 'password' => $passwordDesencriptado]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al desencriptar la contraseña.'], 500);
        }
    }
}
