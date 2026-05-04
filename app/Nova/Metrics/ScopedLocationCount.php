<?php

namespace App\Nova\Metrics;

use App\Models\SystemLocation;
use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class ScopedLocationCount extends Value
{
    use ResolvesDashboardScope;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $query = SystemLocation::query()->where('is_active', true);
        $this->applyScopedLocationFilter($query, 'system_locations.id', $this->dashboardUser($request));

        return $this->result($query->count())
            ->help(__('Active locations in :scope.', ['scope' => $this->scopeLabel($this->dashboardUser($request))]));
    }

    public function name()
    {
        return __('Scoped Active Locations');
    }
}
