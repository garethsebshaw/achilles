<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

use App\Nova\Metrics\TotalLanguages;
use App\Nova\Filters\ActiveLanguage;

class Language extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Language>
     */
    public static $model = \App\Models\Language::class;

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
        'name', 'iso_code'
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
                ->rules('required', 'unique:languages,name')
                ->sortable(),

            Text::make(__('ISO Code'))
                ->nullable()
                ->sortable(),

            BelongsTo::make(__('Category'), 'category', \App\Nova\SystemCategory::class)
                ->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                    $query->whereHas('systemModule', function($q) {
                        $q->where('model_type', \App\Models\Language::class);
                    });
                })
                ->required(),

            Boolean::make(__('Active'))
                ->filterable()
                ->default(true),

            HasMany::make(__('Proficiencies'), 'proficiencies', LanguageProficiency::class)
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
            (new Metrics\TotalLanguages()),
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
            new Filters\ActiveLanguage(),
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
