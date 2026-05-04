<?php

namespace Database\Seeders;

use App\Models\SystemLocation;
use App\Models\SystemModule;
use App\Models\SystemStatus;
use App\Models\User;
use App\Models\WeatherData;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WeatherSystemSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedWeatherPreferences();
        $this->seedWeatherForecasts();
    }

    private function seedWeatherPreferences(): void
    {
        $rows = [];
        $now = now();

        foreach (User::orderBy('id')->limit(500)->pluck('id')->all() as $index => $userId) {
            $rows[] = [
                'user_id' => $userId,
                'temperature_unit' => $index % 3 === 0 ? 'fahrenheit' : 'celsius',
                'wind_speed_unit' => ['mph', 'kmh', 'ms', 'kn'][$index % 4],
                'precipitation_unit' => $index % 4 === 0 ? 'inch' : 'mm',
                'timezone' => $index % 5 === 0 ? 'America/New_York' : null,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('weather_preferences')->insert($rows);
    }

    private function seedWeatherForecasts(): void
    {
        $weatherModuleId = SystemModule::where('model_type', WeatherData::class)->value('id');
        $weatherCodes = array_values(
            SystemStatus::where('system_module_id', $weatherModuleId)
                ->orderBy('sort_order')
                ->pluck('id')
                ->all()
        );

        $hourlyRows = [];
        $dailyRows = [];
        $now = now();

        foreach (SystemLocation::orderBy('id')->get() as $locationIndex => $location) {
            $timezone = $location->timezone ?: 'UTC';
            $baseTime = CarbonImmutable::now($timezone)->startOfHour();

            for ($step = 0; $step <= 24; $step++) {
                $forecastTime = $baseTime->addHours($step * 3);
                $temperature = $this->waveValue(12 + ($locationIndex % 8), 9, $step, 6);
                $precipitation = max(0, $this->waveValue(1.4, 2.1, $step, 4));
                $weatherCodeId = $weatherCodes[($locationIndex + $step) % count($weatherCodes)];

                $hourlyRows[] = [
                    'location_id' => $location->id,
                    'forecast_time' => $forecastTime->utc()->toDateTimeString(),
                    'generated_at' => $baseTime->subMinutes(40)->utc()->toDateTimeString(),
                    'temperature_2m' => $temperature,
                    'apparent_temperature' => $temperature - (($step % 4) * 0.4),
                    'dew_point_2m' => $temperature - 4.5,
                    'pressure_msl' => 1010 + (($locationIndex + $step) % 16),
                    'surface_pressure' => 1004 + (($locationIndex + $step) % 14),
                    'relative_humidity_2m' => 55 + (($locationIndex + ($step * 3)) % 35),
                    'cloud_cover' => 15 + (($locationIndex + ($step * 11)) % 80),
                    'cloud_cover_low' => 10 + (($locationIndex + ($step * 7)) % 50),
                    'cloud_cover_mid' => 8 + (($locationIndex + ($step * 5)) % 45),
                    'cloud_cover_high' => 12 + (($locationIndex + ($step * 9)) % 55),
                    'wind_speed_10m' => max(1.2, $this->waveValue(4.2, 2.4, $step, 3)),
                    'wind_gusts_10m' => max(2.0, $this->waveValue(7.0, 3.1, $step, 5)),
                    'wind_direction_10m' => (($locationIndex * 27) + ($step * 18)) % 360,
                    'shortwave_radiation' => max(0, $this->waveValue(350, 310, $step, 6)),
                    'direct_radiation' => max(0, $this->waveValue(210, 180, $step, 7)),
                    'diffuse_radiation' => max(0, $this->waveValue(120, 90, $step, 8)),
                    'precipitation' => $precipitation,
                    'snowfall' => $temperature < 1 ? round($precipitation * 0.7, 2) : 0,
                    'weather_code' => $weatherCodeId,
                    'vapour_pressure_deficit' => round(0.4 + (($step + $locationIndex) % 9) * 0.08, 3),
                    'et0_fao_evapotranspiration' => round(max(0.1, 0.4 + sin($step / 3)), 2),
                    'sunshine_duration' => max(0, (int) round(1800 + sin($step / 2) * 1200)),
                    'cape' => round(max(0, 80 + sin($step / 4) * 140 + ($locationIndex % 50)), 2),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            for ($day = 0; $day < 14; $day++) {
                $date = $baseTime->addDays($day);
                $maxTemp = $this->waveValue(19 + ($locationIndex % 5), 8, $day, 4);
                $minTemp = $maxTemp - (5 + ($day % 4));

                $dailyRows[] = [
                    'location_id' => $location->id,
                    'date' => $date->toDateString(),
                    'generated_at' => $baseTime->subMinutes(40)->utc()->toDateTimeString(),
                    'temperature_2m_max' => $maxTemp,
                    'temperature_2m_min' => $minTemp,
                    'apparent_temperature_max' => $maxTemp - 0.5,
                    'apparent_temperature_min' => $minTemp - 1.0,
                    'precipitation_sum' => round(max(0, sin(($day + $locationIndex) / 3) * 6), 2),
                    'snowfall_sum' => $maxTemp < 1 ? round(max(0, sin(($day + $locationIndex) / 4) * 4), 2) : 0,
                    'precipitation_hours' => ($day + $locationIndex) % 7,
                    'sunrise' => $date->setTime(6, 15)->utc()->toDateTimeString(),
                    'sunset' => $date->setTime(18, 45)->utc()->toDateTimeString(),
                    'sunshine_duration' => 18000 + (($day + $locationIndex) % 6) * 1200,
                    'daylight_duration' => 43200 + (($day + $locationIndex) % 4) * 900,
                    'wind_speed_10m_max' => round(6.5 + (($day + $locationIndex) % 8) * 0.8, 2),
                    'wind_gusts_10m_max' => round(10.5 + (($day + $locationIndex) % 8) * 1.1, 2),
                    'wind_direction_10m_dominant' => (($day * 24) + ($locationIndex * 15)) % 360,
                    'shortwave_radiation_sum' => round(12.5 + (($day + $locationIndex) % 9) * 1.4, 2),
                    'et0_fao_evapotranspiration' => round(1.2 + (($day + $locationIndex) % 5) * 0.3, 2),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($hourlyRows, 1000) as $chunk) {
            DB::table('weather_data')->insert($chunk);
        }

        foreach (array_chunk($dailyRows, 1000) as $chunk) {
            DB::table('weather_daily_data')->insert($chunk);
        }
    }

    private function waveValue(float $base, float $amplitude, int $step, int $divisor): float
    {
        return round($base + (sin($step / $divisor) * $amplitude), 2);
    }
}
