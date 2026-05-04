@extends('auth.layout')

@section('title', __('Reset your password'))
@section('hero_title', __('Reset access without support tickets'))
@section('hero_intro', __('Request a password reset link and we will email you the next steps.'))

@section('hero_points')
    <li>{{ __('Reset links are time-limited for security.') }}</li>
    <li>{{ __('Use the same email address you sign in with.') }}</li>
    <li>{{ __('After you reset your password, you can continue through the usual two-factor flow if your account requires it.') }}</li>
@endsection

@section('content')
    <h2>{{ __('Forgot your password?') }}</h2>
    <p class="intro">{{ __('Enter your email address and we will send you a secure link to reset your password.') }}</p>

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

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label for="email">{{ __('Email') }}</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            autofocus
            autocomplete="email"
        >

        <div class="row">
            <a href="{{ route('login') }}">{{ __('Back to sign in') }}</a>
            <button type="submit">{{ __('Email password reset link') }}</button>
        </div>
    </form>
@endsection
