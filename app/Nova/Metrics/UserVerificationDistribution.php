<?php

namespace App\Nova\Metrics;

use App\Models\User;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class UserVerificationDistribution extends Partition
{
    public $width = '1/2';

    public function calculate(NovaRequest $request)
    {
        $verified = User::query()->whereNotNull('email_verified_at')->count();
        $unverified = User::query()->whereNull('email_verified_at')->count();

        return $this->result([
            __('Verified Users') => $verified,
            __('Unverified Users') => $unverified,
        ])->colors([
            __('Verified Users') => '#38a169',
            __('Unverified Users') => '#dd6b20',
        ]);
    }

    public function name()
    {
        return __('Verification Distribution');
    }

    public function uriKey()
    {
        return 'user-verification-distribution';
    }
}
