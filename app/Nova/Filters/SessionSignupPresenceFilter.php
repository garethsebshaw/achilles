<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class SessionSignupPresenceFilter extends Filter
{
    public $component = 'select-filter';

    public function name()
    {
        return __('Signup Presence');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        return match ($value) {
            'with_signups' => $query->has('signups'),
            'without_signups' => $query->doesntHave('signups'),
            default => $query,
        };
    }

    public function options(NovaRequest $request)
    {
        return [
            __('With Signups') => 'with_signups',
            __('Without Signups') => 'without_signups',
        ];
    }
}
