<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', __('Security'))</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #edf2f7;
            --panel: #ffffff;
            --panel-edge: #d7dee8;
            --ink: #14213d;
            --muted: #5b6477;
            --accent: #0f4c81;
            --accent-soft: #dceeff;
            --danger: #8c2f39;
            --danger-soft: #fde8ea;
            --success: #155e4b;
            --success-soft: #e3f7f0;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: "Helvetica Neue", Arial, sans-serif;
            background:
                radial-gradient(circle at top right, rgba(15, 76, 129, 0.12), transparent 24rem),
                linear-gradient(180deg, #f8fbfd 0%, var(--bg) 100%);
            color: var(--ink);
        }

        a {
            color: var(--accent);
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 32px 20px;
        }

        .shell {
            width: 100%;
            max-width: 1100px;
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 24px;
        }

        .hero,
        .card {
            background: var(--panel);
            border: 1px solid var(--panel-edge);
            border-radius: 20px;
            box-shadow: 0 18px 48px rgba(20, 33, 61, 0.08);
        }

        .hero {
            padding: 28px;
            background:
                linear-gradient(135deg, rgba(15, 76, 129, 0.08), rgba(255,255,255,0)),
                var(--panel);
        }

        .hero h1 {
            margin: 0 0 10px;
            font-size: 30px;
            line-height: 1.15;
        }

        .hero p {
            margin: 0;
            color: var(--muted);
            line-height: 1.6;
        }

        .hero ul {
            margin: 18px 0 0;
            padding-left: 18px;
            color: var(--muted);
            line-height: 1.6;
        }

        .card {
            padding: 28px;
        }

        h2 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .intro {
            margin: 0 0 24px;
            color: var(--muted);
            line-height: 1.6;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            color: #344055;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 13px 14px;
            border: 1px solid #c6d1df;
            border-radius: 12px;
            margin-bottom: 16px;
            font-size: 15px;
            color: var(--ink);
            background: #fbfdff;
        }

        input:focus {
            outline: 2px solid rgba(15, 76, 129, 0.2);
            border-color: var(--accent);
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            color: var(--muted);
        }

        .checkbox label {
            margin: 0;
            font-weight: 500;
            text-transform: none;
            letter-spacing: 0;
        }

        .row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }

        .button,
        button {
            appearance: none;
            border: 0;
            border-radius: 12px;
            background: var(--ink);
            color: #ffffff;
            padding: 12px 18px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .button.secondary,
        button.secondary {
            background: #eef4fb;
            color: var(--accent);
        }

        .button.ghost,
        button.ghost {
            background: transparent;
            color: var(--accent);
            border: 1px solid #bfd2e8;
        }

        .status,
        .error-list {
            border-radius: 14px;
            padding: 14px 16px;
            margin-bottom: 20px;
            line-height: 1.55;
        }

        .status {
            background: var(--success-soft);
            border: 1px solid #b8ecd9;
            color: var(--success);
        }

        .error-list {
            background: var(--danger-soft);
            border: 1px solid #f4c5cc;
            color: var(--danger);
        }

        .muted {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.6;
        }

        .fine-print {
            margin-top: 20px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .section-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .panel {
            border: 1px solid #dbe3ee;
            border-radius: 16px;
            padding: 20px;
            background: #fcfdff;
        }

        .panel h3 {
            margin: 0 0 10px;
            font-size: 20px;
        }

        .panel p {
            margin: 0 0 16px;
            color: var(--muted);
            line-height: 1.6;
        }

        .panel code {
            display: block;
            padding: 12px 14px;
            border-radius: 12px;
            background: #132238;
            color: #f8fbff;
            font-size: 14px;
            overflow-wrap: anywhere;
            margin-bottom: 14px;
        }

        .recovery-codes {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 10px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .recovery-codes li {
            padding: 10px 12px;
            border: 1px solid #dbe3ee;
            border-radius: 12px;
            background: #ffffff;
            font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
            font-size: 13px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .actions-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .divider {
            margin: 24px 0;
            border: 0;
            border-top: 1px solid #e3eaf2;
        }

        @media (min-width: 980px) {
            .shell.auth-shell {
                grid-template-columns: minmax(0, 0.95fr) minmax(360px, 0.8fr);
                align-items: start;
            }
        }
    </style>
    @stack('head')
</head>
<body>
    <div class="page">
        <div class="shell @yield('shell_class', 'auth-shell')">
            <section class="hero">
                <h1>@yield('hero_title', __('Achilles Workouts Security'))</h1>
                <p>@yield('hero_intro', __('Manage sign-in, password recovery, and additional account protection from a single workflow.'))</p>
                @hasSection('hero_points')
                    <ul>
                        @yield('hero_points')
                    </ul>
                @endif
            </section>

            <main class="card">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
