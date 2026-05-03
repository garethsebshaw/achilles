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

class EquipmentComponent extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\EquipmentComponent>
     */
    public static $model = \App\Models\EquipmentComponent::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'model';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'model', 'serial_number'
    ];

    public static $group = 'Equipment Management';

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

            BelongsTo::make('Component Type', 'componentType')
                ->rules('required'),

            BelongsTo::make('Manufacturer')
                ->rules('required'),

            Text::make('Model')
                ->nullable(),

            Text::make('Serial Number')
                ->nullable(),

            BelongsTo::make('Status', 'status', SystemStatus::class)
                ->rules('required'),

            BelongsTo::make('Condition', 'equipmentCondition', EquipmentCondition::class)
                ->rules('required'),

            Date::make('Installation Date')
                ->nullable(),

            Date::make('Warranty Expiry')
                ->nullable(),

            Boolean::make('Is Monitored')
                ->default(false),

            Number::make('Maintenance Interval (Miles)', 'maintenance_interval_miles')
                ->nullable()
                ->min(0),

            Number::make('Maintenance Interval (Months)', 'maintenance_interval_months')
                ->nullable()
                ->min(0),

            DateTime::make('Last Maintenance Date')
                ->nullable(),

            DateTime::make('Next Maintenance Date')
                ->nullable(),

            Textarea::make('Notes')
                ->nullable()
                ->rows(3),

            Panel::make('Relationships', [
                HasMany::make('Maintenance Requests', 'maintenanceRequests', MaintenanceRequest::class),
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
