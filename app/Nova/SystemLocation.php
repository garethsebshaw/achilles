<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class SystemLocation extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\SystemLocation>
     */
    public static $model = \App\Models\SystemLocation::class;

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
        'name', 'address_line_1', 'city', 'state', 'postal_code'
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

            new Panel('Basic Information', [
                Text::make('Name')
                    ->rules('required', 'max:255')
                    ->sortable(),

                Boolean::make('Active', 'is_active')
                    ->default(true)
                    ->sortable(),
            ]),

            new Panel('Address Information', [
                Text::make('Address Line 1')
                    ->hideFromIndex(),

                Text::make('Address Line 2')
                    ->hideFromIndex(),

                Text::make('City')
                    ->sortable(),

                Text::make('State')
                    ->sortable(),

                Text::make('Postal Code'),

                BelongsTo::make('Country', 'country', SystemCountry::class)
                    ->nullable(),

                BelongsTo::make('Region', 'region', SystemRegion::class)
                    ->nullable(),

                BelongsTo::make('Chapter', 'chapter', SystemChapter::class)
                    ->nullable(),

                Text::make('Latitude')
                    ->rules('nullable', 'numeric', 'between:-90,90')
                    ->hideFromIndex(),

                Text::make('Longitude')
                    ->rules('nullable', 'numeric', 'between:-180,180')
                    ->hideFromIndex(),
            ]),

            new Panel('Contact Information', [
                Text::make('Phone')
                    ->rules('nullable', 'max:255'),

                Text::make('Email')
                    ->rules('nullable', 'email', 'max:255'),

                Text::make('Contact Name')
                    ->rules('nullable', 'max:255'),

                Text::make('Timezone')
                    ->rules('nullable', 'max:255')
                    ->hideFromIndex(),
            ]),

            new Panel('Access Information', [
                Text::make('Access Code')
                    ->hideFromIndex()
                    ->onlyOnForms(),

                Textarea::make('Access Instructions')
                    ->hideFromIndex()
                    ->rows(3),

                HasMany::make('Access Records', 'userAccess', SystemLocationAccess::class),
            ]),

            new Panel('Additional Information', [
                Textarea::make('Notes')
                    ->hideFromIndex()
                    ->rows(3),

                Code::make('Metadata')
                    ->json()
                    ->hideFromIndex(),
            ]),

            new Panel('Related Items', [
                HasMany::make('Equipment'),
                HasMany::make('Storage Locations', 'storageLocations'),
                HasMany::make('Events'),
            ]),
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
