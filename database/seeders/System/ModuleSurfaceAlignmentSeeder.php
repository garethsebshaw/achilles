<?php

namespace Database\Seeders\System;

use App\Models\MaintenanceRequest;
use App\Models\MeetingPoint;
use App\Models\SystemModule;
use App\Models\SystemNotification;
use App\Models\SystemStatus;
use App\Models\TandemBikePairing;
use App\Models\TandemBikePairingCheck;
use App\Models\WeatherDailyData;
use App\Models\WeatherData;
use App\Models\WeatherPreference;
use App\Models\WorkoutSessionMeetingPoint;
use App\Models\WorkoutSpecificDetails;
use Illuminate\Database\Seeder;

class ModuleSurfaceAlignmentSeeder extends Seeder
{
    public function run(): void
    {
        $this->alignImplementedModules();
        $this->deprecateLegacyModules();
        $this->seedImplementedStatuses();
    }

    private function alignImplementedModules(): void
    {
        $definitions = [
            [
                'name' => 'Notifications',
                'model_type' => SystemNotification::class,
                'description' => 'User notifications and alerts stored in the system notification feed.',
                'active' => true,
            ],
            [
                'name' => 'Meeting Points',
                'model_type' => MeetingPoint::class,
                'description' => 'Sport-specific workout meeting points for chapters and sessions.',
                'active' => true,
            ],
            [
                'name' => 'Workout Session Meeting Points',
                'model_type' => WorkoutSessionMeetingPoint::class,
                'description' => 'Session-specific meeting point assignments.',
                'active' => true,
            ],
            [
                'name' => 'Weather Preferences',
                'model_type' => WeatherPreference::class,
                'description' => 'User weather display preferences.',
                'active' => true,
            ],
            [
                'name' => 'Weather Forecast Data',
                'model_type' => WeatherData::class,
                'description' => 'Hourly and near-term weather forecast records by location.',
                'active' => true,
            ],
            [
                'name' => 'Weather Daily Data',
                'model_type' => WeatherDailyData::class,
                'description' => 'Daily weather forecast summaries by location.',
                'active' => true,
            ],
            [
                'name' => 'Tandem Bike Pairings',
                'model_type' => TandemBikePairing::class,
                'description' => 'Guide, athlete, and tandem-bike compatibility pairings.',
                'active' => true,
            ],
            [
                'name' => 'Tandem Bike Pairing Checks',
                'model_type' => TandemBikePairingCheck::class,
                'description' => 'Operational checks for tandem-bike pairings.',
                'active' => true,
            ],
            [
                'name' => 'Maintenance Requests',
                'model_type' => MaintenanceRequest::class,
                'description' => 'Track and manage maintenance requests.',
                'active' => true,
            ],
        ];

        foreach ($definitions as $definition) {
            SystemModule::updateOrCreate(
                ['model_type' => $definition['model_type']],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                    'active' => $definition['active'],
                ]
            );
        }
    }

    private function deprecateLegacyModules(): void
    {
        $legacyModules = [
            'App\Models\WorkoutLocation',
            'App\Models\WorkoutMeetingPoint',
            'App\Models\WorkoutWeather',
            'App\Models\WeatherLocation',
            'App\Models\Notification',
        ];

        SystemModule::whereIn('model_type', $legacyModules)->update(['active' => false]);
    }

    private function seedImplementedStatuses(): void
    {
        $statusSets = [
            WorkoutSpecificDetails::class => [
                ['name' => 'Miles', 'code' => 'unit_miles', 'color' => '#0d6efd', 'sort_order' => 10, 'metadata' => ['unit_type' => 'distance', 'symbol' => 'mi']],
                ['name' => 'Kilometers', 'code' => 'unit_kilometers', 'color' => '#20c997', 'sort_order' => 20, 'metadata' => ['unit_type' => 'distance', 'symbol' => 'km']],
                ['name' => 'Meters', 'code' => 'unit_meters', 'color' => '#6f42c1', 'sort_order' => 30, 'metadata' => ['unit_type' => 'distance', 'symbol' => 'm']],
                ['name' => 'Minutes per Mile', 'code' => 'pace_min_mile', 'color' => '#6610f2', 'sort_order' => 40, 'metadata' => ['unit_type' => 'pace', 'symbol' => 'min/mi']],
                ['name' => 'Minutes per Kilometer', 'code' => 'pace_min_km', 'color' => '#17a2b8', 'sort_order' => 50, 'metadata' => ['unit_type' => 'pace', 'symbol' => 'min/km']],
                ['name' => 'Miles per Hour', 'code' => 'speed_mph', 'color' => '#fd7e14', 'sort_order' => 60, 'metadata' => ['unit_type' => 'speed', 'symbol' => 'mph']],
                ['name' => 'Kilometers per Hour', 'code' => 'speed_kmh', 'color' => '#198754', 'sort_order' => 70, 'metadata' => ['unit_type' => 'speed', 'symbol' => 'km/h']],
                ['name' => 'Meters per Second', 'code' => 'speed_ms', 'color' => '#dc3545', 'sort_order' => 80, 'metadata' => ['unit_type' => 'speed', 'symbol' => 'm/s']],
            ],
            MaintenanceRequest::class => [
                ['name' => 'Reported', 'code' => 'maintreq_reported', 'color' => '#ffc107', 'sort_order' => 10],
                ['name' => 'Assigned', 'code' => 'maintreq_assigned', 'color' => '#0d6efd', 'sort_order' => 20],
                ['name' => 'In Progress', 'code' => 'maintreq_progress', 'color' => '#17a2b8', 'sort_order' => 30],
                ['name' => 'Waiting for Parts', 'code' => 'maintreq_parts', 'color' => '#fd7e14', 'sort_order' => 40],
                ['name' => 'Completed', 'code' => 'maintreq_completed', 'color' => '#198754', 'sort_order' => 50],
                ['name' => 'Cancelled', 'code' => 'maintreq_cancelled', 'color' => '#6c757d', 'sort_order' => 60],
            ],
            WeatherData::class => [
                ['name' => 'Clear', 'code' => 'weather_clear', 'color' => '#ffc107', 'sort_order' => 10],
                ['name' => 'Partly Cloudy', 'code' => 'weather_partly_cloudy', 'color' => '#17a2b8', 'sort_order' => 20],
                ['name' => 'Cloudy', 'code' => 'weather_cloudy', 'color' => '#6c757d', 'sort_order' => 30],
                ['name' => 'Rain', 'code' => 'weather_rain', 'color' => '#0d6efd', 'sort_order' => 40],
                ['name' => 'Heavy Rain', 'code' => 'weather_heavy_rain', 'color' => '#0b5ed7', 'sort_order' => 50],
                ['name' => 'Snow', 'code' => 'weather_snow', 'color' => '#adb5bd', 'sort_order' => 60],
                ['name' => 'Wind Advisory', 'code' => 'weather_windy', 'color' => '#fd7e14', 'sort_order' => 70],
                ['name' => 'Storm', 'code' => 'weather_storm', 'color' => '#dc3545', 'sort_order' => 80],
            ],
        ];

        foreach ($statusSets as $modelType => $statuses) {
            $module = SystemModule::where('model_type', $modelType)->first();

            if (! $module) {
                continue;
            }

            foreach ($statuses as $index => $status) {
                SystemStatus::updateOrCreate(
                    [
                        'system_module_id' => $module->id,
                        'code' => $status['code'],
                    ],
                    [
                        'name' => $status['name'],
                        'color' => $status['color'],
                        'sort_order' => $status['sort_order'],
                        'is_default' => $index === 0,
                        'is_system' => true,
                        'metadata' => $status['metadata'] ?? null,
                    ]
                );
            }
        }
    }
}
