<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Metrics\Value;
use App\Models\LanguageProficiency;
use Laravel\Nova\Http\Requests\NovaRequest;

class TotalLanguageProficiencies extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, LanguageProficiency::class);
    }

    public function ranges()
    {
        return [
            30 => __('30 Days'),
            60 => __('60 Days'),
            365 => __('365 Days'),
            'TODAY' => __('Today'),
            'MTD' => __('Month To Date'),
            'QTD' => __('Quarter To Date'),
            'YTD' => __('Year To Date'),
        ];
    }

    public function uriKey()
    {
        return 'total-language-proficiencies';
    }
}
