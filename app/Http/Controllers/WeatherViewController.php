<?php

namespace App\Http\Controllers;

use App\Models\SystemLocation;
use App\Models\WeatherData;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WeatherViewController extends Controller
{
    public function show(Request $request, SystemLocation $location)
    {
        $weatherData = WeatherData::where('location_id', $location->id)
            ->where('forecast_time', '>=', Carbon::now()->subDays(7))
            ->orderBy('forecast_time')
            ->get();

        return view('weather.show', [
            'location' => $location,
            'weatherData' => $weatherData,
        ]);
    }
}
