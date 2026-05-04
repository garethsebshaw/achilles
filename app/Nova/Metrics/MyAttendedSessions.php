<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class MyAttendedSessions extends Value
{
    use ResolvesDashboardScope;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $user = $this->dashboardUser($request);

        if (! $user) {
            return $this->result(0);
        }

        $count = $this->personalSignupQuery($user)
            ->where(function ($query) {
                $query->whereIn('system_statuses.code', $this->attendedSignupCodes())
                    ->orWhereNotNull('workout_signups.checked_out_at');
            })
            ->distinct('workout_signups.workout_session_id')
            ->count('workout_signups.workout_session_id');

        return $this->result($count)->help(__('Sessions you completed successfully.'));
    }

    public function name()
    {
        return __('My Attended Sessions');
    }
}
