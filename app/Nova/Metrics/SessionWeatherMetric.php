<?php

namespace App\Nova\Metrics;

use App\Models\WeatherData;
use App\Models\WorkoutSession;
use App\Support\Attendance\CheckInSessionContext;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class SessionWeatherMetric extends Value
{
    public function calculate(NovaRequest $request)
    {
        $sessionId = $this->meta['workout_session_id']
            ?? $request->get('workout_session_id')
            ?? app(CheckInSessionContext::class)->currentSessionId();

        if (! $sessionId) {
            return $this->result(__('No weather context'));
        }

        $session = WorkoutSession::query()->with('location')->find($sessionId);

        if (! $session || ! $session->location_id) {
            return $this->result(__('No weather context'));
        }

        $weather = WeatherData::query()
            ->where('location_id', $session->location_id)
            ->where('forecast_time', '>=', now()->subHour())
            ->orderByRaw('ABS(strftime(\'%s\', forecast_time) - strftime(\'%s\', CURRENT_TIMESTAMP))')
            ->first();

        if (! $weather) {
            return $this->result(__('No weather data'));
        }

        $summary = collect([
            $weather->cloud_cover !== null ? __('Clouds :value%', ['value' => (int) round($weather->cloud_cover)]) : null,
            $weather->precipitation !== null ? __('Rain :value mm', ['value' => round((float) $weather->precipitation, 1)]) : null,
            $weather->wind_speed_10m !== null ? __('Wind :value km/h', ['value' => round((float) $weather->wind_speed_10m, 1)]) : null,
            $weather->relative_humidity_2m !== null ? __('Humidity :value%', ['value' => (int) round($weather->relative_humidity_2m)]) : null,
            $weather->generated_at ? __('Updated :time', ['time' => $weather->generated_at->diffForHumans()]) : null,
        ])->filter()->implode(' · ');

        return $this->result(
            $weather->temperature_2m !== null
                ? round((float) $weather->temperature_2m, 1).'°C'
                : __('N/A')
        )->suffix($summary);
    }

    public function name()
    {
        return __('Session Weather');
    }
}
