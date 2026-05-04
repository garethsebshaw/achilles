<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserVerificationFilter extends Filter
{
    public $component = 'select-filter';

    public function apply(Request $request, $query, $value)
    {
        return match ($value) {
            'verified' => $query->whereNotNull('email_verified_at'),
            'unverified' => $query->whereNull('email_verified_at'),
            default => $query,
        };
    }

    public function options(Request $request): array
    {
        return [
            __('Verified Users') => 'verified',
            __('Unverified Users') => 'unverified',
        ];
    }

    public function name()
    {
        return __('Verification');
    }
}
