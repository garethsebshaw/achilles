<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSignup;
use App\Nova\Metrics\Concerns\InterpretsSessionWindows;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class SignupWindowValue extends Value
{
    use InterpretsSessionWindows;

    public $width = '1/2';

    public function calculate(NovaRequest $request)
    {
        $range = $request->range ?? 'THIS_WEEK';

        $query = WorkoutSignup::query()
            ->join('workout_sessions', 'workout_signups.workout_session_id', '=', 'workout_sessions.id');

        $this->applySessionWindow($query, $range, 'workout_sessions.session_date');

        return $this->result($query->distinct('workout_signups.id')->count('workout_signups.id'));
    }

    public function ranges(): array
    {
        return $this->sessionWindowRanges();
    }

    public function name()
    {
        return __('Signups');
    }

    public function cacheFor()
    {
        return now()->addMinutes(5);
    }
}
