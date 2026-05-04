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
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Color;
use Laravel\Nova\Fields\Number;

use App\Nova\User;

class SystemStatus extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\SystemStatus>
     */
    public static $model = \App\Models\SystemStatus::class;

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
        'name', 'code'
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

            BelongsTo::make(__('System Module'), 'systemModule', SystemModule::class)
                ->filterable()
                ->sortable()
                ->showCreateRelationButton(),

            BelongsTo::make(__('Parent Status'), 'parent', SystemStatus::class)
                ->sortable()
                ->nullable(),

            Text::make(__('Name'))
                ->filterable()
                ->sortable()
                ->rules('required'),

            Text::make(__('Code'))
                ->sortable()
                ->rules('required', 'unique:system_statuses,code,{{resourceId}}'),

            Color::make(__('Color'))->nullable(),

            Number::make(__('Sort Order'))
                ->sortable()
                ->default(0),

            Boolean::make(__('Is Default'))
                ->filterable()
                ->rules('unique_default_per_type'),

            Boolean::make(__('Is System'))
                ->canSee(fn($request) => $request->user()->isAdmin()),

            Code::make(__('Metadata'))
                ->json()
                ->nullable(),

            DateTime::make(__('Created At'))->onlyOnDetail(),
            DateTime::make(__('Updated At'))->onlyOnDetail()
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
