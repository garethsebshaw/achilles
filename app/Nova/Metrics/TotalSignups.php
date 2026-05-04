<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSignup;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;

class TotalSignups extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, WorkoutSignup::class);
    }

    public function ranges()
    {
        return [
            30 => __('30 Days'),
            60 => __('60 Days'),
            90 => __('90 Days'),
            'TODAY' => __('Today'),
            'MTD' => __('Month To Date'),
            'QTD' => __('Quarter To Date'),
            'YTD' => __('Year To Date'),
        ];
    }
}
