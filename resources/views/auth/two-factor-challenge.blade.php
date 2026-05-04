@extends('auth.layout')

@section('title', __('Two-factor challenge'))
@section('hero_title', __('Verify your sign-in'))
@section('hero_intro', __('Your password was accepted. Complete the sign-in with a one-time code or a recovery code.'))

@section('hero_points')
    <li>{{ __('Use the current code from your authenticator app when available.') }}</li>
    <li>{{ __('If you do not have your device, use one of your saved recovery codes.') }}</li>
    <li>{{ __('Recovery codes are single use. Generate a new set after you use one.') }}</li>
@endsection

@section('content')
    <h2>{{ __('Two-factor challenge') }}</h2>
    <p class="intro">{{ __('Finish signing in by entering either the six-digit authentication code or one of your recovery codes.') }}</p>

    @if ($errors->any())
        <div class="error-list">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="section-grid">
        <section class="panel">
            <h3>{{ __('Authenticator app code') }}</h3>
            <p>{{ __('Open your authenticator app and enter the current code for this account.') }}</p>

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <label for="code">{{ __('Authentication code') }}</label>
                <input
                    id="code"
                    type="text"
                    name="code"
                    inputmode="numeric"
                    autocomplete="one-time-code"
                    required
                >

                <button type="submit">{{ __('Verify and continue') }}</button>
            </form>
        </section>

        <section class="panel">
            <h3>{{ __('Recovery code') }}</h3>
            <p>{{ __('If you cannot access your authenticator app, enter one of your saved recovery codes instead.') }}</p>

            <form method="POST" action="{{ route('two-factor.login.store') }}">
                @csrf

                <label for="recovery_code">{{ __('Recovery code') }}</label>
                <input
                    id="recovery_code"
                    type="text"
                    name="recovery_code"
                    autocomplete="one-time-code"
                    required
                >

                <button class="secondary" type="submit">{{ __('Use recovery code') }}</button>
            </form>
        </section>
    </div>

    <p class="fine-print">{{ __('If you are still locked out after using your saved codes, reset your password first and then contact an administrator if the problem continues.') }}</p>
@endsection
