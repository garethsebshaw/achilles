<?php

namespace App\Nova\Metrics\Logging;

use App\Modules\Logging\Models\OperatorEvent;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class OpenOperatorEventsCount extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->result(
            OperatorEvent::query()
                ->where('status', 'open')
                ->count()
        );
    }

    public function name()
    {
        return __('Open Operator Events');
    }
}
