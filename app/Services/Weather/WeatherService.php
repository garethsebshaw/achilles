<?php

namespace App\Services\Weather;

use App\Models\SystemLocation;
use App\Models\WeatherData;
use App\Models\WeatherDailyData;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Exception;

class WeatherService
{
    private const BASE_URL = 'https://api.open-meteo.com/v1/forecast';

    /**
     * Fetch weather data for all active locations
     */
    public function fetchWeatherDataForAllLocations(): void
    {
        $locations = SystemLocation::query()
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        foreach ($locations as $location) {
            try {
                $this->fetchWeatherDataForLocation($location);
            } catch (Exception $e) {
                Log::error('Error fetching weather data for location', [
                    'location_id' => $location->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Fetch weather data for a specific location
     */
    public function fetchWeatherDataForLocation(SystemLocation $location): void
    {
        $response = Http::get(self::BASE_URL, [
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'hourly' => $this->getHourlyParameters(),
            'daily' => $this->getDailyParameters(),
            'timezone' => $location->timezone ?? 'GMT',
            'forecast_days' => 4,
        ]);

        if ($response->failed()) {
            throw new Exception('Failed to fetch weather data: ' . $response->body());
        }

        $data = $response->json();

        $this->processHourlyData($location, $data);
        $this->processDailyData($location, $data);
    }

    /**
     * Get the list of hourly parameters we want to fetch
     */
    private function getHourlyParameters(): string
    {
        return implode(',', [
            'temperature_2m',
            'apparent_temperature',
            'dew_point_2m',
            'pressure_msl',
            'surface_pressure',
            'relative_humidity_2m',
            'cloud_cover',
            'cloud_cover_low',
            'cloud_cover_mid',
            'cloud_cover_high',
            'wind_speed_10m',
            'wind_gusts_10m',
            'wind_direction_10m',
            'shortwave_radiation',
            'direct_radiation',
            'diffuse_radiation',
            'precipitation',
            'snowfall',
            'weather_code',
            'vapour_pressure_deficit',
            'et0_fao_evapotranspiration',
            'sunshine_duration',
            'cape',
        ]);
    }

    /**
     * Get the list of daily parameters we want to fetch
     */
    private function getDailyParameters(): string
    {
        return implode(',', [
            'temperature_2m_max',
            'temperature_2m_min',
            'apparent_temperature_max',
            'apparent_temperature_min',
            'precipitation_sum',
            'snowfall_sum',
            'precipitation_hours',
            'sunrise',
            'sunset',
            'sunshine_duration',
            'daylight_duration',
            'wind_speed_10m_max',
            'wind_gusts_10m_max',
            'wind_direction_10m_dominant',
            'shortwave_radiation_sum',
            'et0_fao_evapotranspiration',
        ]);
    }

    /**
     * Process and store hourly weather data
     */
    private function processHourlyData(SystemLocation $location, array $data): void
    {
        $hourly = $data['hourly'];
        $generatedAt = now();

        foreach ($hourly['time'] as $index => $time) {
            WeatherData::updateOrCreate([
                'location_id' => $location->id,
                'forecast_time' => $time,
            ], [
                'location_id' => $location->id,
                'forecast_time' => $time,
                'generated_at' => $generatedAt,
                'temperature_2m' => $hourly['temperature_2m'][$index] ?? null,
                'apparent_temperature' => $hourly['apparent_temperature'][$index] ?? null,
                'dew_point_2m' => $hourly['dew_point_2m'][$index] ?? null,
                'pressure_msl' => $hourly['pressure_msl'][$index] ?? null,
                'surface_pressure' => $hourly['surface_pressure'][$index] ?? null,
                'relative_humidity_2m' => $hourly['relative_humidity_2m'][$index] ?? null,
                'cloud_cover' => $hourly['cloud_cover'][$index] ?? null,
                'cloud_cover_low' => $hourly['cloud_cover_low'][$index] ?? null,
                'cloud_cover_mid' => $hourly['cloud_cover_mid'][$index] ?? null,
                'cloud_cover_high' => $hourly['cloud_cover_high'][$index] ?? null,
                'wind_speed_10m' => $hourly['wind_speed_10m'][$index] ?? null,
                'wind_gusts_10m' => $hourly['wind_gusts_10m'][$index] ?? null,
                'wind_direction_10m' => $hourly['wind_direction_10m'][$index] ?? null,
                'shortwave_radiation' => $hourly['shortwave_radiation'][$index] ?? null,
                'direct_radiation' => $hourly['direct_radiation'][$index] ?? null,
                'diffuse_radiation' => $hourly['diffuse_radiation'][$index] ?? null,
                'precipitation' => $hourly['precipitation'][$index] ?? null,
                'snowfall' => $hourly['snowfall'][$index] ?? null,
                'weather_code' => $hourly['weather_code'][$index] ?? null,
                'vapour_pressure_deficit' => $hourly['vapour_pressure_deficit'][$index] ?? null,
                'et0_fao_evapotranspiration' => $hourly['et0_fao_evapotranspiration'][$index] ?? null,
                'sunshine_duration' => $hourly['sunshine_duration'][$index] ?? null,
                'cape' => $hourly['cape'][$index] ?? null,
            ]);
        }
    }

    /**
     * Process and store daily weather data
     */
    private function processDailyData(SystemLocation $location, array $data): void
    {
        $daily = $data['daily'];
        $generatedAt = now();

        foreach ($daily['time'] as $index => $date) {
            WeatherDailyData::updateOrCreate([
                'location_id' => $location->id,
                'date' => $date,
            ], [
                'location_id' => $location->id,
                'date' => $date,
                'generated_at' => $generatedAt,
                'temperature_2m_max' => $daily['temperature_2m_max'][$index] ?? null,
                'temperature_2m_min' => $daily['temperature_2m_min'][$index] ?? null,
                'apparent_temperature_max' => $daily['apparent_temperature_max'][$index] ?? null,
                'apparent_temperature_min' => $daily['apparent_temperature_min'][$index] ?? null,
                'precipitation_sum' => $daily['precipitation_sum'][$index] ?? null,
                'snowfall_sum' => $daily['snowfall_sum'][$index] ?? null,
                'precipitation_hours' => $daily['precipitation_hours'][$index] ?? null,
                'sunrise' => $daily['sunrise'][$index] ?? null,
                'sunset' => $daily['sunset'][$index] ?? null,
                'sunshine_duration' => $daily['sunshine_duration'][$index] ?? null,
                'daylight_duration' => $daily['daylight_duration'][$index] ?? null,
                'wind_speed_10m_max' => $daily['wind_speed_10m_max'][$index] ?? null,
                'wind_gusts_10m_max' => $daily['wind_gusts_10m_max'][$index] ?? null,
                'wind_direction_10m_dominant' => $daily['wind_direction_10m_dominant'][$index] ?? null,
                'shortwave_radiation_sum' => $daily['shortwave_radiation_sum'][$index] ?? null,
                'et0_fao_evapotranspiration' => $daily['et0_fao_evapotranspiration'][$index] ?? null,
            ]);
        }
    }
}
