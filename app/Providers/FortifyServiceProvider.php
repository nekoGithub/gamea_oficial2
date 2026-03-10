<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Controllers\RegisteredUserController;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);
        Fortify::redirectUserForTwoFactorAuthenticationUsing(RedirectIfTwoFactorAuthenticatable::class);

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())) . '|' . $request->ip());

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        app('router')->post('/register', [RegisteredUserController::class, 'store'])
            ->name('register');

        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            
            if ($user && Hash::check($request->password, $user->password)) {

            
                if (is_null($user->email_verified_at)) {
                    
                    session(['email_pending_verification' => $user->email]);

                    
                    throw ValidationException::withMessages([
                        'email' => 'Tu cuenta aún no está verificada. Ingresa tu código de verificación.',
                    ])->redirectTo(route('verify.code.form', [
                        'email' => encrypt($user->email),
                    ]));
                }


                return $user;
            }

            throw ValidationException::withMessages([
                Fortify::username() => __('Estas credenciales no coinciden con nuestros registros.'),
            ]);
        });
    }
}
