<?php

namespace App\Nova;

use App\Nova\Actions\Logging\CopyLogContext;
use App\Nova\Actions\Logging\DeleteOldSystemLogs;
use App\Nova\Filters\Logging\DateWindowFilter;
use App\Nova\Filters\Logging\TenantFilter;
use App\Nova\Lenses\SystemLogCriticalLogs;
use App\Nova\Lenses\SystemLogRecentErrors;
use App\Nova\Lenses\SystemLogWithCorrelationId;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class SystemLog extends LoggingResource
{
    public static $model = \App\Modules\Logging\Models\SystemLog::class;

    public static $title = 'event_key';

    public static $search = ['event_key', 'message', 'module_key', 'level', 'correlation_id', 'request_id'];

    public static $group = 'Logging';

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make(__('Tenant'), fn () => optional($this->tenant)->name ?? __('Platform'))
                ->sortable(),
            Text::make(__('User'), fn () => optional($this->user)->name)->sortable(),
            Text::make(__('Level'), 'level')->sortable()->filterable(),
            Text::make(__('Module Key'), 'module_key')->sortable()->filterable(),
            Text::make(__('Event Key'), 'event_key')->sortable()->filterable(),
            Text::make(__('Message'), 'message')->sortable(),
            Text::make(__('Source Type'), 'source_type')->sortable()->filterable(),
            Text::make(__('Request ID'), 'request_id')->hideFromIndex(),
            Text::make(__('Correlation ID'), 'correlation_id')->hideFromIndex(),
            DateTime::make(__('Created At'), 'created_at')->sortable(),
            Code::make(__('Context'), 'context')->json()->onlyOnDetail(),
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
            new SystemLogRecentErrors(),
            new SystemLogCriticalLogs(),
            new SystemLogWithCorrelationId(),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        $actions = [
            new CopyLogContext(),
        ];

        if ($request->user()?->isSysAdmin()) {
            $actions[] = (new DeleteOldSystemLogs())->standalone();
        }

        return $actions;
    }
}
