<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\MorphTo;

class SystemNotification extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\SystemNotification>
     */
    public static $model = \App\Models\SystemNotification::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'type';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
        'type',
    ];

    public static $group = 'System';

    public function title()
    {
        return $this->resource->title;
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

            Text::make(__('Title'), fn () => $this->resource->title)
                ->sortable()
                ->exceptOnForms(),

            Text::make(__('Type'))
                ->sortable()
                ->displayUsing(fn (?string $value) => $value ? class_basename($value) : null),

            MorphTo::make(__('Notifiable'))
                ->types([
                    User::class,
                ]),

            Boolean::make(__('Read'), fn () => $this->read_at !== null)
                ->exceptOnForms(),

            Code::make(__('Data'))
                ->json()
                ->rules('required'),

            DateTime::make(__('Read At'))
                ->sortable()
                ->nullable(),

            DateTime::make(__('Created At'))->onlyOnDetail(),
            DateTime::make(__('Updated At'))->onlyOnDetail(),
            DateTime::make(__('Deleted At'))->onlyOnDetail(),
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

    public static function label()
    {
        return __('System Notifications');
    }

    public static function singularLabel()
    {
        return __('System Notification');
    }
}
