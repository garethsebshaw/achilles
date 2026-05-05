<?php

namespace App\Nova\Filters;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class LocationActiveFilter extends Filter
{
    public $component = 'select-filter';

    public function apply(NovaRequest $request, Builder $query, mixed $value): Builder
    {
        return $query->where('is_active', (bool) $value);
    }

    public function options(NovaRequest $request): array
    {
        return [
            __('Active') => 1,
            __('Inactive') => 0,
        ];
    }

    public function name(): string
    {
        return __('Location Status');
    }
}
