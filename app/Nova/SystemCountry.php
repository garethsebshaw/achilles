<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\BelongsToMany;

class SystemCountry extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\SystemCountry>
     */
    public static $model = \App\Models\SystemCountry::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name', 'short_name', 'iso2', 'iso3'
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

            Text::make(__('Name'))
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make(__('Short Name'))
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make(__('Capital'))
                ->nullable()
                ->sortable(),

            Text::make(__('ISO2'), 'iso2')
                ->sortable()
                ->rules('required', 'size:2')
                ->hideFromIndex()
                ->displayUsing(fn ($value) => strtoupper($value)),

            Text::make(__('ISO3'), 'iso3')
                ->sortable()
                ->rules('required', 'size:3')
                ->hideFromIndex()
                ->displayUsing(fn ($value) => strtoupper($value)),

            Text::make(__('Numeric Code'))
                ->sortable()
                ->filterable()
                ->hideFromIndex()
                ->rules('required', 'size:3'),

            Text::make(__('Calling Code'))
                ->sortable()
                ->filterable()
                ->rules('required'),

            Text::make(__('Currency'))
                ->nullable()
                ->sortable()
                ->filterable(),

            Text::make(__('Currency Symbol'))
                ->nullable()
                ->filterable(),

            Boolean::make(__('Active'))
                ->sortable()
                ->filterable()
                ->default(false),

            Code::make(__('Metadata'))
                ->json()
                ->nullable(),

            BelongsToMany::make(__('Certifications')),
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
