<?php

namespace App\Http\Controllers;

use App\Models\SystemLocation;
use App\Models\WeatherData;
use App\Models\WeatherDailyData;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WeatherViewController extends Controller
{
    public function show(Request $request, SystemLocation $location)
    {
        $latestHourlyGeneration = WeatherData::query()
            ->where('location_id', $location->id)
            ->max('generated_at');

        $weatherData = WeatherData::query()
            ->where('location_id', $location->id)
            ->when($latestHourlyGeneration, fn ($query) => $query->where('generated_at', $latestHourlyGeneration))
            ->where('forecast_time', '>=', Carbon::now()->subHours(6))
            ->orderBy('forecast_time')
            ->limit(48)
            ->get()
            ->unique(fn (WeatherData $entry) => optional($entry->forecast_time)?->toIso8601String())
            ->values();

        $latestDailyGeneration = WeatherDailyData::query()
            ->where('location_id', $location->id)
            ->max('generated_at');

        $dailyWeatherData = WeatherDailyData::query()
            ->where('location_id', $location->id)
            ->when($latestDailyGeneration, fn ($query) => $query->where('generated_at', $latestDailyGeneration))
            ->where('date', '>=', Carbon::now()->toDateString())
            ->orderBy('date')
            ->limit(7)
            ->get()
            ->unique(fn (WeatherDailyData $entry) => optional($entry->date)?->toDateString())
            ->values();

        $currentWeather = $weatherData
            ->first(fn (WeatherData $entry) => $entry->forecast_time?->greaterThanOrEqualTo(Carbon::now()->subHour()))
            ?? $weatherData->first();

        return view('weather.show', [
            'location' => $location,
            'weatherData' => $weatherData,
            'dailyWeatherData' => $dailyWeatherData,
            'currentWeather' => $currentWeather,
        ]);
    }
}
