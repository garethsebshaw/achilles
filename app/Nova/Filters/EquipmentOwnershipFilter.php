<?php

namespace App\Nova\Filters;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class EquipmentOwnershipFilter extends Filter
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
        return match ($value) {
            'athlete' => $query->where('owner_type', 'athlete'),
            'partner' => $query->where('owner_type', 'partner'),
            'assigned' => $query->whereNotNull('assigned_user_id'),
            'unassigned' => $query->whereNull('assigned_user_id'),
            default => $query,
        };
    }

    /**
     * Get the filter's available options.
     *
     * @return array<string, string>
     */
    public function options(NovaRequest $request): array
    {
        return [
            __('Athlete Owned') => 'athlete',
            __('Partner Owned') => 'partner',
            __('Assigned To User') => 'assigned',
            __('Unassigned') => 'unassigned',
        ];
    }

    public function name(): string
    {
        return __('Ownership');
    }
}
