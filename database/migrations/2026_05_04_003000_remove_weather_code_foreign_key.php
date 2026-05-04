<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');

            Schema::create('weather_data_tmp', function (Blueprint $table) {
                $table->id();
                $table->foreignId('location_id')->constrained('system_locations')->onDelete('cascade');
                $table->timestamp('forecast_time')->nullable();
                $table->timestamp('generated_at')->nullable();
                $table->decimal('temperature_2m', 5, 2)->nullable();
                $table->decimal('apparent_temperature', 5, 2)->nullable();
                $table->decimal('dew_point_2m', 5, 2)->nullable();
                $table->decimal('pressure_msl', 6, 2)->nullable();
                $table->decimal('surface_pressure', 6, 2)->nullable();
                $table->decimal('relative_humidity_2m', 5, 2)->nullable();
                $table->decimal('cloud_cover', 5, 2)->nullable();
                $table->decimal('cloud_cover_low', 5, 2)->nullable();
                $table->decimal('cloud_cover_mid', 5, 2)->nullable();
                $table->decimal('cloud_cover_high', 5, 2)->nullable();
                $table->decimal('wind_speed_10m', 5, 2)->nullable();
                $table->decimal('wind_gusts_10m', 5, 2)->nullable();
                $table->decimal('wind_direction_10m', 5, 2)->nullable();
                $table->decimal('shortwave_radiation', 6, 2)->nullable();
                $table->decimal('direct_radiation', 6, 2)->nullable();
                $table->decimal('diffuse_radiation', 6, 2)->nullable();
                $table->decimal('precipitation', 5, 2)->nullable();
                $table->decimal('snowfall', 5, 2)->nullable();
                $table->integer('weather_code')->nullable();
                $table->decimal('vapour_pressure_deficit', 5, 3)->nullable();
                $table->decimal('et0_fao_evapotranspiration', 5, 2)->nullable();
                $table->integer('sunshine_duration')->nullable();
                $table->decimal('cape', 8, 2)->nullable();
                $table->timestamps();

                $table->index('location_id');
                $table->index('forecast_time');
                $table->index(['location_id', 'forecast_time']);
            });

            DB::statement('
                INSERT INTO weather_data_tmp (
                    id, location_id, forecast_time, generated_at,
                    temperature_2m, apparent_temperature, dew_point_2m,
                    pressure_msl, surface_pressure, relative_humidity_2m,
                    cloud_cover, cloud_cover_low, cloud_cover_mid, cloud_cover_high,
                    wind_speed_10m, wind_gusts_10m, wind_direction_10m,
                    shortwave_radiation, direct_radiation, diffuse_radiation,
                    precipitation, snowfall, weather_code,
                    vapour_pressure_deficit, et0_fao_evapotranspiration,
                    sunshine_duration, cape, created_at, updated_at
                )
                SELECT
                    id, location_id, forecast_time, generated_at,
                    temperature_2m, apparent_temperature, dew_point_2m,
                    pressure_msl, surface_pressure, relative_humidity_2m,
                    cloud_cover, cloud_cover_low, cloud_cover_mid, cloud_cover_high,
                    wind_speed_10m, wind_gusts_10m, wind_direction_10m,
                    shortwave_radiation, direct_radiation, diffuse_radiation,
                    precipitation, snowfall, weather_code,
                    vapour_pressure_deficit, et0_fao_evapotranspiration,
                    sunshine_duration, cape, created_at, updated_at
                FROM weather_data
            ');

            Schema::drop('weather_data');
            Schema::rename('weather_data_tmp', 'weather_data');

            DB::statement('PRAGMA foreign_keys=ON');

            return;
        }

        Schema::table('weather_data', function (Blueprint $table) {
            $table->dropForeign(['weather_code']);
            $table->integer('weather_code')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Intentionally one-way. The prior schema was incompatible with live Open-Meteo weather codes.
    }
};
