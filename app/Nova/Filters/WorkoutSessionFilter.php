<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkoutSessionFilter extends Filter
{
    public $component = 'select-filter';

    public function name()
    {
        return __('Session');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        \Log::info('Applying WorkoutSessionFilter', ['value' => $value]);
        return $query->where('workout_session_id', $value);
    }

    public function options(NovaRequest $request)
    {
        // First get the signup counts for all sessions
        $signupCounts = DB::table('workout_signups')
            ->select('workout_session_id', DB::raw('COUNT(DISTINCT user_id) as signup_count'))
            ->groupBy('workout_session_id')
            ->pluck('signup_count', 'workout_session_id')
            ->toArray();

        return \App\Models\WorkoutSession::with(['workout', 'location'])
            ->orderBy('location_id')
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->get()
            ->mapWithKeys(function ($session) use ($signupCounts) {
                if (!$session->workout || !$session->location) {
                    return [__('Unknown Session') => $session->id];
                }

                $activityLocation = $session->location->name ?? __('Unknown Location');
                $sportName = $session->workout->activityType->name ?? __('Unknown Sport');
                $formattedDate = optional($session->session_date)->format('Y-m-d');
                $startTime = optional($session->start_time)->format('H:i');
                $endTime = optional($session->end_time)->format('H:i');

                // Get the signup count for this session (default to 0 if none found)
                $signupCount = $signupCounts[$session->id] ?? 0;

                $formattedLabel = sprintf(
                    __('%s - %s %s-%s - %s (%d signups)'),
                    $activityLocation,
                    $formattedDate,
                    $startTime,
                    $endTime,
                    $sportName,
                    $signupCount
                );

                return [$formattedLabel => $session->id];
            })
            ->toArray();
    }
}
