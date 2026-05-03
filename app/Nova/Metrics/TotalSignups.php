<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSignup;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;

class TotalSignups extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, WorkoutSignup::class);
    }

    public function ranges()
    {
        return [
            30 => '30 Days',
            60 => '60 Days',
            90 => '90 Days',
            'TODAY' => 'Today',
            'MTD' => 'Month To Date',
            'QTD' => 'Quarter To Date',
            'YTD' => 'Year To Date',
        ];
    }
}
