<?php

namespace App\Console\Commands;

use App\Models\SystemLocation;
use App\Services\Weather\WeatherService;
use Illuminate\Console\Command;

class FetchWeatherData extends Command
{
    protected $signature = 'weather:fetch {locationId?}';
    protected $description = 'Fetch weather data for all active locations';

    public function handle(WeatherService $weatherService): void
    {
        $this->info('Starting weather data fetch...');

        try {
            $locationId = $this->argument('locationId');

            if ($locationId) {
                $weatherService->fetchWeatherDataForLocation(
                    SystemLocation::query()->findOrFail($locationId)
                );
            } else {
                $weatherService->fetchWeatherDataForAllLocations();
            }

            $this->info('Weather data fetch completed successfully.');
        } catch (\Exception $e) {
            $this->error('Error fetching weather data: ' . $e->getMessage());
        }
    }
}
