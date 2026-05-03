<?php

namespace App\Nova\Metrics;

use Laravel\Nova\Metrics\Value;
use App\Models\Language;
use Laravel\Nova\Http\Requests\NovaRequest;

class TotalLanguages extends Value
{
    public $width = '1/4';

    public function calculate(NovaRequest $request)
    {
        return $this->count($request, Language::class);
    }

    public function ranges()
    {
        return [
            30 => __('30 Days'),
            60 => __('60 Days'),
            365 => __('365 Days'),
        ];
    }

    public function uriKey()
    {
        return 'total-languages';
    }
}
