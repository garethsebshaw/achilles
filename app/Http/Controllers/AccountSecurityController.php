<?php

namespace App\Http\Controllers;

use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\DisableTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use Laravel\Fortify\Actions\GenerateNewRecoveryCodes;

class AccountSecurityController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user()->fresh();

        return view('auth.security', [
            'user' => $user,
            'recoveryCodes' => $user->two_factor_recovery_codes ? $user->recoveryCodes() : [],
            'twoFactorQrCodeSvg' => $user->two_factor_secret ? $user->twoFactorQrCodeSvg() : null,
            'twoFactorSecretKey' => $user->two_factor_secret
                ? decrypt($user->two_factor_secret)
                : null,
            'twoFactorConfirmed' => $user->hasEnabledTwoFactorAuthentication(),
        ]);
    }

    public function updatePassword(Request $request, UpdateUserPassword $updateUserPassword): RedirectResponse
    {
        $updateUserPassword->update($request->user(), $request->all());

        return back()->with('status', __('Your password has been updated.'));
    }

    public function enableTwoFactor(Request $request, EnableTwoFactorAuthentication $enableTwoFactorAuthentication): RedirectResponse
    {
        $enableTwoFactorAuthentication($request->user(), $request->boolean('force'));

        return back()->with('status', __('Two-factor authentication setup is ready. Scan the QR code and confirm with a one-time code.'));
    }

    public function confirmTwoFactor(Request $request, ConfirmTwoFactorAuthentication $confirmTwoFactorAuthentication): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $confirmTwoFactorAuthentication($request->user(), (string) $request->input('code'));

        return back()->with('status', __('Two-factor authentication is now enabled for your account.'));
    }

    public function regenerateRecoveryCodes(Request $request, GenerateNewRecoveryCodes $generateNewRecoveryCodes): RedirectResponse
    {
        $generateNewRecoveryCodes($request->user());

        return back()->with('status', __('A new set of recovery codes has been generated.'));
    }

    public function disableTwoFactor(Request $request, DisableTwoFactorAuthentication $disableTwoFactorAuthentication): RedirectResponse
    {
        $disableTwoFactorAuthentication($request->user());

        return back()->with('status', __('Two-factor authentication has been disabled.'));
    }
}
