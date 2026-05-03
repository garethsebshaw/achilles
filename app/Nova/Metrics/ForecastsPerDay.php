<?php

namespace App\Nova\Metrics;

use App\Models\WeatherDailyData;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Http\Requests\NovaRequest;

class ForecastsPerDay extends Trend
{
    public function calculate(NovaRequest $request)
    {
        return $this->countByDays($request, WeatherDailyData::query(), 'date');
    }

    public function name()
    {
        return 'Forecasts Per Day';
    }
    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            7 => '7 Days',
            30 => '30 Days',
            60 => '60 Days',
            90 => '90 Days',
            180 => '6 Months',
            365 => '1 Year',
            720 => '2 Years',
        ];
    }

    /**
     * Determine the amount of time the results should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        return now()->addMinutes(5);
    }

    /**
     * Set the default range
     *
     * @return string|int
     */
    public function defaultRange(string $key): mixed
    {
        return 30; // or 'MTD' or whatever range key you want as default
    }
}
