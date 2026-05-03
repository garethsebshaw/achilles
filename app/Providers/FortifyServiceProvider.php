<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        config([
            'fortify.limiters.login' => 'login',
            'fortify.limiters.two-factor' => 'two-factor',
            'fortify.lowercase_usernames' => true,
        ]);

        Fortify::loginView(function () {
            return view('auth.login');
        });

        Fortify::authenticateUsing(function (Request $request) {
            $email = Str::lower(trim((string) $request->input('email')));
            $password = (string) $request->input('password');

            $user = User::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();

            if (! $user || ! $user->canAccessNova() || ! Hash::check($password, $user->password)) {
                return null;
            }

            if (Hash::needsRehash($user->password)) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }

            return $user;
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(
                Str::lower($request->input(Fortify::username())).'|'.$request->ip()
            );

            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
