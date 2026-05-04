<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Nova\Metrics\TotalLanguageProficiencies;
use App\Nova\Filters\LanguageProficiencyFilter;
use Illuminate\Database\Eloquent\Builder;

class LanguageProficiency extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\LanguageProficiency>
     */
    public static $model = \App\Models\LanguageProficiency::class;

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
        'id',
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

            BelongsTo::make(__('User'), 'user', User::class)
                ->searchable()
                ->filterable()
                ->sortable()
                ->required(),

            BelongsTo::make(__('Language'), 'language', Language::class)
                ->filterable()
                ->sortable()
                ->required(),

            BelongsTo::make(__('Proficiency Level'), 'proficiencyStatus', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                    return $query->forModelType(\App\Models\LanguageProficiency::class);
                })
                ->filterable()
                ->sortable()
                ->required(),
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
            new Cards\TotalLanguageProficiencies()
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
            new Filters\LanguageProficiencyFilter()
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

    public static function label()
    {
        return __('Language Proficiencies');
    }

    public static function singularLabel()
    {
        return __('Language Proficiency');
    }
}
