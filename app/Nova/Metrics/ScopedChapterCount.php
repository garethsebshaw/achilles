<?php

namespace App\Nova\Metrics;

use App\Models\SystemLocation;
use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class ScopedChapterCount extends Value
{
    use ResolvesDashboardScope;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $query = SystemLocation::query()->whereNotNull('chapter_id');
        $this->applyScopedLocationFilter($query, 'system_locations.id', $this->dashboardUser($request));

        return $this->result($query->distinct('chapter_id')->count('chapter_id'))
            ->help(__('Operational chapter coverage for :scope.', ['scope' => $this->scopeLabel($this->dashboardUser($request))]));
    }

    public function name()
    {
        return __('Scoped Chapters');
    }
}
