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

            BelongsTo::make(__('Location'), 'location', SystemLocation::class),

//            BelongsTo::make(__('Weather'), 'weathercode', SystemStatus::class)
//                ->relatableQueryUsing(function (NovaRequest $request, $query) {
//                    return $query->whereHas('module', function ($q) {
//                        $q->where('model_type', 'App\Models\WeatherLocation');
//                    });
//                }),


            DateTime::make(__('Forecast Time')),
            DateTime::make(__('Generated At')),

            new Panel(__('Temperature Data'), [
                Number::make(__('Temperature 2m'), 'temperature_2m')
                    ->step(0.01),
                Number::make(__('Apparent Temperature'), 'apparent_temperature')
                    ->step(0.01),
                Number::make(__('Dew Point 2m'), 'dew_point_2m')
                    ->step(0.01),
            ]),

            new Panel(__('Pressure Data'), [
                Number::make(__('Pressure MSL'), 'pressure_msl')
                    ->step(0.01),
                Number::make(__('Surface Pressure'), 'surface_pressure')
                    ->step(0.01),
            ]),

            new Panel(__('Cloud Cover'), [
                Number::make(__('Cloud Cover'), 'cloud_cover')
                    ->step(0.01),
                Number::make(__('Cloud Cover Low'), 'cloud_cover_low')
                    ->step(0.01),
                Number::make(__('Cloud Cover Mid'), 'cloud_cover_mid')
                    ->step(0.01),
                Number::make(__('Cloud Cover High'), 'cloud_cover_high')
                    ->step(0.01),
            ]),

            new Panel(__('Wind Data'), [
                Number::make(__('Wind Speed 10m'), 'wind_speed_10m')
                    ->step(0.01),
                Number::make(__('Wind Gusts 10m'), 'wind_gusts_10m')
                    ->step(0.01),
                Number::make(__('Wind Direction 10m'), 'wind_direction_10m')
                    ->step(0.01),
            ]),

            new Panel(__('Radiation Data'), [
                Number::make(__('Shortwave Radiation'), 'shortwave_radiation')
                    ->step(0.01),
                Number::make(__('Direct Radiation'), 'direct_radiation')
                    ->step(0.01),
                Number::make(__('Diffuse Radiation'), 'diffuse_radiation')
                    ->step(0.01),
            ]),

            new Panel(__('Precipitation Data'), [
                Number::make(__('Precipitation'), 'precipitation')
                    ->step(0.01),
                Number::make(__('Snowfall'), 'snowfall')
                    ->step(0.01),
            ]),

            new Panel(__('Other Data'), [
//                Number::make(__('Weather Code'), 'weather_code'),
                Number::make(__('Vapour Pressure Deficit'), 'vapour_pressure_deficit')
                    ->step(0.001),
                Number::make(__('ET0 FAO Evapotranspiration'), 'et0_fao_evapotranspiration')
                    ->step(0.01),
                Number::make(__('Sunshine Duration'), 'sunshine_duration'),
                Number::make(__('CAPE'), 'cape')
                    ->step(0.01),
            ]),

            DateTime::make(__('Created At'))
                ->onlyOnDetail(),

            DateTime::make(__('Updated At'))
                ->onlyOnDetail(),
        ];
    }
}
