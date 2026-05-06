<?php

namespace App\Nova\Metrics\Logging;

use App\Modules\Logging\Models\JobLog;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class FailedJobsLast24Hours extends Value
{
    public function calculate(NovaRequest $request)
    {
        return $this->result(
            JobLog::query()
                ->where('status', 'failed')
                ->where('created_at', '>=', now()->subDay())
                ->count()
        );
    }

    public function name()
    {
        return __('Failed Jobs in Last 24 Hours');
    }
}
