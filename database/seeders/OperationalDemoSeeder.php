<?php

namespace Database\Seeders;

use Database\Seeders\Equipment\EquipmentSystemSeeder;
use Database\Seeders\Events\EventSystemSeeder;
use Database\Seeders\Geography\SystemLocationSeeder;
use Database\Seeders\System\SystemOperationsSeeder;
use Database\Seeders\Weather\WeatherSystemSeeder;
use Database\Seeders\Workouts\WorkoutSessionAndSignupSeeder;
use Illuminate\Database\Seeder;

class OperationalDemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SystemLocationSeeder::class,
            EventSystemSeeder::class,
            EquipmentSystemSeeder::class,
            WeatherSystemSeeder::class,
            WorkoutSessionAndSignupSeeder::class,
            SystemOperationsSeeder::class,
        ]);
    }
}
