<?php

namespace App\Nova\Filters;

use App\Models\MaintenanceRequest;
use App\Models\SystemStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class MaintenanceStatusFilter extends Filter
{
    public $component = 'select-filter';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('status_id', $value);
    }

    public function options(NovaRequest $request): array
    {
        return SystemStatus::query()
            ->forModelType(MaintenanceRequest::class)
            ->orderBy('name')
            ->pluck('id', 'name')
            ->toArray();
    }

    public function name(): string
    {
        return __('Status');
    }
}
