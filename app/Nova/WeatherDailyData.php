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

            BelongsTo::make(__('Location'), 'location', SystemLocation::class),

            DateTime::make(__('Date')),
            DateTime::make(__('Generated At')),

            new Panel(__('Temperature Data'), [
                Number::make(__('Temperature 2m Max'), 'temperature_2m_max')
                    ->step(0.01),
                Number::make(__('Temperature 2m Min'), 'temperature_2m_min')
                    ->step(0.01),
                Number::make(__('Apparent Temperature Max'), 'apparent_temperature_max')
                    ->step(0.01),
                Number::make(__('Apparent Temperature Min'), 'apparent_temperature_min')
                    ->step(0.01),
            ]),

            new Panel(__('Precipitation Data'), [
                Number::make(__('Precipitation Sum'), 'precipitation_sum')
                    ->step(0.01),
                Number::make(__('Snowfall Sum'), 'snowfall_sum')
                    ->step(0.01),
                Number::make(__('Precipitation Hours'), 'precipitation_hours'),
            ]),

            new Panel(__('Sun Data'), [
                DateTime::make(__('Sunrise')),
                DateTime::make(__('Sunset')),
                Number::make(__('Sunshine Duration'), 'sunshine_duration'),
                Number::make(__('Daylight Duration'), 'daylight_duration'),
            ]),

            new Panel(__('Wind Data'), [
                Number::make(__('Wind Speed 10m Max'), 'wind_speed_10m_max')
                    ->step(0.01),
                Number::make(__('Wind Gusts 10m Max'), 'wind_gusts_10m_max')
                    ->step(0.01),
                Number::make(__('Wind Direction 10m Dominant'), 'wind_direction_10m_dominant')
                    ->step(0.01),
            ]),

            new Panel(__('Other Data'), [
                Number::make(__('Shortwave Radiation Sum'), 'shortwave_radiation_sum')
                    ->step(0.01),
                Number::make(__('ET0 FAO Evapotranspiration'), 'et0_fao_evapotranspiration')
                    ->step(0.01),
            ]),

            DateTime::make(__('Created At'))
                ->onlyOnDetail(),

            DateTime::make(__('Updated At'))
                ->onlyOnDetail(),
        ];
    }
}
