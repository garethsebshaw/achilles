@extends('auth.layout')

@section('title', __('Choose a new password'))
@section('hero_title', __('Create a new password'))
@section('hero_intro', __('Finish the reset flow by choosing a strong new password for your account.'))

@section('hero_points')
    <li>{{ __('Choose a unique password that you do not reuse elsewhere.') }}</li>
    <li>{{ __('Use at least one password manager-friendly random passphrase or a strong generated password.') }}</li>
    <li>{{ __('You can sign in immediately after the reset is complete.') }}</li>
@endsection

@section('content')
    <h2>{{ __('Reset password') }}</h2>
    <p class="intro">{{ __('Enter your email address and choose your new password below.') }}</p>

    @if ($errors->any())
        <div class="error-list">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <label for="email">{{ __('Email') }}</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $email) }}"
            required
            autocomplete="email"
        >

        <label for="password">{{ __('New password') }}</label>
        <input
            id="password"
            type="password"
            name="password"
            required
            autocomplete="new-password"
        >

        <label for="password_confirmation">{{ __('Confirm new password') }}</label>
        <input
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            required
            autocomplete="new-password"
        >

        <div class="row">
            <a href="{{ route('login') }}">{{ __('Back to sign in') }}</a>
            <button type="submit">{{ __('Reset password') }}</button>
        </div>
    </form>
@endsection
