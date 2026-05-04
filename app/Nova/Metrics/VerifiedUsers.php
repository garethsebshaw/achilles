<?php

namespace App\Nova\Metrics;

use App\Models\User;
use App\Nova\Metrics\Concerns\InterpretsUserRanges;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class VerifiedUsers extends Value
{
    use InterpretsUserRanges;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $query = User::query()->whereNotNull('email_verified_at');
        $query = $this->applyRange($query, $request->range);

        return $this->result($query->count());
    }

    public function ranges()
    {
        return $this->standardRanges();
    }

    public function name()
    {
        return __('Verified Users');
    }

    public function uriKey()
    {
        return 'verified-users';
    }
}
