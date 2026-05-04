<?php

namespace App\Nova\Metrics;

use App\Models\User;
use App\Nova\Metrics\Concerns\InterpretsUserRanges;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class UsersWithActiveLocationAccessMetric extends Value
{
    use InterpretsUserRanges;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $query = User::query()->whereHas('activeLocationAccessRecords');
        $query = $this->applyRange($query, $request->range);

        return $this->result($query->count());
    }

    public function ranges()
    {
        return $this->standardRanges();
    }

    public function name()
    {
        return __('Users With Active Location Access');
    }

    public function uriKey()
    {
        return 'users-with-active-location-access-metric';
    }
}
