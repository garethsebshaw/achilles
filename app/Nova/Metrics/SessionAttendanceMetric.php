<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\DB;

class SessionAttendanceMetric extends Value
{
    public $refreshInterval = 5;
    public $component = 'session-attendance-metric';

    public function calculate(NovaRequest $request)
    {
        $sessionId = $this->meta['workout_session_id'] ?? $request->get('workout_session_id');

        if (!$sessionId) {
            return $this->result(0);
        }

        $counts = DB::table('workout_signups')
            ->join('users', 'workout_signups.user_id', '=', 'users.id')
            ->where('workout_signups.workout_session_id', $sessionId)
            ->select(
                DB::raw('COUNT(DISTINCT CASE WHEN users.is_athlete = 1 THEN workout_signups.user_id END) as total_athletes'),
                DB::raw('COUNT(DISTINCT CASE WHEN users.is_athlete = 1 AND workout_signups.checked_in_at IS NOT NULL THEN workout_signups.user_id END) as checked_in_athletes'),
                DB::raw('COUNT(DISTINCT CASE WHEN users.is_athlete = 1 AND workout_signups.checked_out_at IS NOT NULL THEN workout_signups.user_id END) as checked_out_athletes'),
                DB::raw('COUNT(DISTINCT CASE WHEN users.is_guide = 1 THEN workout_signups.user_id END) as total_guides'),
                DB::raw('COUNT(DISTINCT CASE WHEN users.is_guide = 1 AND workout_signups.checked_in_at IS NOT NULL THEN workout_signups.user_id END) as checked_in_guides'),
                DB::raw('COUNT(DISTINCT CASE WHEN users.is_guide = 1 AND workout_signups.checked_out_at IS NOT NULL THEN workout_signups.user_id END) as checked_out_guides')
            )
            ->first();

        return $this->result(0)->withMeta([
            'athleteStats' => [
                'total' => $counts->total_athletes ?? 0,
                'checkedIn' => $counts->checked_in_athletes ?? 0,
                'checkedOut' => $counts->checked_out_athletes ?? 0,
                'checkedInPercent' => ($counts->total_athletes ?? 0) > 0
                    ? round(($counts->checked_in_athletes / $counts->total_athletes) * 100)
                    : 0,
                'checkedOutPercent' => ($counts->checked_in_athletes ?? 0) > 0
                    ? round(($counts->checked_out_athletes / $counts->checked_in_athletes) * 100)
                    : 0,
            ],
            'guideStats' => [
                'total' => $counts->total_guides ?? 0,
                'checkedIn' => $counts->checked_in_guides ?? 0,
                'checkedOut' => $counts->checked_out_guides ?? 0,
                'checkedInPercent' => ($counts->total_guides ?? 0) > 0
                    ? round(($counts->checked_in_guides / $counts->total_guides) * 100)
                    : 0,
                'checkedOutPercent' => ($counts->checked_in_guides ?? 0) > 0
                    ? round(($counts->checked_out_guides / $counts->checked_in_guides) * 100)
                    : 0,
            ]
        ]);
    }

    public function name()
    {
        return __('Session Attendance');
    }

    public function component()
    {
        return 'session-attendance-metric';
    }
}
