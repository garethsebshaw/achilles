<?php

namespace App\Nova;

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

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Equipment')
                ->rules('required'),

            BelongsTo::make('Component', 'component', EquipmentComponent::class)
                ->nullable(),

            BelongsTo::make('Reported By', 'reportedBy', User::class)
                ->rules('required'),

            BelongsTo::make('Assigned To', 'assignedTo', User::class)
                ->nullable(),

            BelongsTo::make('Status', 'status', SystemStatus::class)
                ->rules('required'),

            BelongsTo::make('Priority')
                ->rules('required'),

            Textarea::make('Description')
                ->rules('required'),

            DateTime::make('Reported At')
                ->rules('required'),

            DateTime::make('Assigned At')
                ->nullable(),

            Number::make('Estimated Time (Minutes)')
                ->nullable()
                ->min(0),

            Number::make('Actual Time (Minutes)')
                ->nullable()
                ->min(0),

            Currency::make('Estimated Cost')
                ->nullable(),

            Currency::make('Actual Cost')
                ->nullable(),

            DateTime::make('Completed At')
                ->nullable(),

            BelongsTo::make('Parent Request', 'parentRequest', MaintenanceRequest::class)
                ->nullable(),

            Textarea::make('Notes')
                ->nullable()
                ->rows(3),

            Panel::make('Relationships', [
                HasMany::make('Child Requests', 'childRequests', MaintenanceRequest::class),
                HasMany::make('Maintenance Logs', 'maintenanceLogs', MaintenanceLog::class),
            ]),

            DateTime::make('Created At')->onlyOnDetail(),
            DateTime::make('Updated At')->onlyOnDetail(),
        ];
    }

    /**
     * Get the cards available for the resource.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Laravel\Nova\Filters\Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [];
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
