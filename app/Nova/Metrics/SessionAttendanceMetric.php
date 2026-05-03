<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\DB;

class SessionAttendanceMetric extends Value
{
    public $refreshInterval = 5;
    public $component = 'session-attendance-metric';  // This should match the Vue component registration

    public function calculate(NovaRequest $request)
    {

        // Debug the incoming request
        \Log::info('SessionAttendanceMetric Calculate', [
            'request' => $request->all(),
            'meta' => $this->meta
        ]);

        // Get workout_session_id from request
        $sessionId = $request->get('workout_session_id');

        if (!$sessionId) {
            \Log::warning('No session ID provided');
            return $this->result(0);
        }

        // Get counts for athletes
        $athleteCounts = DB::table('workout_signups')
            ->join('users', 'workout_signups.user_id', '=', 'users.id')
            ->where('workout_signups.workout_session_id', $sessionId)
            ->where('users.is_athlete', true)
            ->select(
                DB::raw('COUNT(DISTINCT workout_signups.user_id) as total_athletes'),
                DB::raw('COUNT(DISTINCT CASE WHEN checked_in_at IS NOT NULL THEN workout_signups.user_id END) as checked_in_athletes'),
                DB::raw('COUNT(DISTINCT CASE WHEN checked_out_at IS NOT NULL THEN workout_signups.user_id END) as checked_out_athletes')
            )
            ->first();

        \Log::info('Athlete Counts', [
            'counts' => $athleteCounts,
            'sql' => DB::getQueryLog()
        ]);

        // Get counts for guides
        $guideCounts = DB::table('workout_signups')
            ->join('users', 'workout_signups.user_id', '=', 'users.id')
            ->where('workout_signups.workout_session_id', $sessionId)
            ->where('users.is_guide', true)
            ->select(
                DB::raw('COUNT(DISTINCT workout_signups.user_id) as total_guides'),
                DB::raw('COUNT(DISTINCT CASE WHEN checked_in_at IS NOT NULL THEN workout_signups.user_id END) as checked_in_guides'),
                DB::raw('COUNT(DISTINCT CASE WHEN checked_out_at IS NOT NULL THEN workout_signups.user_id END) as checked_out_guides')
            )
            ->first();


        \Log::info('Guides Counts', [
            'counts' => $guideCounts,
            'sql' => DB::getQueryLog()
        ]);

        return $this->result(0)->withMeta([
            'athleteStats' => [
                'total' => $athleteCounts->total_athletes ?? 0,
                'checkedIn' => $athleteCounts->checked_in_athletes ?? 0,
                'checkedOut' => $athleteCounts->checked_out_athletes ?? 0,
                'checkedInPercent' => $athleteCounts->total_athletes > 0
                    ? round(($athleteCounts->checked_in_athletes / $athleteCounts->total_athletes) * 100)
                    : 0,
                'checkedOutPercent' => $athleteCounts->checked_in_athletes > 0
                    ? round(($athleteCounts->checked_out_athletes / $athleteCounts->checked_in_athletes) * 100)
                    : 0,
            ],
            'guideStats' => [
                'total' => $guideCounts->total_guides ?? 0,
                'checkedIn' => $guideCounts->checked_in_guides ?? 0,
                'checkedOut' => $guideCounts->checked_out_guides ?? 0,
                'checkedInPercent' => $guideCounts->total_guides > 0
                    ? round(($guideCounts->checked_in_guides / $guideCounts->total_guides) * 100)
                    : 0,
                'checkedOutPercent' => $guideCounts->checked_in_guides > 0
                    ? round(($guideCounts->checked_out_guides / $guideCounts->checked_in_guides) * 100)
                    : 0,
            ]
        ]);
    }

    public function name()
    {
        return 'Session Attendance';
    }

    public function component()
    {
        return 'session-attendance-metric';
    }
}
