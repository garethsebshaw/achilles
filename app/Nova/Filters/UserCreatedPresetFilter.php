<?php

namespace App\Nova\Filters;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserCreatedPresetFilter extends Filter
{
    public $component = 'select-filter';

    public function apply(Request $request, $query, $value)
    {
        $today = Carbon::today();

        return match ($value) {
            'today' => $query->whereDate('created_at', $today),
            'last_7_days' => $query->where('created_at', '>=', $today->copy()->subDays(7)),
            'last_30_days' => $query->where('created_at', '>=', $today->copy()->subDays(30)),
            'last_90_days' => $query->where('created_at', '>=', $today->copy()->subDays(90)),
            'this_year' => $query->whereYear('created_at', now()->year),
            'older' => $query->where('created_at', '<', $today->copy()->subDays(365)),
            default => $query,
        };
    }

    public function options(Request $request): array
    {
        return [
            __('Created Today') => 'today',
            __('Joined in Last 7 Days') => 'last_7_days',
            __('Joined in Last 30 Days') => 'last_30_days',
            __('Joined in Last 90 Days') => 'last_90_days',
            __('Joined This Year') => 'this_year',
            __('Joined More Than 1 Year Ago') => 'older',
        ];
    }

    public function name()
    {
        return __('Joined');
    }
}
