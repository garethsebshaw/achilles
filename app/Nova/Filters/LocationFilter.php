<?php

namespace App\Nova\Filters;

use App\Models\SystemLocation;
use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class LocationFilter extends Filter
{
    public $component = 'select-filter';

    public function apply(Request $request, $query, $value)
    {
        return $query->where('location_id', $value);
    }

    public function options(Request $request)
    {
        return SystemLocation::query()
            ->orderBy('name')
            ->pluck('id', 'name')
            ->toArray();
    }

    public function name()
    {
        return __('Location');
    }
}
