<?php

namespace App\Nova\Metrics\Logging;

use App\Modules\Logging\Models\SystemLog;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class ErrorsLast24Hours extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->result(
            SystemLog::query()
                ->whereIn('level', ['error', 'critical', 'alert', 'emergency'])
                ->where('created_at', '>=', now()->subDay())
                ->count()
        );
    }

    public function name()
    {
        return __('Errors in Last 24 Hours');
    }
}
