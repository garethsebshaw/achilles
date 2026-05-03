<?php

namespace App\Nova;

use App\Nova\Metrics\ForecastsPerDay;
use App\Nova\Metrics\LastWeatherUpdate;
use App\Nova\Metrics\LocationsWithWeatherData;
use App\Nova\Metrics\NextWeatherForecast;
use App\Nova\Metrics\TotalForecastEntries;
use App\Nova\Metrics\WeatherLocationCount;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Component;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;

use App\Nova\Actions\ViewWeatherData;
use App\Nova\Fields\WeatherDashboard;

class WeatherLocation extends Resource
{
    public static $model = \App\Models\SystemLocation::class;

    public static $title = 'name';
    public static $search = ['name', 'city', 'state'];
    public static $group = 'Weather';

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable(),

            Text::make('City')
                ->sortable(),

            Text::make('State')
                ->sortable(),

            Number::make('Latitude')
                ->sortable()
                ->step(0.000001),

            Number::make('Longitude')
                ->sortable()
                ->step(0.000001),

            Boolean::make('Active', 'is_active')
                ->sortable(),

            new Panel('Latest Weather Data', [
                WeatherDashboard::make('Weather Dashboard')
                    ->onlyOnDetail(),
            ]),

            new Panel('Location Details', [
                Text::make('Address Line 1'),
                Text::make('Address Line 2'),
                Text::make('Postal Code'),
                Text::make('Timezone'),
                Text::make('Phone'),
                Text::make('Email'),
            ]),
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [
            //new WeatherLocationCount(),
            new LocationsWithWeatherData(),
            new LastWeatherUpdate(),
            new NextWeatherForecast(),
            new TotalForecastEntries(),
            new ForecastsPerDay(),
        ];
    }

    public function actions(NovaRequest $request)
    {
        return [
            new ViewWeatherData,
        ];
    }
}
