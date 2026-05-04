<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\User;

class UserGrowth extends Trend
{
    public function calculate(NovaRequest $request)
    {
        if (is_numeric($request->range) && $request->range > 1000) {
            // For year filters, show monthly data
            return $this->countByMonths(
                $request,
                User::class,
                'created_at'
            )->showLatestValue()
                ->whereYear('created_at', $request->range);
        }

        // For all other ranges, show daily data
        return $this->countByDays(
            $request,
            User::class,
            'created_at'
        )->showLatestValue();
    }

    public function ranges()
    {
        $currentYear = now()->year;

        return [
            7 => __('7 Days'),
            30 => __('30 Days'),
            60 => __('60 Days'),
            90 => __('90 Days'),
        ];
    }

    public function uriKey()
    {
        return 'user-growth';
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
}
