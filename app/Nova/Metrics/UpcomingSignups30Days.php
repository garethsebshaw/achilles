<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSignup;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class UpcomingSignups30Days extends Value
{
    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        return $this->result(
            WorkoutSignup::query()
                ->whereHas('workoutSession', function ($query) {
                    $query->whereBetween('session_date', [now()->toDateString(), now()->addDays(30)->toDateString()]);
                })
                ->count()
        )->help(__('Signups tied to sessions over the next 30 days.'));
    }

    public function name()
    {
        return __('Upcoming Signups (30 Days)');
    }
}
