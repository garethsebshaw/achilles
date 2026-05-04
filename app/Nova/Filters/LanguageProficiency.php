<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\SystemStatus;

class LanguageProficiency extends Filter
{
    public function name()
    {
        return __('Proficiency Level');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('proficiency_status_id', $value);
    }

    public function options(NovaRequest $request)
    {
        return SystemStatus::query()
            ->forModelType(\App\Models\LanguageProficiency::class)
            ->where('code', 'LIKE', 'lang_%')
            ->pluck('name', 'id')
            ->toArray();
    }

    public function default()
    {
        return null;
    }

    public function authorizedToSee(Request $request)
    {
        return true;
    }
}
