@extends('auth.layout')

@section('title', __('Sign in'))
@section('hero_title', __('Secure access for every chapter'))
@section('hero_intro', __('Sign in to manage sessions, signups, and operational tools across the Achilles platform.'))

@section('hero_points')
    <li>{{ __('Use your email address and password to access the platform.') }}</li>
    <li>{{ __('If your account uses two-factor authentication, you will be prompted for a one-time code after password verification.') }}</li>
    <li>{{ __('If you cannot remember your password, you can request a reset link from this page.') }}</li>
@endsection

@section('content')
    <h2>{{ __('Sign in') }}</h2>
    <p class="intro">{{ __('Use your account credentials to access Achilles Workouts.') }}</p>

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

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">{{ __('Email') }}</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            autofocus
            autocomplete="username"
        >

        <label for="password">{{ __('Password') }}</label>
        <input
            id="password"
            type="password"
            name="password"
            required
            autocomplete="current-password"
        >

        <div class="checkbox">
            <input id="remember" type="checkbox" name="remember">
            <label for="remember">{{ __('Remember me') }}</label>
        </div>

        <div class="row">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">{{ __('Forgot your password?') }}</a>
            @else
                <span></span>
            @endif

            <button type="submit">{{ __('Log in') }}</button>
        </div>
    </form>

    <p class="fine-print">{{ __('Need to complete a sign-in challenge with a recovery code instead? Continue with your password first and then choose the recovery-code option on the next screen.') }}</p>
@endsection
