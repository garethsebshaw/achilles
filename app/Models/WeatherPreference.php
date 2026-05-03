<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherPreference extends Model
{
    protected $fillable = [
        'user_id',
        'temperature_unit',
        'wind_speed_unit',
        'precipitation_unit',
        'timezone',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
