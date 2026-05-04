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

            BelongsTo::make(__('Equipment'))
                ->rules('required'),

            BelongsTo::make(__('Component Type'), 'componentType')
                ->rules('required'),

            BelongsTo::make(__('Manufacturer'))
                ->rules('required'),

            Text::make(__('Model'))
                ->nullable(),

            Text::make(__('Serial Number'))
                ->nullable(),

            BelongsTo::make(__('Status'), 'status', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    return $query->forModelType(\App\Models\EquipmentComponent::class);
                })
                ->rules('required'),

            BelongsTo::make(__('Condition'), 'condition', EquipmentCondition::class)
                ->rules('required'),

            Date::make(__('Installation Date'))
                ->nullable(),

            Date::make(__('Warranty Expiry'))
                ->nullable(),

            Boolean::make(__('Is Monitored'))
                ->default(false),

            Number::make(__('Maintenance Interval (Miles)'), 'maintenance_interval_miles')
                ->nullable()
                ->min(0),

            Number::make(__('Maintenance Interval (Months)'), 'maintenance_interval_months')
                ->nullable()
                ->min(0),

            DateTime::make(__('Last Maintenance Date'))
                ->nullable(),

            DateTime::make(__('Next Maintenance Date'))
                ->nullable(),

            Textarea::make(__('Notes'))
                ->nullable()
                ->rows(3),

            Panel::make(__('Relationships'), [
                HasMany::make(__('Maintenance Requests'), 'maintenanceRequests', MaintenanceRequest::class),
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
