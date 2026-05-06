<?php

namespace App\Nova\Metrics\Logging;

use App\Modules\Logging\Models\SystemLog;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class LogsByModule extends Partition
{
    public function calculate(NovaRequest $request)
    {
        return $this->result(
            SystemLog::query()
                ->whereNotNull('module_key')
                ->selectRaw('module_key, count(*) as aggregate')
                ->groupBy('module_key')
                ->pluck('aggregate', 'module_key')
                ->all()
        );
    }

    public function name()
    {
        return __('Logs by Module');
    }
}
