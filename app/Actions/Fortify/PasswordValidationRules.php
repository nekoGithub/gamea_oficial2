<?php

namespace App\Actions\Fortify;

use Illuminate\Validation\Rules\Password;

trait PasswordValidationRules
{
    /**
     * Get the validation rules used to validate passwords.
     *
     * @return array<int, \Illuminate\Contracts\Validation\Rule|array<mixed>|string>
     */
    protected function passwordRules(): array
    {
        return [
            'required',
            'string',
            'confirmed',
            Password::min(8), // mínimo 8 caracteres
            'regex:/[a-z]/',      // al menos una letra minúscula
            'regex:/[A-Z]/',      // al menos una letra mayúscula
            'regex:/[0-9]/',      // al menos un número
            'regex:/[@$!%*?&]/'   // al menos un caracter especial
        ];
    }
}
