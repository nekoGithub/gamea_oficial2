<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\CreateNewUser;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    public function store(Request $request, CreateNewUser $creator)
    {
        $user = $creator->create($request->all());

        return redirect()->route('verify.code.form', [
            'email' => encrypt($user->email)
        ])->with('status', 'Te enviamos un código de verificación a tu correo.');
    }
}
