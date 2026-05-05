<?php

namespace App\Nova\Filters;

use App\Models\SystemCountry;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class LocationCountryFilter extends Filter
{
    public $component = 'select-filter';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('country_id', $value);
    }

    public function options(NovaRequest $request): array
    {
        return SystemCountry::query()
            ->orderBy('name')
            ->pluck('id', 'name')
            ->toArray();
    }

    public function name(): string
    {
        return __('Country');
    }
}
