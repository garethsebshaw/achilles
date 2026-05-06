<?php

namespace App\Nova\Metrics\Logging;

use App\Modules\Logging\Models\OperatorEvent;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class CriticalEventsLast7Days extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->result(
            OperatorEvent::query()
                ->where('severity', 'critical')
                ->where('created_at', '>=', now()->subDays(7))
                ->count()
        );
    }

    public function name()
    {
        return __('Critical Events in Last 7 Days');
    }
}
