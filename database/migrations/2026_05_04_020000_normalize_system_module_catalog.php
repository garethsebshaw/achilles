<?php

use App\Models\SystemModule;
use Database\Seeders\System\SystemModuleSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        (new SystemModuleSeeder())->run();
    }

    public function down(): void
    {
        $modelTypes = [
            'App\Models\MeetingPoint',
            'App\Models\WorkoutSessionMeetingPoint',
            'App\Models\WeatherPreference',
            'App\Models\WeatherData',
            'App\Models\WeatherDailyData',
            'App\Models\TandemBikePairingCheck',
        ];

        SystemModule::query()
            ->whereIn('model_type', $modelTypes)
            ->delete();
    }
};
