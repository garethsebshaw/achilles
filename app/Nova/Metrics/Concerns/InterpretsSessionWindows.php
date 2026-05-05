<?php

namespace App\Nova\Metrics\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait InterpretsSessionWindows
{
    protected function sessionWindowRanges(): array
    {
        return [
            'TODAY' => __('Today'),
            'THIS_WEEK' => __('This Week'),
            'NEXT_WEEK' => __('Next Week'),
            'NEXT_30_DAYS' => __('Next 30 Days'),
            'ROLLING_7' => __('Rolling 7 Days'),
            'ROLLING_14' => __('Rolling 14 Days'),
            'ROLLING_30' => __('Rolling 30 Days'),
        ];
    }

    protected function applySessionWindow(Builder $query, mixed $range, string $column = 'session_date'): Builder
    {
        return match ($range) {
            'TODAY' => $query->whereDate($column, Carbon::today()),
            'THIS_WEEK' => $query->whereBetween($column, [Carbon::now()->startOfWeek()->toDateString(), Carbon::now()->endOfWeek()->toDateString()]),
            'NEXT_WEEK' => $query->whereBetween($column, [Carbon::now()->addWeek()->startOfWeek()->toDateString(), Carbon::now()->addWeek()->endOfWeek()->toDateString()]),
            'ROLLING_7' => $query->whereBetween($column, [Carbon::today()->subDays(6)->toDateString(), Carbon::today()->toDateString()]),
            'ROLLING_14' => $query->whereBetween($column, [Carbon::today()->subDays(13)->toDateString(), Carbon::today()->toDateString()]),
            'ROLLING_30' => $query->whereBetween($column, [Carbon::today()->subDays(29)->toDateString(), Carbon::today()->toDateString()]),
            default => $query->whereBetween($column, [Carbon::today()->toDateString(), Carbon::today()->addDays(30)->toDateString()]),
        };
    }
}
