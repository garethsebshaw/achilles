<?php

namespace App\Nova\Metrics;

use App\Models\WeatherData;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;
use Carbon\Carbon;

class LastWeatherUpdate extends Value
{
    public function calculate(NovaRequest $request)
    {
        $lastUpdate = WeatherData::orderByDesc('generated_at')->value('generated_at');

        if (!$lastUpdate) {
            return $this->result('No Data Available');
        }

        return $this->result(Carbon::parse($lastUpdate)->format('Y-m-d H:i:s'))->suffix('UTC');
    }

    public function name()
    {
        return 'Last Weather Update';
    }
}
