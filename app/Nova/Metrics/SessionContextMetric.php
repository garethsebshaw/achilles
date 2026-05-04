<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSession;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class SessionContextMetric extends Value
{
    public function calculate(NovaRequest $request)
    {
        $sessionId = $request->get('workout_session_id');

        if (! $sessionId) {
            return $this->result(0);
        }

        $session = WorkoutSession::query()
            ->with(['workout.activityType', 'location'])
            ->find($sessionId);

        if (! $session) {
            return $this->result(0);
        }

        $summary = __(':sport at :location on :date from :start to :end', [
            'sport' => $session->workout->activityType->name ?? __('Unknown Sport'),
            'location' => $session->location->name ?? __('Unknown Location'),
            'date' => optional($session->session_date)->format('D, M j, Y') ?? __('Unknown Date'),
            'start' => optional($session->start_time)->format('g:ia') ?? __('TBD'),
            'end' => optional($session->end_time)->format('g:ia') ?? __('TBD'),
        ]);

        return $this->result($session->signups()->count());
    }

    public function name()
    {
        return __('Active Check-in Session');
    }
}
