<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\User;

class TotalGuides extends Value
{
    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $query = User::where('is_guide', true);

        // Handle year ranges
        $range = $request->range;
        if (is_numeric($range) && $range > 1000) { // This means it's a year
            return $this->result(
                $query->whereYear('updated_at', $range)->count()
            );
        }

        // Handle default ranges (30 days, MTD, etc)
        return $this->count($request, $query, 'updated_at');
    }

    public function ranges()
    {
        $currentYear = now()->year;

        return [
            'ALL' => __('All Time'),
            'TODAY' => __('Today'),
            'MTD' => __('Month To Date'),
            'QTD' => __('Quarter To Date'),
            'YTD' => __('Year To Date'),
            30 => __('30 Days'),
            60 => __('60 Days'),
            365 => __('1 Year'),
            720 => __('2 Years'),
//            $currentYear => (string)$currentYear,        // "2025"
//            ($currentYear-1) => (string)($currentYear-1), // "2024"
//            ($currentYear-2) => (string)($currentYear-2), // "2023"
        ];
    }

    public function uriKey()
    {
        return 'total-user-guides';
    }
}
