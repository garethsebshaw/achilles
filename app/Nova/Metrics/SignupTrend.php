<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSignup;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Http\Requests\NovaRequest;

class SignupTrend extends Trend
{
    public function calculate(NovaRequest $request)
    {
        return $this->countByDays($request, WorkoutSignup::class)
            ->showLatestValue();
    }

    public function ranges()
    {
        return [
            7 => __('7 Days'),
            30 => __('30 Days'),
            60 => __('60 Days'),
            90 => __('90 Days')
        ];
    }
}
