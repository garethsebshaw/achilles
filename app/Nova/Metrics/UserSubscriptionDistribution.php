<?php

namespace App\Nova\Metrics;

use App\Models\User;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class UserSubscriptionDistribution extends Partition
{
    public $width = '1/2';

    public function calculate(NovaRequest $request)
    {
        $subscribed = User::query()->where('is_subscribed', true)->count();
        $unsubscribed = User::query()->where('is_subscribed', false)->count();

        return $this->result([
            __('Subscribed Users') => $subscribed,
            __('Unsubscribed Users') => $unsubscribed,
        ])->colors([
            __('Subscribed Users') => '#3182ce',
            __('Unsubscribed Users') => '#718096',
        ]);
    }

    public function name()
    {
        return __('Subscription Distribution');
    }

    public function uriKey()
    {
        return 'user-subscription-distribution';
    }
}
