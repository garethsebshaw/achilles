<?php

namespace App\Nova\Metrics;

use App\Models\WeatherData;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;

class LocationsWithWeatherData extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->result(
            WeatherData::distinct('location_id')->count('location_id')
        );
    }

    public function name()
    {
        return 'Locations With Weather Data';
    }
}
