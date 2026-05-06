<?php

namespace App\Nova\Filters\Logging;

use App\Models\Tenant;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;

class TenantFilter extends Filter
{
    public $component = 'select-filter';

    public function name()
    {
        return __('Tenant');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        if ($value === '__platform__') {
            return $query->whereNull('tenant_id');
        }

        return $query->where('tenant_id', $value);
    }

    public function options(NovaRequest $request)
    {
        return Tenant::query()
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn (Tenant $tenant) => [$tenant->name => $tenant->id])
            ->put(__('Platform Level'), '__platform__')
            ->all();
    }
}
