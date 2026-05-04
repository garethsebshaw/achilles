<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class SystemChapter extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\SystemChapter>
     */
    public static $model = \App\Models\SystemChapter::class;

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
        'name', 'city', 'email'
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

            BelongsTo::make(__('Country'), 'country', SystemCountry::class)
                ->sortable()
                ->filterable()
                ->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                    $query->where('active', [1]);}),

            BelongsTo::make(__('Region'), 'region', SystemRegion::class)
                ->sortable()
                ->filterable()
                ->showCreateRelationButton(),

            Text::make(__('Name'))
                ->sortable()
                ->rules('required'),

            Text::make(__('City'))
                ->sortable()
                ->nullable()
                ->hideFromIndex(),

            Text::make(__('State'))
                ->sortable()
                ->nullable()
                ->hideFromIndex(),

            Text::make(__('Postal Code'))
                ->nullable()
                ->hideFromIndex(),

            Text::make(__('Email'))
                ->nullable()
                ->rules('nullable', 'email')
                ->onlyOnIndex()
                ->displayUsing(fn($value) => Str::limit($value, 30)),

            Text::make(__('Email'))
                ->nullable()
                ->hideFromIndex()
                ->rules('nullable', 'email'),

            Text::make(__('Phone'))
                ->nullable(),

            Text::make(__('Website'))
                ->nullable()
                ->rules('nullable', 'url')
                ->hideFromIndex(),

            Code::make(__('Social Media'))
                ->json()
                ->nullable()
                ->hideFromIndex(),

            Boolean::make(__('Is Headquarters'))
                ->sortable()
                ->filterable()
                ->default(false),

            Boolean::make(__('Active'))
                ->sortable()
                ->filterable()
                ->default(true),

            Code::make(__('Metadata'))
                ->json()
                ->nullable()
                ->hideFromIndex(),

            Text::make(__('No. Contacts'), function() {
                return $this->contacts_count;
            })->onlyOnIndex(),

            HasMany::make(__('Contacts'), 'contacts', SystemChapterContact::class),
        ];
    }

    public static function indexQuery(NovaRequest $request, $query) : Builder
    {
        return $query->withCount('contacts');
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
