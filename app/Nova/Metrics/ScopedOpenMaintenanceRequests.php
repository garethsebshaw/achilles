<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class ScopedOpenMaintenanceRequests extends Value
{
    use ResolvesDashboardScope;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $count = $this->scopedMaintenanceQuery($this->dashboardUser($request))
            ->whereNotIn('system_statuses.code', ['maintreq_completed', 'maintreq_cancelled'])
            ->distinct('maintenance_requests.id')
            ->count('maintenance_requests.id');

        return $this->result($count)->help(__('Maintenance requests still requiring action.'));
    }

    public function name()
    {
        return __('Scoped Open Maintenance');
    }
}
