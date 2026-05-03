<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherDailyData extends Model
{
    protected $fillable = [
        'location_id',
        'date',
        'generated_at',
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
    ];

    protected $casts = [
        'date' => 'date',
        'generated_at' => 'datetime',
        'sunrise' => 'datetime',
        'sunset' => 'datetime',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(SystemLocation::class, 'location_id');
    }
}
