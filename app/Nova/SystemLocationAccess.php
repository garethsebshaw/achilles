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

class SystemLocationAccess extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\SystemLocationAccess>
     */
    public static $model = \App\Models\SystemLocationAccess::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'access_identifier';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'access_identifier', 'notes'
    ];

    public static $group = 'System Management';

    public static $displayInNavigation = false;

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make(__('Location'), 'location', SystemLocation::class)
                ->rules('required'),

            BelongsTo::make(__('User'))
                ->rules('required'),

            Select::make(__('Access Type'))
                ->options([
                    'key' => __('Key'),
                    'code' => __('Access Code'),
                    'card' => __('Access Card'),
                    'fob' => __('Key Fob'),
                    'other' => __('Other')
                ])
                ->rules('required'),

            Text::make(__('Access Identifier'))
                ->help(__('Key number, card number, etc.'))
                ->nullable(),

            Date::make(__('Access Granted Date'))
                ->rules('required')
                ->default(now()),

            Date::make(__('Access Expiry Date'))
                ->nullable(),

            BelongsTo::make(__('Granted By'), 'grantedBy', User::class)
                ->rules('required'),

            Boolean::make(__('Active'), 'is_active')
                ->default(true),

            Textarea::make(__('Notes'))
                ->rows(3)
                ->nullable(),
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
