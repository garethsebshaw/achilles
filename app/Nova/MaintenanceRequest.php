<?php

namespace App\Nova;

use App\Nova\Filters\MaintenancePriorityFilter;
use App\Nova\Filters\MaintenanceStatusFilter;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class MaintenanceRequest extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\MaintenanceRequest>
     */
    public static $model = \App\Models\MaintenanceRequest::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'id';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'description',
    ];

    public static $perPageOptions = [25, 50, 100];

    public static function indexQuery(NovaRequest $request, BuilderContract $query): BuilderContract
    {
        $query = $query
            ->with([
                'equipment.location.chapter',
                'component',
                'reportedBy',
                'assignedTo',
                'status',
                'priority',
            ]);

        if (! $request->filled('orderBy')) {
            $query->orderByDesc('reported_at')
                ->orderByDesc('id');
        }

        return $query;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make(__('Equipment'))
                ->searchable()
                ->rules('required'),

            BelongsTo::make(__('Component'), 'component', EquipmentComponent::class)
                ->searchable()
                ->nullable(),

            BelongsTo::make(__('Reported By'), 'reportedBy', User::class)
                ->searchable()
                ->rules('required'),

            BelongsTo::make(__('Assigned To'), 'assignedTo', User::class)
                ->searchable()
                ->nullable(),

            BelongsTo::make(__('Status'), 'status', SystemStatus::class)
                ->searchable()
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    return $query->forModelType(\App\Models\MaintenanceRequest::class);
                })
                ->rules('required'),

            BelongsTo::make(__('Priority'), 'priority', EquipmentMaintenancePriority::class)
                ->searchable()
                ->rules('required'),

            Textarea::make(__('Description'))
                ->rules('required'),

            DateTime::make(__('Reported At'))
                ->rules('required'),

            DateTime::make(__('Assigned At'))
                ->nullable(),

            Number::make(__('Estimated Time (Minutes)'))
                ->nullable()
                ->min(0),

            Number::make(__('Actual Time (Minutes)'))
                ->nullable()
                ->min(0),

            Currency::make(__('Estimated Cost'))
                ->nullable(),

            Currency::make(__('Actual Cost'))
                ->nullable(),

            DateTime::make(__('Completed At'))
                ->nullable(),

            BelongsTo::make(__('Parent Request'), 'parentRequest', MaintenanceRequest::class)
                ->searchable()
                ->nullable(),

            Textarea::make(__('Notes'))
                ->nullable()
                ->rows(3),

            Panel::make(__('Relationships'), [
                HasMany::make(__('Child Requests'), 'childRequests', MaintenanceRequest::class),
                HasMany::make(__('Maintenance Logs'), 'maintenanceLogs', MaintenanceLog::class),
            ]),

            DateTime::make(__('Created At'))->onlyOnDetail(),
            DateTime::make(__('Updated At'))->onlyOnDetail(),
        ];
    }

    /**
     * Get the cards available for the resource.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [
            new Metrics\ActiveMaintenanceRequests(),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Laravel\Nova\Filters\Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [
            new MaintenanceStatusFilter(),
            new MaintenancePriorityFilter(),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array<int, \Laravel\Nova\Lenses\Lens>
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, \Laravel\Nova\Actions\Action>
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
