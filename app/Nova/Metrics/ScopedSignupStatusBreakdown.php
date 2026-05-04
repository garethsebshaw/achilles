<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class ScopedSignupStatusBreakdown extends Partition
{
    use ResolvesDashboardScope;

    public $width = '1/2';

    public function calculate(NovaRequest $request)
    {
        $data = $this->scopedSignupQuery($this->dashboardUser($request))
            ->whereDate('workout_sessions.session_date', '>=', now()->subDays(30)->toDateString())
            ->selectRaw("COALESCE(system_statuses.name, 'Unknown') as status_label, COUNT(*) as aggregate")
            ->groupBy('status_label')
            ->pluck('aggregate', 'status_label')
            ->toArray();

        return $this->result($data);
    }

    public function name()
    {
        return __('Scoped Signup Statuses');
    }
}
