<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Builder;
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

class ComponentType extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\ComponentType>
     */
    public static $model = \App\Models\ComponentType::class;

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
        'name', 'description'
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

            BelongsTo::make(__('Category'), 'category', SystemCategory::class)
                ->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                    $query->whereHas('systemModule', function($q) {
                        $q->where('model_type', \App\Models\ComponentType::class);
                    });
                })
                ->filterable()
                ->sortable()
                ->required(),

            Text::make(__('Name'))
                ->rules('required', 'max:255')
                ->sortable(),

            Textarea::make(__('Description'))
                ->nullable()
                ->rows(3),

            Number::make(__('Maintenance (Miles)'), 'default_maintenance_interval_miles')
                ->nullable()
                ->filterable()
                ->min(0),

            Number::make(__('Maintenance (Months)'), 'default_maintenance_interval_months')
                ->nullable()
                ->filterable()
                ->min(0),

            Code::make(__('Attributes'))
                ->json()
                ->nullable(),

            Panel::make(__('Relationships'), [
                HasMany::make(__('Components'), 'components', EquipmentComponent::class),
                HasMany::make(__('Compatible With'), 'compatibleWith', ComponentType::class),
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
