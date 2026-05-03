<?php

namespace App\Nova\Metrics;

use App\Models\User;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\TrendResult;

class GuideGrowth extends Trend
{
    public $width = '1/2';

    /**
     * Calculate the value of the metric.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @return \Laravel\Nova\Metrics\TrendResult
     */
    public function calculate(NovaRequest $request): TrendResult
    {
        return $this->countByDays(
            $request,
            User::where('is_guide', true),
            'created_at'
        )
            ->prefix('');
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
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'guides-growth';
    }

    /**
     * The displayable name of the metric
     *
     * @return string
     */
    public function name()
    {
        return 'Guide Growth';
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
