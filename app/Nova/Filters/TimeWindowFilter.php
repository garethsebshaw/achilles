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
        return 'Time Window';
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        // If 'all' is selected, return the query unmodified
        if ($value === 'all') {
            return $query;
        }

        $hours = (int)$value;
        $now = Carbon::now();

        return $query->where(function($query) use ($now, $hours) {
            // Get session datetime by combining date and time
            $query->whereRaw('TIMESTAMP(session_date, start_time) >= ?', [$now->copy()->subHours($hours)])
                ->whereRaw('TIMESTAMP(session_date, start_time) <= ?', [$now->copy()->addHours($hours)]);
        });
    }

    public function options(NovaRequest $request)
    {
        return [
            '± 1 Hour' => 1,
            '± 3 Hours' => 3,
            '± 6 Hours' => 6,
            '± 12 Hours' => 12,
            'All Sessions' => 'all'
        ];
    }

    public function default()
    {
        return 'all';
    }
}
