<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class ActiveLanguage extends Filter
{
    public $name = 'Active Languages';

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('active', $value);
    }

    public function options(NovaRequest $request)
    {
        return [
            'Active' => 1,
            'Inactive' => 0
        ];
    }

    public function default()
    {
        return 1; // Default to showing active languages
    }

    // Use the correct method signature from the parent class
    public function authorizedToSee(Request $request)
    {
        return true; // Or add your custom authorization logic
    }
}
