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
    public static $search = ['user', 'Temperature Unit', 'Wind Speed Unit', 'Precipitation Unit'];
    public static $group = 'Weather';

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('User', 'user', \App\Models\User::class),

            Select::make('Temperature Unit')
                ->options([
                    'celsius' => 'Celsius',
                    'fahrenheit' => 'Fahrenheit',
                ])
                ->required()
            ->filterable(),

            Select::make('Wind Speed Unit')
                ->options([
                    'kmh' => 'km/h',
                    'ms' => 'm/s',
                    'mph' => 'mph',
                    'kn' => 'knots',
                ])
                ->required()
                ->filterable(),

            Select::make('Precipitation Unit')
                ->options([
                    'mm' => 'mm',
                    'inch' => 'inches',
                ])
                ->required()
                ->filterable(),

            Text::make('Timezone')
                ->nullable()
                ->filterable(),

            DateTime::make('Created At')
                ->onlyOnDetail(),

            DateTime::make('Updated At')
                ->onlyOnDetail(),
        ];
    }
}
