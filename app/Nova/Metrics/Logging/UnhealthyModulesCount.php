<?php

namespace App\Nova\Metrics\Logging;

use App\Modules\Logging\Models\ModuleHealthCheck;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class UnhealthyModulesCount extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->result(
            ModuleHealthCheck::query()
                ->where('status', '!=', 'healthy')
                ->whereNull('resolved_at')
                ->count()
        );
    }

    public function name()
    {
        return __('Unhealthy Modules');
    }
}
