<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use Carbon\Carbon;

class WorkoutSessionFilter extends Filter
{
    public $component = 'select-filter';

    public function name()
    {
        return __('Session');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('workout_session_id', $value);
    }

    public function options(NovaRequest $request)
    {
        return \App\Models\WorkoutSession::query()
            ->with(['workout.activityType', 'location'])
            ->withCount('signups')
            ->has('signups')
            ->whereBetween('session_date', [
                Carbon::today()->subDays(14)->toDateString(),
                Carbon::today()->addDays(60)->toDateString(),
            ])
            ->orderBy('session_date')
            ->orderBy('start_time')
            ->limit(250)
            ->get()
            ->mapWithKeys(function ($session) {
                if (!$session->workout || !$session->location) {
                    return [__('Unknown Session') => $session->id];
                }

                $activityLocation = $session->location->name ?? __('Unknown Location');
                $sportName = optional(optional($session->workout)->activityType)->name ?? __('Unknown Sport');
                $formattedDate = optional($session->session_date)->format('Y-m-d');
                $startTime = optional($session->start_time)->format('H:i');
                $endTime = optional($session->end_time)->format('H:i');

                $formattedLabel = sprintf(
                    __('%s - %s %s-%s - %s (%d signups)'),
                    $activityLocation,
                    $formattedDate,
                    $startTime,
                    $endTime,
                    $sportName,
                    $session->signups_count
                );

                return [$formattedLabel => $session->id];
            })
            ->toArray();
    }
}
