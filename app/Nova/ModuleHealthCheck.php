<?php

namespace App\Nova;

use App\Nova\Actions\Logging\IgnoreHealthChecks;
use App\Nova\Actions\Logging\ResolveHealthChecks;
use App\Nova\Filters\Logging\DateWindowFilter;
use App\Nova\Filters\Logging\TenantFilter;
use App\Nova\Lenses\PlatformLevelIssuesLens;
use App\Nova\Lenses\TenantSpecificIssuesLens;
use App\Nova\Lenses\UnhealthyModulesLens;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class ModuleHealthCheck extends LoggingResource
{
    public static $model = \App\Modules\Logging\Models\ModuleHealthCheck::class;

    public static $title = 'summary';

    public static $search = ['module_key', 'summary', 'status', 'severity'];

    public static $group = 'Logging';

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make(__('Tenant'), fn () => optional($this->tenant)->name ?? __('Platform'))->sortable(),
            Text::make(__('Module Key'), 'module_key')->sortable()->filterable(),
            Text::make(__('Status'), 'status')->sortable()->filterable(),
            Text::make(__('Severity'), 'severity')->sortable()->filterable(),
            Text::make(__('Summary'), 'summary')->sortable(),
            DateTime::make(__('Last Checked At'), 'last_checked_at')->sortable(),
            DateTime::make(__('Resolved At'), 'resolved_at')->sortable(),
            Code::make(__('Details'), 'details')->json()->onlyOnDetail(),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new TenantFilter(),
            new DateWindowFilter('last_checked_at', __('Last Checked Window')),
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [
            new UnhealthyModulesLens(),
            new TenantSpecificIssuesLens(),
            new PlatformLevelIssuesLens(),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            new ResolveHealthChecks(),
            new IgnoreHealthChecks(),
        ];
    }
}
