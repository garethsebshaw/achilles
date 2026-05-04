<?php

namespace App\Nova\Filters;

use Illuminate\Support\Carbon;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class SessionSchedulePresetFilter extends Filter
{
    public $component = 'select-filter';

    public function name()
    {
        return __('Schedule Window');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        $today = Carbon::today();

        return match ($value) {
            'today' => $query->whereDate('session_date', $today),
            'this_week' => $query->whereBetween('session_date', [
                $today->copy()->startOfWeek(),
                $today->copy()->endOfWeek(),
            ]),
            'next_week' => $query->whereBetween('session_date', [
                $today->copy()->addWeek()->startOfWeek(),
                $today->copy()->addWeek()->endOfWeek(),
            ]),
            'week_after_next' => $query->whereBetween('session_date', [
                $today->copy()->addWeeks(2)->startOfWeek(),
                $today->copy()->addWeeks(2)->endOfWeek(),
            ]),
            'this_month' => $query->whereBetween('session_date', [
                $today->copy()->startOfMonth(),
                $today->copy()->endOfMonth(),
            ]),
            'next_month' => $query->whereBetween('session_date', [
                $today->copy()->addMonthNoOverflow()->startOfMonth(),
                $today->copy()->addMonthNoOverflow()->endOfMonth(),
            ]),
            'next_7_days' => $query->whereBetween('session_date', [
                $today,
                $today->copy()->addDays(7),
            ]),
            'next_14_days' => $query->whereBetween('session_date', [
                $today,
                $today->copy()->addDays(14),
            ]),
            'next_30_days' => $query->whereBetween('session_date', [
                $today,
                $today->copy()->addDays(30),
            ]),
            'past_7_days' => $query->whereBetween('session_date', [
                $today->copy()->subDays(7),
                $today,
            ]),
            'past_30_days' => $query->whereBetween('session_date', [
                $today->copy()->subDays(30),
                $today,
            ]),
            default => $query,
        };
    }

    public function options(NovaRequest $request)
    {
        return [
            __('Today') => 'today',
            __('This Week') => 'this_week',
            __('Next Week') => 'next_week',
            __('Week After Next') => 'week_after_next',
            __('This Month') => 'this_month',
            __('Next Month') => 'next_month',
            __('Rolling 7 Days') => 'next_7_days',
            __('Rolling 14 Days') => 'next_14_days',
            __('Rolling 30 Days') => 'next_30_days',
            __('Past 7 Days') => 'past_7_days',
            __('Past 30 Days') => 'past_30_days',
        ];
    }
}
