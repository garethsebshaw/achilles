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
        $weatherData = WeatherData::query()
            ->where('location_id', $location->id)
            ->where('forecast_time', '>=', Carbon::now()->subHours(6))
            ->orderBy('forecast_time')
            ->limit(48)
            ->get();

        $dailyWeatherData = WeatherDailyData::query()
            ->where('location_id', $location->id)
            ->where('date', '>=', Carbon::now()->toDateString())
            ->orderBy('date')
            ->limit(7)
            ->get();

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
