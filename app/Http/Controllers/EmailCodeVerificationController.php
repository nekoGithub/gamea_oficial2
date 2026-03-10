<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerificationCodeMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class EmailCodeVerificationController extends Controller
{
    public function showForm(Request $request)
    {
        $email = null;
        $remaining = 0;

        if ($request->has('email')) {
            try {
                $email = decrypt($request->email);
                $user = User::where('email', $email)->first();

                if ($user && $user->email_verification_expires_at) {
                    $remaining = now()->diffInSeconds($user->email_verification_expires_at, false);
                    $remaining = max($remaining, 0);
                }
            } catch (\Exception $e) {
                return redirect()->route('login')
                    ->withErrors(['email' => 'El enlace de verificación no es válido.']);
            }
        }

        return view('auth.verify-code', compact('email', 'remaining'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Usuario no encontrado.']);
        }

        if ($user->email_verification_code !== $request->code) {
            return back()->withErrors(['code' => 'El código ingresado es incorrecto.']);
        }

        if (now()->greaterThan($user->email_verification_expires_at)) {
            return back()->withErrors(['code' => 'El código ha expirado, solicita uno nuevo.']);
        }

        $user->email_verified_at = now();
        $user->email_verification_code = null;
        $user->email_verification_expires_at = null;
        $user->save();

        Auth::login($user);

        return redirect()->route('dashboard')->with('status', 'Correo verificado correctamente.');
    }

    public function resend(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->firstOrFail();

        $code = rand(100000, 999999);
        $user->email_verification_code = $code;
        $user->email_verification_expires_at = now()->addMinutes(10);
        $user->save();

        Mail::to($user->email)->send(new \App\Mail\EmailVerificationCodeMail($code));

        return response()->json([
            'message' => 'Se ha reenviado un nuevo código de verificación a tu correo.'
        ]);
    }
}
