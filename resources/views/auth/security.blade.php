@extends('auth.layout')

@section('title', __('Account security'))
@section('shell_class', '')
@section('hero_title', __('Protect your account'))
@section('hero_intro', __('Manage your password, two-factor authentication, and recovery codes from one place.'))

@section('hero_points')
    <li>{{ __('A strong password protects the first step of every sign-in.') }}</li>
    <li>{{ __('Two-factor authentication protects the account even if your password is exposed.') }}</li>
    <li>{{ __('Recovery codes should be stored offline or in your password manager.') }}</li>
@endsection

@section('content')
    <h2>{{ __('Account security') }}</h2>
    <p class="intro">{{ __('Review your current password and two-factor settings below.') }}</p>

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="error-list">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="section-grid">
        <section class="panel">
            <h3>{{ __('Password') }}</h3>
            <p>{{ __('Change your password without leaving the security page.') }}</p>

            <form method="POST" action="{{ route('account.security.password.update') }}">
                @csrf
                @method('PUT')

                <label for="current_password">{{ __('Current password') }}</label>
                <input id="current_password" type="password" name="current_password" required autocomplete="current-password">

                <label for="password">{{ __('New password') }}</label>
                <input id="password" type="password" name="password" required autocomplete="new-password">

                <label for="password_confirmation">{{ __('Confirm new password') }}</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">

                <button type="submit">{{ __('Update password') }}</button>
            </form>
        </section>

        <section class="panel">
            <h3>{{ __('Two-factor authentication') }}</h3>

            @if ($user->two_factor_secret && $twoFactorConfirmed)
                <div class="badge">{{ __('Enabled and confirmed') }}</div>
            @elseif ($user->two_factor_secret)
                <div class="badge">{{ __('Setup pending confirmation') }}</div>
            @else
                <div class="badge">{{ __('Not enabled') }}</div>
            @endif

            @if (! $user->two_factor_secret)
                <p>{{ __('Add a second verification step to your account before broader rollout reaches every chapter and role.') }}</p>

                <form method="POST" action="{{ route('account.security.two-factor.enable') }}">
                    @csrf
                    <button type="submit">{{ __('Enable two-factor authentication') }}</button>
                </form>
            @else
                <p>{{ __('Scan the QR code with your authenticator app, confirm the setup, and save your recovery codes somewhere safe.') }}</p>

                @if ($twoFactorQrCodeSvg)
                    <div style="display:inline-block; padding: 12px; border-radius: 16px; background: #ffffff; border: 1px solid #dbe3ee; margin-bottom: 16px;">
                        {!! $twoFactorQrCodeSvg !!}
                    </div>
                @endif

                @if ($twoFactorSecretKey)
                    <label>{{ __('Manual setup key') }}</label>
                    <code>{{ $twoFactorSecretKey }}</code>
                @endif

                @if (! $twoFactorConfirmed)
                    <form method="POST" action="{{ route('account.security.two-factor.confirm') }}">
                        @csrf

                        <label for="code">{{ __('Authentication code') }}</label>
                        <input id="code" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" required>

                        <button type="submit">{{ __('Confirm two-factor authentication') }}</button>
                    </form>

                    <hr class="divider">
                @endif

                @if (! empty($recoveryCodes))
                    <label>{{ __('Recovery codes') }}</label>
                    <ul class="recovery-codes">
                        @foreach ($recoveryCodes as $recoveryCode)
                            <li>{{ $recoveryCode }}</li>
                        @endforeach
                    </ul>
                @endif

                <div class="actions-stack" style="margin-top: 18px;">
                    <form method="POST" action="{{ route('account.security.two-factor.recovery-codes') }}">
                        @csrf
                        <button class="secondary" type="submit">{{ __('Generate new recovery codes') }}</button>
                    </form>

                    <form method="POST" action="{{ route('account.security.two-factor.disable') }}">
                        @csrf
                        @method('DELETE')
                        <button class="ghost" type="submit">{{ __('Disable two-factor authentication') }}</button>
                    </form>
                </div>
            @endif
        </section>
    </div>
@endsection
