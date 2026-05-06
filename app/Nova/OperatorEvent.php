<?php

namespace App\Nova;

use App\Nova\Actions\Logging\AcknowledgeOperatorEvents;
use App\Nova\Actions\Logging\IgnoreOperatorEvents;
use App\Nova\Actions\Logging\ResolveOperatorEvents;
use App\Nova\Filters\Logging\DateWindowFilter;
use App\Nova\Filters\Logging\TenantFilter;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class OperatorEvent extends LoggingResource
{
    public static $model = \App\Modules\Logging\Models\OperatorEvent::class;

    public static $title = 'title';

    public static $search = ['title', 'event_key', 'module_key', 'status', 'severity'];

    public static $group = 'Logging';

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make(__('Tenant'), fn () => optional($this->tenant)->name ?? __('Platform'))->sortable(),
            Text::make(__('User'), fn () => optional($this->user)->name)->sortable(),
            Text::make(__('Module Key'), 'module_key')->sortable()->filterable(),
            Text::make(__('Event Key'), 'event_key')->sortable()->filterable(),
            Text::make(__('Title'), 'title')->sortable(),
            Text::make(__('Description'), 'description')->hideFromIndex(),
            Text::make(__('Severity'), 'severity')->sortable()->filterable(),
            Text::make(__('Status'), 'status')->sortable()->filterable(),
            DateTime::make(__('Created At'), 'created_at')->sortable(),
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

    public function actions(NovaRequest $request): array
    {
        return [
            new AcknowledgeOperatorEvents(),
            new ResolveOperatorEvents(),
            new IgnoreOperatorEvents(),
        ];
    }
}
