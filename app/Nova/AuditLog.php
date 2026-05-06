<?php

namespace App\Nova;

use App\Nova\Actions\Logging\ExportAuditLogs;
use App\Nova\Filters\Logging\DateWindowFilter;
use App\Nova\Filters\Logging\TenantFilter;
use App\Nova\Lenses\RecentUserAdminActionsLens;
use App\Nova\Lenses\SecuritySensitiveAuditLogsLens;
use App\Nova\Lenses\SecretRelatedAuditLogsLens;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class AuditLog extends LoggingResource
{
    public static $model = \App\Modules\Logging\Models\AuditLog::class;

    public static $title = 'action';

    public static $search = ['action', 'auditable_type', 'request_id'];

    public static $group = 'Logging';

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make(__('Tenant'), fn () => optional($this->tenant)->name ?? __('Platform'))->sortable(),
            Text::make(__('User'), fn () => optional($this->user)->name)->sortable(),
            Text::make(__('Action'), 'action')->sortable()->filterable(),
            Text::make(__('Auditable Type'), 'auditable_type')->sortable()->filterable(),
            Text::make(__('Auditable ID'), 'auditable_id')->sortable(),
            DateTime::make(__('Created At'), 'created_at')->sortable(),
            Code::make(__('Before'), 'before')->json()->onlyOnDetail(),
            Code::make(__('After'), 'after')->json()->onlyOnDetail(),
            Code::make(__('Metadata'), 'metadata')->json()->onlyOnDetail(),
        ];
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new TenantFilter(),
            new DateWindowFilter('created_at'),
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [
            new SecuritySensitiveAuditLogsLens(),
            new RecentUserAdminActionsLens(),
            new SecretRelatedAuditLogsLens(),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return $request->user()?->isSysAdmin()
            ? [new ExportAuditLogs()]
            : [];
    }
}
