<?php

namespace App\Console\Commands;

use App\Services\Weather\WeatherService;
use Illuminate\Console\Command;

class FetchWeatherData extends Command
{
    protected $signature = 'weather:fetch';
    protected $description = 'Fetch weather data for all active locations';

    public function handle(WeatherService $weatherService): void
    {
        $this->info('Starting weather data fetch...');

        try {
            $weatherService->fetchWeatherDataForAllLocations();
            $this->info('Weather data fetch completed successfully.');
        } catch (\Exception $e) {
            $this->error('Error fetching weather data: ' . $e->getMessage());
        }
    }
}
