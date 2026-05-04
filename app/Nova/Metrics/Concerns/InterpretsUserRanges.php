<?php

namespace App\Nova\Metrics\Concerns;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

trait InterpretsUserRanges
{
    protected function applyRange(Builder $query, mixed $range, string $column = 'created_at'): Builder
    {
        return match ($range) {
            'ALL', null => $query,
            'TODAY' => $query->whereDate($column, Carbon::today()),
            'MTD' => $query->whereBetween($column, [Carbon::now()->startOfMonth(), Carbon::now()->endOfDay()]),
            'QTD' => $query->whereBetween($column, [Carbon::now()->startOfQuarter(), Carbon::now()->endOfDay()]),
            'YTD' => $query->whereBetween($column, [Carbon::now()->startOfYear(), Carbon::now()->endOfDay()]),
            default => is_numeric($range)
                ? $query->where($column, '>=', Carbon::now()->subDays((int) $range))
                : $query,
        };
    }

    protected function standardRanges(): array
    {
        return [
            'ALL' => __('All Time'),
            'TODAY' => __('Today'),
            'MTD' => __('Month To Date'),
            'QTD' => __('Quarter To Date'),
            'YTD' => __('Year To Date'),
            30 => __('30 Days'),
            90 => __('90 Days'),
            365 => __('1 Year'),
        ];
    }
}
