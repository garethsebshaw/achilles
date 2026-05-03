<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(17, 24, 39, 0.08);
            padding: 32px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        p {
            margin: 0 0 24px;
            color: #4b5563;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            font-size: 14px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            box-sizing: border-box;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            margin-bottom: 16px;
            font-size: 15px;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .checkbox label {
            margin: 0;
            font-weight: 400;
        }

        .actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .link {
            color: #2563eb;
            text-decoration: none;
            font-size: 14px;
        }

        .button {
            border: 0;
            border-radius: 8px;
            background: #111827;
            color: #ffffff;
            padding: 12px 18px;
            font-size: 15px;
            cursor: pointer;
        }

        .error-list,
        .status {
            border-radius: 8px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .error-list {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .status {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #065f46;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="card">
            <h1>Sign in</h1>
            <p>Use your account credentials to access Achilles Workouts.</p>

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

                <label for="email">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                >

                <label for="password">Password</label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                >

                <div class="checkbox">
                    <input id="remember" type="checkbox" name="remember">
                    <label for="remember">Remember me</label>
                </div>

                <div class="actions">
                    @if (Route::has('password.request'))
                        <a class="link" href="{{ route('password.request') }}">Forgot your password?</a>
                    @else
                        <span></span>
                    @endif

                    <button class="button" type="submit">Log in</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
