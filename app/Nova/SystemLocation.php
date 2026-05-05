<?php

namespace App\Nova;

use App\Nova\Filters\LocationActiveFilter;
use App\Nova\Filters\LocationCountryFilter;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

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

    public static $perPageOptions = [25, 50, 100];

    public static function indexQuery(NovaRequest $request, BuilderContract $query): BuilderContract
    {
        $query = $query
            ->with(['chapter', 'country', 'region'])
            ->withCount([
                'equipment',
                'storageLocations',
                'events',
                'activeAccessRecords as active_access_count',
            ]);

        if (! $request->filled('orderBy')) {
            $query->orderByDesc('is_active')
                ->orderBy('name');
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

            new Panel(__('Basic Information'), [
                Text::make(__('Name'))
                    ->rules('required', 'max:255')
                    ->sortable(),

                Boolean::make(__('Active'), 'is_active')
                    ->default(true)
                    ->sortable(),

                Text::make(__('Chapter'), fn () => $this->chapter?->name)
                    ->exceptOnForms()
                    ->sortable(),
            ]),

            new Panel(__('Address Information'), [
                Text::make(__('Address Line 1'))
                    ->hideFromIndex(),

                Text::make(__('Address Line 2'))
                    ->hideFromIndex(),

                Text::make(__('City'))
                    ->sortable(),

                Text::make(__('State'))
                    ->sortable(),

                Text::make(__('Postal Code')),

                BelongsTo::make(__('Country'), 'country', SystemCountry::class)
                    ->nullable(),

                BelongsTo::make(__('Region'), 'region', SystemRegion::class)
                    ->nullable(),

                BelongsTo::make(__('Chapter'), 'chapter', SystemChapter::class)
                    ->nullable(),

                Text::make(__('Latitude'))
                    ->rules('nullable', 'numeric', 'between:-90,90')
                    ->hideFromIndex(),

                Text::make(__('Longitude'))
                    ->rules('nullable', 'numeric', 'between:-180,180')
                    ->hideFromIndex(),
            ]),

            new Panel(__('Contact Information'), [
                Text::make(__('Phone'))
                    ->rules('nullable', 'max:255'),

                Text::make(__('Email'))
                    ->rules('nullable', 'email', 'max:255'),

                Text::make(__('Contact Name'))
                    ->rules('nullable', 'max:255'),

                Text::make(__('Timezone'))
                    ->rules('nullable', 'max:255')
                    ->hideFromIndex(),
            ]),

            new Panel(__('Access Information'), [
                Text::make(__('Access Code'))
                    ->hideFromIndex()
                    ->onlyOnForms(),

                Textarea::make(__('Access Instructions'))
                    ->hideFromIndex()
                    ->rows(3),

                HasMany::make(__('Access Records'), 'accessRecords', SystemLocationAccess::class),
            ]),

            new Panel(__('Additional Information'), [
                Textarea::make(__('Notes'))
                    ->hideFromIndex()
                    ->rows(3),

                Code::make(__('Metadata'))
                    ->json()
                    ->hideFromIndex(),
            ]),

            new Panel(__('Related Items'), [
                Number::make(__('Active Access Records'), 'active_access_count')
                    ->exceptOnForms()
                    ->sortable(),
                Number::make(__('Equipment Count'), 'equipment_count')
                    ->exceptOnForms()
                    ->sortable(),
                Number::make(__('Storage Locations Count'), 'storage_locations_count')
                    ->exceptOnForms()
                    ->sortable(),
                Number::make(__('Events Count'), 'events_count')
                    ->exceptOnForms()
                    ->sortable(),
                HasMany::make(__('Equipment')),
                HasMany::make(__('Storage Locations'), 'storageLocations'),
                HasMany::make(__('Events')),
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
        return [
            new Metrics\WeatherLocationCount(),
            new Metrics\LocationsWithWeatherData(),
            new Metrics\LastWeatherUpdate(),
            new Metrics\NextWeatherForecast(),
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
            new LocationActiveFilter(),
            new LocationCountryFilter(),
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
