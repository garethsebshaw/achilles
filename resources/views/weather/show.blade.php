<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Weather Data for :location', ['location' => $location->name]) }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">{{ __('Weather Data for :location', ['location' => $location->name]) }}</h1>

    <div class="grid gap-6 lg:grid-cols-4">
        <div class="bg-white rounded-lg shadow-lg p-6 lg:col-span-1">
            <h2 class="text-lg font-semibold mb-4">{{ __('Current Conditions') }}</h2>

            @if ($currentWeather)
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="font-medium text-gray-500">{{ __('Forecast Time') }}</dt>
                        <dd>{{ $currentWeather->forecast_time?->setTimezone($location->timezone ?? config('app.timezone'))->format('D, M j g:ia') }}</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">{{ __('Temperature') }}</dt>
                        <dd>{{ number_format((float) $currentWeather->temperature_2m, 1) }}&deg;C</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">{{ __('Feels Like') }}</dt>
                        <dd>{{ number_format((float) $currentWeather->apparent_temperature, 1) }}&deg;C</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">{{ __('Humidity') }}</dt>
                        <dd>{{ (int) $currentWeather->relative_humidity_2m }}%</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">{{ __('Wind Speed') }}</dt>
                        <dd>{{ number_format((float) $currentWeather->wind_speed_10m, 1) }} m/s</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">{{ __('Precipitation') }}</dt>
                        <dd>{{ number_format((float) $currentWeather->precipitation, 1) }} mm</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">{{ __('Cloud Cover') }}</dt>
                        <dd>{{ (int) $currentWeather->cloud_cover }}%</dd>
                    </div>
                    <div>
                        <dt class="font-medium text-gray-500">{{ __('Last Generated') }}</dt>
                        <dd>{{ $currentWeather->generated_at?->setTimezone($location->timezone ?? config('app.timezone'))->format('D, M j g:ia') }}</dd>
                    </div>
                </dl>
            @else
                <p class="text-sm text-gray-500">{{ __('No live weather data is currently available for this location.') }}</p>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 lg:col-span-3">
            <h2 class="text-lg font-semibold mb-4">{{ __('Next 48 Hours') }}</h2>

            @if ($weatherData->isEmpty())
                <p class="text-sm text-gray-500">{{ __('No hourly weather records were found for this location.') }}</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="border-b text-left">
                                <th class="py-2 pr-4">{{ __('Time') }}</th>
                                <th class="py-2 pr-4">{{ __('Temp') }}</th>
                                <th class="py-2 pr-4">{{ __('Feels Like') }}</th>
                                <th class="py-2 pr-4">{{ __('Humidity') }}</th>
                                <th class="py-2 pr-4">{{ __('Wind') }}</th>
                                <th class="py-2 pr-4">{{ __('Precipitation') }}</th>
                                <th class="py-2 pr-4">{{ __('Clouds') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($weatherData as $entry)
                                <tr class="border-b last:border-b-0">
                                    <td class="py-2 pr-4">{{ $entry->forecast_time?->setTimezone($location->timezone ?? config('app.timezone'))->format('D g:ia') }}</td>
                                    <td class="py-2 pr-4">{{ number_format((float) $entry->temperature_2m, 1) }}&deg;C</td>
                                    <td class="py-2 pr-4">{{ number_format((float) $entry->apparent_temperature, 1) }}&deg;C</td>
                                    <td class="py-2 pr-4">{{ (int) $entry->relative_humidity_2m }}%</td>
                                    <td class="py-2 pr-4">{{ number_format((float) $entry->wind_speed_10m, 1) }} m/s</td>
                                    <td class="py-2 pr-4">{{ number_format((float) $entry->precipitation, 1) }} mm</td>
                                    <td class="py-2 pr-4">{{ (int) $entry->cloud_cover }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('7 Day Outlook') }}</h2>

        @if ($dailyWeatherData->isEmpty())
            <p class="text-sm text-gray-500">{{ __('No daily weather records were found for this location.') }}</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2 pr-4">{{ __('Date') }}</th>
                            <th class="py-2 pr-4">{{ __('High') }}</th>
                            <th class="py-2 pr-4">{{ __('Low') }}</th>
                            <th class="py-2 pr-4">{{ __('Precipitation') }}</th>
                            <th class="py-2 pr-4">{{ __('Sunrise') }}</th>
                            <th class="py-2 pr-4">{{ __('Sunset') }}</th>
                            <th class="py-2 pr-4">{{ __('Wind Max') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dailyWeatherData as $entry)
                            <tr class="border-b last:border-b-0">
                                <td class="py-2 pr-4">{{ $entry->date?->setTimezone($location->timezone ?? config('app.timezone'))->format('D, M j') }}</td>
                                <td class="py-2 pr-4">{{ number_format((float) $entry->temperature_2m_max, 1) }}&deg;C</td>
                                <td class="py-2 pr-4">{{ number_format((float) $entry->temperature_2m_min, 1) }}&deg;C</td>
                                <td class="py-2 pr-4">{{ number_format((float) $entry->precipitation_sum, 1) }} mm</td>
                                <td class="py-2 pr-4">{{ $entry->sunrise?->setTimezone($location->timezone ?? config('app.timezone'))->format('g:ia') }}</td>
                                <td class="py-2 pr-4">{{ $entry->sunset?->setTimezone($location->timezone ?? config('app.timezone'))->format('g:ia') }}</td>
                                <td class="py-2 pr-4">{{ number_format((float) $entry->wind_speed_10m_max, 1) }} m/s</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

</body>
</html>
