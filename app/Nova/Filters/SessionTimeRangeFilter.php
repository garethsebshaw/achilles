<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Carbon\Carbon;

class SessionTimeRangeFilter extends Filter
{
    public $name = 'Session Time Range';

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
            'Last 3 Hours' => 3,
            'Last 6 Hours' => 6,
            'Last 12 Hours' => 12,
            'Last 24 Hours' => 24,
        ];
    }
}
