<?php

namespace App\Http\Controllers;

use App\Models\WeatherData;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WeatherDataController extends Controller
{
    public function getLocationData($locationId)
    {
        // Get the last 24 hours of weather data
        $weatherData = WeatherData::where('location_id', $locationId)
            ->where('forecast_time', '>=', Carbon::now()->subHours(24))
            ->orderBy('forecast_time')
            ->get()
            ->map(function ($data) {
                return [
                    'forecast_time' => $data->forecast_time->format('Y-m-d H:i'),
                    'temperature_2m' => $data->temperature_2m,
                    'apparent_temperature' => $data->apparent_temperature,
                    'precipitation' => $data->precipitation,
                    'snowfall' => $data->snowfall,
                    'wind_speed_10m' => $data->wind_speed_10m,
                    'wind_gusts_10m' => $data->wind_gusts_10m,
                    'relative_humidity_2m' => $data->relative_humidity_2m,
                    'cloud_cover' => $data->cloud_cover,
                ];
            });

        return response()->json($weatherData);
    }
}
