<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:profile.show')->only(['show', 'updateInfo', 'updatePassword']);
    }
    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    public function updateInfo(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
        ], [
            'name.required'  => 'El nombre es obligatorio',
            'name.max'       => 'El nombre no debe superar 50 caracteres',
            'email.required' => 'El correo es obligatorio',
            'email.email'    => 'El correo debe ser válido',
            'email.unique'   => 'Este correo ya está en uso',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        if ($request->hasFile('avatar')) {
            $file       = $request->file('avatar');
            $avatarName = time() . '_' . $file->getClientOriginalName();
            $file->storeAs('avatars', $avatarName, 'public');
            $user->update(['profile_photo_path' => $avatarName]);
        }

        return back()->with('success_info', 'Perfil actualizado correctamente');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:100',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
                'confirmed'
            ],
        ], [
            'current_password.required' => 'La contraseña actual es obligatoria',
            'password.required'         => 'La nueva contraseña es obligatoria',
            'password.min'              => 'La contraseña debe tener al menos 8 caracteres',
            'password.max'              => 'La contraseña no debe superar 100 caracteres',
            'password.regex'            => 'Debe contener mayúscula, minúscula, número y carácter especial',
            'password.confirmed'        => 'Las contraseñas no coinciden',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return response()->json([
                'errors' => ['current_password' => ['La contraseña actual es incorrecta']]
            ], 422);
        }

        Auth::user()->update(['password' => bcrypt($request->password)]);

        return response()->json(['success' => true]);
    }
}
