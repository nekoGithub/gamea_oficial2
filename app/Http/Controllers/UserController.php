<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerificationCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin.users.index')->only('index');
        $this->middleware('can:admin.users.store')->only('store');
        $this->middleware('can:admin.users.show')->only('show');
        $this->middleware('can:admin.users.edit')->only('edit');
        $this->middleware('can:admin.users.update')->only('update');
        $this->middleware('can:admin.users.destroy')->only('destroy');
        $this->middleware('can:admin.users.restore')->only('restore');
    }

    public function index()
    {
        $users = User::withTrashed()->orderBy('id', 'desc')->get();
        $roles = Role::all();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
                'max:100',
            ],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'role' => ['required', 'string', 'exists:roles,name'],
        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'El correo debe ser válido',
            'email.unique' => 'Este correo ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.regex' => 'La contraseña debe contener mayúscula, minúscula, número y carácter especial',
            'password.max' => 'La contraseña no debe superar los 100 caracteres',
            'role.required' => 'Debes seleccionar un rol',
            'role.exists' => 'El rol seleccionado no es válido',
        ]);

        $avatarName = null;
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $avatarName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('avatars', $avatarName, 'public');
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'profile_photo_path' => $avatarName,
        ]);

        // ========================================
        // ENVÍO DE CORREO DE VERIFICACIÓN
        // ========================================
        $code = rand(100000, 999999);
        $user->email_verification_code = $code;
        $user->email_verification_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        try {
            Mail::to($user->email)->send(new EmailVerificationCodeMail($code));
        } catch (\Exception $e) {
            Log::warning("No se pudo enviar correo de verificación al usuario {$user->id}: " . $e->getMessage());
        }
        // ========================================

        $user->assignRole($validated['role']);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', '¡Usuario creado correctamente!');
    }

    public function show(User $user)
    {
        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'profile_photo_path' => $user->profile_photo_path,
                'created_at' => $user->created_at,
            ]
        ]);
    }

    public function edit(User $user)
    {
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'profile_photo_path' => $user->profile_photo_path,
            'roles' => $user->roles->pluck('name'),
            'all_roles' => Role::pluck('name')
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ["required", "email", "unique:users,email,{$user->id}"],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
                'max:100',
            ],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'role' => ['required', 'string', 'exists:roles,name']
        ], [
            'name.required' => 'El nombre es obligatorio',
            'email.required' => 'El correo es obligatorio',
            'email.email' => 'El correo debe ser válido',
            'email.unique' => 'Este correo ya está registrado',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.regex' => 'La contraseña debe contener mayúscula, minúscula, número y carácter especial',
            'password.max' => 'La contraseña no debe superar los 100 caracteres',
            'role.required' => 'Debes seleccionar un rol',
            'role.exists' => 'El rol seleccionado no es válido',
        ]);

        // Actualizar datos básicos
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Actualizar contraseña solo si se proporcionó
        if (!empty($validated['password'])) {
            $user->update(['password' => bcrypt($validated['password'])]);
        }

        // Actualizar avatar si se subió uno nuevo
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $avatarName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('avatars', $avatarName, 'public');
            $user->update(['profile_photo_path' => $avatarName]);
        }

        // Sincronizar rol
        $user->syncRoles([$validated['role']]);

        return response()->json([
            'success' => true,
            'user' => $user
        ]);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado correctamente']);
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return response()->json(['message' => 'Usuario restaurado correctamente']);
    }

    public function checkEmail(Request $request)
    {
        $exists = User::withTrashed()
            ->where('email', $request->email)
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}
