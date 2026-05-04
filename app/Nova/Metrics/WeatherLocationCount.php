<?php

namespace App\Nova\Metrics;

use App\Models\SystemLocation;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;

class WeatherLocationCount extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, SystemLocation::where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude'));
    }

    public function name()
    {
        return __('Active Weather Locations');
    }
}
