<?php

namespace App\Nova;

use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class WeatherPreference extends Resource
{
    public static $model = \App\Models\WeatherPreference::class;
    public static $title = 'id';
    public static $search = ['temperature_unit', 'wind_speed_unit', 'precipitation_unit', 'timezone'];
    public static $group = 'Weather';

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make(__('User'), 'user', User::class),

            Select::make(__('Temperature Unit'))
                ->options([
                    'celsius' => __('Celsius'),
                    'fahrenheit' => __('Fahrenheit'),
                ])
                ->required()
            ->filterable(),

            Select::make(__('Wind Speed Unit'))
                ->options([
                    'kmh' => 'km/h',
                    'ms' => 'm/s',
                    'mph' => 'mph',
                    'kn' => 'knots',
                ])
                ->required()
                ->filterable(),

            Select::make(__('Precipitation Unit'))
                ->options([
                    'mm' => 'mm',
                    'inch' => 'inches',
                ])
                ->required()
                ->filterable(),

            Text::make(__('Timezone'))
                ->nullable()
                ->filterable(),

            DateTime::make(__('Created At'))
                ->onlyOnDetail(),

            DateTime::make(__('Updated At'))
                ->onlyOnDetail(),
        ];
    }
}
