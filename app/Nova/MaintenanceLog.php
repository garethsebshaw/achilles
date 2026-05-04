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

class MaintenanceLog extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\MaintenanceLog>
     */
    public static $model = \App\Models\MaintenanceLog::class;

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

            BelongsTo::make(__('Maintenance Request'), 'maintenanceRequest', MaintenanceRequest::class)
                ->nullable(),

            BelongsTo::make(__('Equipment'))
                ->rules('required'),

            BelongsTo::make(__('Component'), 'component', EquipmentComponent::class)
                ->nullable(),

            BelongsTo::make(__('Performed By'), 'performedBy', User::class)
                ->rules('required'),

            Select::make(__('Work Type'))
                ->options([
                    'service' => __('Service'),
                    'repair' => __('Repair'),
                    'inspection' => __('Inspection'),
                ])
                ->rules('required'),

            Textarea::make(__('Description'))
                ->rules('required'),

            DateTime::make(__('Performed At'))
                ->rules('required'),

            Number::make(__('Time Spent (Minutes)'))
                ->nullable()
                ->min(0),

            Currency::make(__('Cost'))
                ->nullable(),

            Code::make(__('Parts Used'))
                ->json()
                ->nullable(),

            Textarea::make(__('Notes'))
                ->nullable()
                ->rows(3),

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
