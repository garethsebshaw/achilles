<?php

namespace App\Nova\Metrics\Logging;

use App\Modules\Logging\Models\SystemLog;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class LogsByLevel extends Partition
{
    public function calculate(NovaRequest $request)
    {
        return $this->result(
            SystemLog::query()
                ->selectRaw('level, count(*) as aggregate')
                ->groupBy('level')
                ->pluck('aggregate', 'level')
                ->all()
        );
    }

    public function name()
    {
        return __('Logs by Level');
    }
}
