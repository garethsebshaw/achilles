<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-assets-path="{{ asset('/') }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Portal Dashboard') }}</title>
    @vite(['resources/css/portal.css', 'resources/js/portal/main.js'])
</head>
<body class="layout-wrapper layout-content-navbar portal-vuexy">
    <script>
        window.__PORTAL_DASHBOARD_CONFIG__ = {{ \Illuminate\Support\Js::from($dashboardConfig) }};
    </script>

    <a class="portal-skip-link" href="#portal-main">{{ __('Skip to dashboard content') }}</a>

    <div class="layout-container">
        <aside id="layout-menu" class="layout-menu menu-vertical portal-sidebar" aria-label="{{ __('Primary navigation') }}">
            <div class="portal-sidebar__brand">
                <a href="{{ route('portal.dashboard') }}" class="portal-sidebar__brand-link">
                    <span class="portal-sidebar__brand-mark">A</span>
                    <span>
                        <strong>{{ $portalShell['app_name'] }}</strong>
                        <small>{{ $portalShell['workspace_label'] }}</small>
                    </span>
                </a>

                <button type="button" class="portal-icon-button d-lg-none" data-portal-menu-toggle aria-label="{{ __('Toggle navigation') }}">
                    ☰
                </button>
            </div>

            <nav class="portal-sidebar__nav">
                <ul class="portal-sidebar__menu">
                    @foreach ($portalShell['menu'] as $item)
                        <li>
                            <a
                                href="{{ $item['href'] }}"
                                class="portal-sidebar__link {{ ($item['active'] ?? false) ? 'is-active' : '' }}"
                            >
                                <span class="portal-sidebar__icon">{{ $item['icon'] }}</span>
                                <span>{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="portal-sidebar__footer">
                @foreach ($portalShell['secondary_links'] as $item)
                    <a href="{{ $item['href'] }}" class="portal-sidebar__secondary-link">{{ $item['label'] }}</a>
                @endforeach
            </div>
        </aside>

        <div class="layout-page">
            <header class="layout-navbar portal-topbar">
                <div class="portal-topbar__inner">
                    <div class="portal-topbar__left">
                        <button type="button" class="portal-icon-button d-lg-none" data-portal-menu-toggle aria-label="{{ __('Toggle navigation') }}">
                            ☰
                        </button>

                        <label class="portal-search" for="portal-search">
                            <span class="portal-search__icon">⌕</span>
                            <input
                                id="portal-search"
                                type="search"
                                placeholder="{{ $portalShell['search_placeholder'] }}"
                                aria-label="{{ $portalShell['search_placeholder'] }}"
                                disabled
                            >
                        </label>
                    </div>

                    <div class="portal-topbar__right">
                        <a href="{{ route('account.security') }}" class="portal-topbar__link">{{ __('Security') }}</a>
                        <a href="{{ url('/dashboards/main') }}" class="portal-topbar__link">{{ __('Admin') }}</a>

                        <div class="portal-user-chip" aria-label="{{ __('Signed in user') }}">
                            <span class="portal-user-chip__avatar">{{ $portalShell['user']['initials'] }}</span>
                            <span class="portal-user-chip__meta">
                                <strong>{{ $portalShell['user']['name'] }}</strong>
                                <small>{{ $portalShell['user']['email'] }}</small>
                            </span>
                        </div>

                        <form method="POST" action="{{ url('/logout') }}">
                            @csrf
                            <button type="submit" class="portal-topbar__button">{{ __('Log Out') }}</button>
                        </form>
                    </div>
                </div>
            </header>

            <main id="portal-main" class="content-wrapper portal-content">
                <div id="portal-dashboard"></div>
            </main>
        </div>
    </div>
</body>
</html>
