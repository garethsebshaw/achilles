<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserSubscriptionFilter extends Filter
{
    public $component = 'select-filter';

    public function apply(Request $request, $query, $value)
    {
        return match ($value) {
            'subscribed' => $query->where('is_subscribed', true),
            'unsubscribed' => $query->where('is_subscribed', false),
            default => $query,
        };
    }

    public function options(Request $request): array
    {
        return [
            __('Subscribed Users') => 'subscribed',
            __('Unsubscribed Users') => 'unsubscribed',
        ];
    }

    public function name()
    {
        return __('Subscription');
    }
}
