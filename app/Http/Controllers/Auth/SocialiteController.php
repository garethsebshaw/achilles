<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $socialiteUser = Socialite::driver($provider)->user();

            $user = User::updateOrCreate([
                'provider' => $provider,
                'provider_id' => $socialiteUser->getId(),
            ], [
                'name' => $socialiteUser->getName(),
                'email' => $socialiteUser->getEmail(),
                'provider_token' => $socialiteUser->token,
                'provider_refresh_token' => $socialiteUser->refreshToken,
            ]);

            auth()->login($user);

            return redirect()->intended('/nova');

        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Something went wrong or You have rejected the app!');
        }
    }
}
