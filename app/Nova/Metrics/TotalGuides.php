<?php

namespace App\Nova\Metrics;

use App\Models\User;
use App\Nova\Metrics\Concerns\InterpretsUserRanges;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class TotalGuides extends Value
{
    use InterpretsUserRanges;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $query = User::query()->where('is_guide', true);
        $query = $this->applyRange($query, $request->range);

        return $this->result($query->count());
    }

    public function ranges()
    {
        return $this->standardRanges();
    }

    public function uriKey()
    {
        return 'total-user-guides';
    }
}
