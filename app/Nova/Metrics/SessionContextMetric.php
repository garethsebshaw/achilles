<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSession;
use App\Support\Attendance\CheckInSessionContext;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class SessionContextMetric extends Value
{
    public function calculate(NovaRequest $request)
    {
        $sessionId = $this->meta['workout_session_id']
            ?? $request->get('workout_session_id')
            ?? app(CheckInSessionContext::class)->currentSessionId();

        if (! $sessionId) {
            return $this->result(__('No active session'));
        }

        $session = WorkoutSession::query()
            ->with(['workout.activityType', 'location'])
            ->find($sessionId);

        if (! $session) {
            return $this->result(__('No active session'));
        }

        return $this->result($session->signups()->count())->suffix(
            __(':sport at :location · ends :time', [
                'sport' => $session->workout->activityType->name ?? __('Unknown Sport'),
                'location' => $session->location->name ?? __('Unknown Location'),
                'time' => optional($session->end_time)->format('H:i') ?? __('Unknown'),
            ])
        );
    }

    public function name()
    {
        return __('Active Check-in Session');
    }
}
