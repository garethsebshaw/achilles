<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSession;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Http\Requests\NovaRequest;

class WorkoutAttendanceTrend extends Trend
{
    public function calculate(NovaRequest $request)
    {
        return $this->countByDays($request, WorkoutSession::class)
            ->showLatestValue();
    }

    public function name()
    {
        return __('Workout Attendance');
    }

    public function ranges()
    {
        return [
            7 => __('7 Days'),
            30 => __('30 Days'),
            60 => __('60 Days'),
            90 => __('90 Days'),
        ];
    }
}
