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

class WeatherDailyData extends Resource
{
    public static $model = \App\Models\WeatherDailyData::class;
    public static $title = 'id';
    public static $search = ['id'];
    public static $group = 'Weather';

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Location', 'location', SystemLocation::class),

            DateTime::make('Date'),
            DateTime::make('Generated At'),

            new Panel('Temperature Data', [
                Number::make('Temperature 2m Max', 'temperature_2m_max')
                    ->step(0.01),
                Number::make('Temperature 2m Min', 'temperature_2m_min')
                    ->step(0.01),
                Number::make('Apparent Temperature Max', 'apparent_temperature_max')
                    ->step(0.01),
                Number::make('Apparent Temperature Min', 'apparent_temperature_min')
                    ->step(0.01),
            ]),

            new Panel('Precipitation Data', [
                Number::make('Precipitation Sum', 'precipitation_sum')
                    ->step(0.01),
                Number::make('Snowfall Sum', 'snowfall_sum')
                    ->step(0.01),
                Number::make('Precipitation Hours', 'precipitation_hours'),
            ]),

            new Panel('Sun Data', [
                DateTime::make('Sunrise'),
                DateTime::make('Sunset'),
                Number::make('Sunshine Duration', 'sunshine_duration'),
                Number::make('Daylight Duration', 'daylight_duration'),
            ]),

            new Panel('Wind Data', [
                Number::make('Wind Speed 10m Max', 'wind_speed_10m_max')
                    ->step(0.01),
                Number::make('Wind Gusts 10m Max', 'wind_gusts_10m_max')
                    ->step(0.01),
                Number::make('Wind Direction 10m Dominant', 'wind_direction_10m_dominant')
                    ->step(0.01),
            ]),

            new Panel('Other Data', [
                Number::make('Shortwave Radiation Sum', 'shortwave_radiation_sum')
                    ->step(0.01),
                Number::make('ET0 FAO Evapotranspiration', 'et0_fao_evapotranspiration')
                    ->step(0.01),
            ]),

            DateTime::make('Created At')
                ->onlyOnDetail(),

            DateTime::make('Updated At')
                ->onlyOnDetail(),
        ];
    }
}
