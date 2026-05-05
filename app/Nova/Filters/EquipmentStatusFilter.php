<?php

namespace App\Nova\Filters;

use App\Models\Equipment;
use App\Models\SystemStatus;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class EquipmentStatusFilter extends Filter
{
    /**
     * The filter's component.
     *
     * @var string
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     */
    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('status_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @return array<string, string>
     */
    public function options(NovaRequest $request): array
    {
        return SystemStatus::query()
            ->forModelType(Equipment::class)
            ->orderBy('name')
            ->pluck('id', 'name')
            ->toArray();
    }

    public function name(): string
    {
        return __('Status');
    }
}
