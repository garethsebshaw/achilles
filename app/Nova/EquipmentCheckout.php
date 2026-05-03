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

class EquipmentCheckout extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\EquipmentCheckout>
     */
    public static $model = \App\Models\EquipmentCheckout::class;

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
        'notes',
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

            BelongsTo::make('User')
                ->rules('required'),

            BelongsTo::make('Event', 'event', Event::class)
                ->nullable(),

            DateTime::make('Checked Out At')
                ->rules('required'),

            DateTime::make('Expected Return At')
                ->nullable(),

            DateTime::make('Returned At')
                ->nullable(),

            Number::make('Distance Traveled')
                ->nullable()
                ->min(0),

            BelongsTo::make('Condition Out', 'conditionOut', EquipmentCondition::class)
                ->rules('required'),

            BelongsTo::make('Condition In', 'conditionIn', EquipmentCondition::class)
                ->nullable(),

            Textarea::make('Notes')
                ->nullable()
                ->rows(3),

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
