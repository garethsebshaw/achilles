<?php

namespace App\Nova\Filters;

use App\Models\SystemStatus;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class LanguageProficiencyFilter extends Filter
{
    public function name()
    {
        return __('Proficiency Level');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->whereHas('proficiencyStatus', function ($q) use ($value) {
            $q->where('id', $value);
        });
    }

    public function options(NovaRequest $request)
    {
        return SystemStatus::query()
            ->forModelType(\App\Models\LanguageProficiency::class)
            ->where('code', 'LIKE', 'lang_%')
            ->pluck('name', 'id')
            ->toArray();
    }
}
