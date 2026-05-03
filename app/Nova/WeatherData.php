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

class WeatherData extends Resource
{
    public static $model = \App\Models\WeatherData::class;
    public static $title = 'id';
    public static $search = ['id'];
    public static $group = 'Weather';

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Location', 'location', SystemLocation::class),

//            BelongsTo::make('Weather', 'weathercode', SystemStatus::class)
//                ->relatableQueryUsing(function (NovaRequest $request, $query) {
//                    return $query->whereHas('module', function ($q) {
//                        $q->where('model_type', 'App\Models\WeatherLocation');
//                    });
//                }),


            DateTime::make('Forecast Time'),
            DateTime::make('Generated At'),

            new Panel('Temperature Data', [
                Number::make('Temperature 2m', 'temperature_2m')
                    ->step(0.01),
                Number::make('Apparent Temperature', 'apparent_temperature')
                    ->step(0.01),
                Number::make('Dew Point 2m', 'dew_point_2m')
                    ->step(0.01),
            ]),

            new Panel('Pressure Data', [
                Number::make('Pressure MSL', 'pressure_msl')
                    ->step(0.01),
                Number::make('Surface Pressure', 'surface_pressure')
                    ->step(0.01),
            ]),

            new Panel('Cloud Cover', [
                Number::make('Cloud Cover', 'cloud_cover')
                    ->step(0.01),
                Number::make('Cloud Cover Low', 'cloud_cover_low')
                    ->step(0.01),
                Number::make('Cloud Cover Mid', 'cloud_cover_mid')
                    ->step(0.01),
                Number::make('Cloud Cover High', 'cloud_cover_high')
                    ->step(0.01),
            ]),

            new Panel('Wind Data', [
                Number::make('Wind Speed 10m', 'wind_speed_10m')
                    ->step(0.01),
                Number::make('Wind Gusts 10m', 'wind_gusts_10m')
                    ->step(0.01),
                Number::make('Wind Direction 10m', 'wind_direction_10m')
                    ->step(0.01),
            ]),

            new Panel('Radiation Data', [
                Number::make('Shortwave Radiation', 'shortwave_radiation')
                    ->step(0.01),
                Number::make('Direct Radiation', 'direct_radiation')
                    ->step(0.01),
                Number::make('Diffuse Radiation', 'diffuse_radiation')
                    ->step(0.01),
            ]),

            new Panel('Precipitation Data', [
                Number::make('Precipitation', 'precipitation')
                    ->step(0.01),
                Number::make('Snowfall', 'snowfall')
                    ->step(0.01),
            ]),

            new Panel('Other Data', [
//                Number::make('Weather Code', 'weather_code'),
                Number::make('Vapour Pressure Deficit', 'vapour_pressure_deficit')
                    ->step(0.001),
                Number::make('ET0 FAO Evapotranspiration', 'et0_fao_evapotranspiration')
                    ->step(0.01),
                Number::make('Sunshine Duration', 'sunshine_duration'),
                Number::make('CAPE', 'cape')
                    ->step(0.01),
            ]),

            DateTime::make('Created At')
                ->onlyOnDetail(),

            DateTime::make('Updated At')
                ->onlyOnDetail(),
        ];
    }
}
