<?php

namespace App\Models;

use App\Models\SystemLocation;
use App\Models\SystemStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherData extends Model
{
    protected $fillable = [
        'location_id',
        'forecast_time',
        'generated_at',
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
    ];

    protected $casts = [
        'weather_code' => 'string',
        'forecast_time' => 'datetime',
        'generated_at' => 'datetime',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(SystemLocation::class, 'location_id');
    }

    public function weathercode(): BelongsTo
    {
        return $this->belongsTo(SystemStatus::class, 'weather_code', 'code')
            ->whereRaw('BINARY system_statuses.code = weather_data.weather_code')
            ->withTrashed();
    }

}
