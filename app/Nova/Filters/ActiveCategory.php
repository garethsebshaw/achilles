<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class ActiveCategory extends Filter
{
    public function name()
    {
        return __('Active Categories');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('active', $value);
    }

    public function options(NovaRequest $request)
    {
        return [
            __('Active') => 1,
            __('Inactive') => 0,
        ];
    }

    public function default()
    {
        return 1; // Default to showing active categories
    }

    // Ensure correct method signature
    public function authorizedToSee(Request $request)
    {
        return true;
    }
}
