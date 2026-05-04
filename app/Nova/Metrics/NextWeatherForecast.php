<?php

namespace App\Nova\Metrics;

use App\Models\WeatherData;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NextWeatherForecast extends Value
{
    public function calculate(NovaRequest $request)
    {
        $nextForecast = WeatherData::select(DB::raw('MAX(forecast_time) as latest_forecast_time'))->first()->latest_forecast_time;

        return $this->result(
            $nextForecast ? Carbon::parse($nextForecast)->format('Y-m-d H:i:s') : 'No Future Forecasts'
        )->suffix('UTC');
    }

    public function name()
    {
        return __('Next Available Forecast');
    }
}
