<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Carbon;

class TimeWindowFilter extends Filter
{
    public $component = 'select-filter';

    public function name()
    {
        return __('Time Window');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        // If 'all' is selected, return the query unmodified
        if ($value === 'all') {
            return $query;
        }

        $now = Carbon::now();

        if ($value === 'next_hour') {
            return $query
                ->whereRaw('TIMESTAMP(session_date, start_time) >= ?', [$now])
                ->whereRaw('TIMESTAMP(session_date, start_time) <= ?', [$now->copy()->addHour()]);
        }

        $hours = (int)$value;

        return $query->where(function($query) use ($now, $hours) {
            // Get session datetime by combining date and time
            $query->whereRaw('TIMESTAMP(session_date, start_time) >= ?', [$now->copy()->subHours($hours)])
                ->whereRaw('TIMESTAMP(session_date, start_time) <= ?', [$now->copy()->addHours($hours)]);
        });
    }

    public function options(NovaRequest $request)
    {
        return [
            __('Starts In Next Hour') => 'next_hour',
            __('± 1 Hour') => 1,
            __('± 3 Hours') => 3,
            __('± 6 Hours') => 6,
            __('± 12 Hours') => 12,
            __('All Sessions') => 'all'
        ];
    }

    public function default()
    {
        return 'all';
    }
}
