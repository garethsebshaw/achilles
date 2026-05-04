<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Carbon\Carbon;

class SessionTimeRangeFilter extends Filter
{
    public function name()
    {
        return __('Session Time Range');
    }

    /**
     * Apply the filter to the query.
     */
    public function apply(Request $request, $query, $value)
    {
        $now = Carbon::now();
        $pastDate = $now->copy()->subHours($value);

        return $query->whereBetween('session_date', [$pastDate, $now]);
    }

    /**
     * Provide the available options for filtering.
     */
    public function options(Request $request)
    {
        return [
            __('Last 3 Hours') => 3,
            __('Last 6 Hours') => 6,
            __('Last 12 Hours') => 12,
            __('Last 24 Hours') => 24,
        ];
    }
}
