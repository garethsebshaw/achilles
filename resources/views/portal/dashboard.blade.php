<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Portal Dashboard') }}</title>
    @vite(['resources/css/portal.css', 'resources/js/portal/main.js'])
</head>
<body>
    <script>
        window.__PORTAL_DASHBOARD_CONFIG__ = {{ \Illuminate\Support\Js::from($dashboardConfig) }};
    </script>
    <div id="portal-dashboard"></div>
</body>
</html>
