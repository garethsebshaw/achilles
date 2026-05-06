<?php

namespace App\Nova;

use App\Nova\Filters\Logging\DateWindowFilter;
use App\Nova\Filters\Logging\TenantFilter;
use App\Nova\Lenses\FailedJobsLens;
use App\Nova\Lenses\RetriedJobsLens;
use App\Nova\Lenses\SlowJobsLens;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class JobLog extends LoggingResource
{
    public static $model = \App\Modules\Logging\Models\JobLog::class;

    public static $title = 'job_class';

    public static $search = ['job_class', 'job_uuid', 'queue', 'status', 'exception_class'];

    public static $group = 'Logging';

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
            Text::make(__('Tenant'), fn () => optional($this->tenant)->name ?? __('Platform'))->sortable(),
            Text::make(__('Job UUID'), 'job_uuid')->hideFromIndex(),
            Text::make(__('Job Class'), 'job_class')->sortable()->filterable(),
            Text::make(__('Queue'), 'queue')->sortable()->filterable(),
            Text::make(__('Connection'), 'connection')->hideFromIndex(),
            Text::make(__('Status'), 'status')->sortable()->filterable(),
            Number::make(__('Attempts'), 'attempts')->sortable(),
            DateTime::make(__('Started At'), 'started_at')->sortable(),
            DateTime::make(__('Finished At'), 'finished_at')->sortable(),
            Number::make(__('Duration (ms)'), 'duration_ms')->sortable(),
            Text::make(__('Exception Class'), 'exception_class')->hideFromIndex(),
            Text::make(__('Exception Message'), 'exception_message')->hideFromIndex(),
            Code::make(__('Payload Summary'), 'payload_summary')->json()->onlyOnDetail(),
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
            new FailedJobsLens(),
            new SlowJobsLens(),
            new RetriedJobsLens(),
        ];
    }
}
