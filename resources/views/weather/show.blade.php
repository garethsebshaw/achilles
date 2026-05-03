<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weather Data for {{ $location->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/react@17/umd/react.production.min.js"></script>
    <script src="https://unpkg.com/react-dom@17/umd/react-dom.production.min.js"></script>
    <script src="https://unpkg.com/recharts/umd/Recharts.min.js"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Weather Data for {{ $location->name }}</h1>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <div id="weather-dashboard" data-weather="{{ json_encode($weatherData) }}" data-location="{{ json_encode($location) }}"></div>
    </div>
</div>

</body>
</html>
