<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // User preferences for weather display
        Schema::create('weather_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('temperature_unit', ['celsius', 'fahrenheit'])->default('celsius');
            $table->enum('wind_speed_unit', ['kmh', 'ms', 'mph', 'kn'])->default('kmh');
            $table->enum('precipitation_unit', ['mm', 'inch'])->default('mm');
            $table->string('timezone')->nullable(); // If null, use browser/system default
            $table->timestamps();

            $table->unique('user_id');
        });

        // Raw weather data storage
        Schema::create('weather_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('system_locations')->onDelete('cascade');
            $table->timestamp('forecast_time')->nullable(); // The time this weather is for
            $table->timestamp('generated_at')->nullable(); // When this forecast was generated

            // Store everything in standard units - we'll convert on display
            // Temperatures in Celsius
            $table->decimal('temperature_2m', 5, 2)->nullable();
            $table->decimal('apparent_temperature', 5, 2)->nullable();
            $table->decimal('dew_point_2m', 5, 2)->nullable();

            // Pressure in hPa
            $table->decimal('pressure_msl', 6, 2)->nullable();
            $table->decimal('surface_pressure', 6, 2)->nullable();

            // Percentages
            $table->decimal('relative_humidity_2m', 5, 2)->nullable();
            $table->decimal('cloud_cover', 5, 2)->nullable();
            $table->decimal('cloud_cover_low', 5, 2)->nullable();
            $table->decimal('cloud_cover_mid', 5, 2)->nullable();
            $table->decimal('cloud_cover_high', 5, 2)->nullable();

            // Wind speeds in m/s (we'll convert to other units on display)
            $table->decimal('wind_speed_10m', 5, 2)->nullable();
            $table->decimal('wind_gusts_10m', 5, 2)->nullable();
            $table->decimal('wind_direction_10m', 5, 2)->nullable();

            // Radiation in W/m²
            $table->decimal('shortwave_radiation', 6, 2)->nullable();
            $table->decimal('direct_radiation', 6, 2)->nullable();
            $table->decimal('diffuse_radiation', 6, 2)->nullable();

            // Precipitation in mm
            $table->decimal('precipitation', 5, 2)->nullable();
            $table->decimal('snowfall', 5, 2)->nullable();

            // Other
            $table->foreignId('weather_code')->constrained('system_statuses')->onDelete('cascade');
            $table->decimal('vapour_pressure_deficit', 5, 3)->nullable(); // kPa
            $table->decimal('et0_fao_evapotranspiration', 5, 2)->nullable(); // mm
            $table->integer('sunshine_duration')->nullable(); // seconds
            $table->decimal('cape', 8, 2)->nullable(); // J/kg

            // Indexes for common queries
            $table->index('location_id');
            $table->index('forecast_time');
            $table->index(['location_id', 'forecast_time']);

            $table->timestamps();
        });

        // Daily aggregations
        Schema::create('weather_daily_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('system_locations')->onDelete('cascade');
            $table->date('date')->nullable();
            $table->timestamp('generated_at')->nullable();

            // Daily min/max temperatures in Celsius
            $table->decimal('temperature_2m_max', 5, 2)->nullable();
            $table->decimal('temperature_2m_min', 5, 2)->nullable();
            $table->decimal('apparent_temperature_max', 5, 2)->nullable();
            $table->decimal('apparent_temperature_min', 5, 2)->nullable();

            // Precipitation in mm
            $table->decimal('precipitation_sum', 5, 2)->nullable();
            $table->decimal('snowfall_sum', 5, 2)->nullable();
            $table->integer('precipitation_hours')->nullable();

            // Sun data
            $table->timestamp('sunrise')->nullable();
            $table->timestamp('sunset')->nullable();
            $table->integer('sunshine_duration')->nullable(); // seconds
            $table->integer('daylight_duration')->nullable(); // seconds

            // Wind data
            $table->decimal('wind_speed_10m_max', 5, 2)->nullable(); // m/s
            $table->decimal('wind_gusts_10m_max', 5, 2)->nullable(); // m/s
            $table->decimal('wind_direction_10m_dominant', 5, 2)->nullable();

            // Other
            $table->decimal('shortwave_radiation_sum', 8, 2)->nullable(); // MJ/m²
            $table->decimal('et0_fao_evapotranspiration', 5, 2)->nullable(); // mm

            // Indexes
            $table->index('location_id');
            $table->index('date');
            $table->index(['location_id', 'date']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weather_daily_data');
        Schema::dropIfExists('weather_data');
        Schema::dropIfExists('weather_preferences');
    }
};
