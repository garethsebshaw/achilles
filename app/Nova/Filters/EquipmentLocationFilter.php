<?php

namespace App\Nova\Filters;

use App\Models\SystemLocation;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class EquipmentLocationFilter extends Filter
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
        return $query->where('location_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @return array<string, string>
     */
    public function options(NovaRequest $request): array
    {
        return SystemLocation::query()
            ->orderBy('name')
            ->pluck('id', 'name')
            ->toArray();
    }

    public function name(): string
    {
        return __('Location');
    }
}
