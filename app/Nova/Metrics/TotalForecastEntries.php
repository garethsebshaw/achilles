<?php

namespace App\Nova\Metrics;

use App\Models\WeatherData;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;

class TotalForecastEntries extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, WeatherData::query());
    }

    public function name()
    {
        return __('Total Weather Forecasts Stored');
    }
}
