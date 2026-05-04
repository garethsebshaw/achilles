<?php

namespace App\Nova\Metrics;

use App\Models\User;
use App\Nova\Metrics\Concerns\InterpretsUserRanges;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class PrivilegedUsersMetric extends Value
{
    use InterpretsUserRanges;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $query = User::query()->where(function ($query) {
            $query->where('is_sys_admin', true)
                ->orWhere('is_admin', true)
                ->orWhere('is_team_leader', true);
        });
        $query = $this->applyRange($query, $request->range);

        return $this->result($query->count());
    }

    public function ranges()
    {
        return $this->standardRanges();
    }

    public function name()
    {
        return __('Privileged Users');
    }

    public function uriKey()
    {
        return 'privileged-users-metric';
    }
}
