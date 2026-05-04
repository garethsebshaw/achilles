<?php

namespace App\Nova\Filters;

use App\Models\SystemModule;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class ModuleImplementationStateFilter extends Filter
{
    public $component = 'select-filter';

    public function name()
    {
        return __('Implementation State');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('metadata->implementation_state', $value);
    }

    public function options(NovaRequest $request)
    {
        return [
            __('Implemented') => SystemModule::STATE_IMPLEMENTED,
            __('Partial Shell') => SystemModule::STATE_PARTIAL_SHELL,
            __('Mapped Alias') => SystemModule::STATE_MAPPED_ALIAS,
            __('Spec Only') => SystemModule::STATE_MISSING_SPEC_ONLY,
        ];
    }
}
