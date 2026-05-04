<?php

namespace Database\Seeders;

use Database\Seeders\Certifications\CertificationSeeder;
use Database\Seeders\Certifications\CertificationTypeSeeder;
use Database\Seeders\Certifications\UserCertificateSeeder;
use Database\Seeders\Equipment\EquipmentComponentTypesSeeder;
use Database\Seeders\Equipment\EquipmentConditionsSeeder;
use Database\Seeders\Equipment\EquipmentMaintenancePrioritiesSeeder;
use Database\Seeders\Equipment\EquipmentManufacturersSeeder;
use Database\Seeders\Equipment\EquipmentSystemSeeder;
use Database\Seeders\Events\EventSystemSeeder;
use Database\Seeders\Geography\SystemChapterSeeder;
use Database\Seeders\Geography\SystemCountrySeeder;
use Database\Seeders\Geography\SystemLocationSeeder;
use Database\Seeders\Geography\SystemRegionSeeder;
use Database\Seeders\System\ModuleSurfaceAlignmentSeeder;
use Database\Seeders\System\SystemCategorySeeder;
use Database\Seeders\System\SystemModuleSeeder;
use Database\Seeders\System\SystemOperationsSeeder;
use Database\Seeders\System\SystemStatusSeeder;
use Database\Seeders\Users\LanguageSeeder;
use Database\Seeders\Users\NormalizeSeededUserPasswordsSeeder;
use Database\Seeders\Users\UserRandomSeeder;
use Database\Seeders\Users\UserSeeder;
use Database\Seeders\Users\UserSeederFull;
use Database\Seeders\Weather\WeatherSystemSeeder;
use Database\Seeders\Workouts\WorkoutSessionAndSignupSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,

            SystemModuleSeeder::class,
            SystemStatusSeeder::class,
            ModuleSurfaceAlignmentSeeder::class,
            SystemCountrySeeder::class,
            SystemRegionSeeder::class,
            SystemChapterSeeder::class,

            CertificationTypeSeeder::class,
            CertificationSeeder::class,
            SystemCategorySeeder::class,

            EquipmentManufacturersSeeder::class,
            EquipmentComponentTypesSeeder::class,
            EquipmentConditionsSeeder::class,
            EquipmentMaintenancePrioritiesSeeder::class,

            UserSeederFull::class, // real user full seeder

            // randoms
            UserRandomSeeder::class, // random user seeder
            NormalizeSeededUserPasswordsSeeder::class,

            LanguageSeeder::class, // random language seeder
            UserCertificateSeeder::class, // random user certificate seeder

            SystemLocationSeeder::class,
            EventSystemSeeder::class,
            EquipmentSystemSeeder::class,
            WeatherSystemSeeder::class,
            WorkoutSessionAndSignupSeeder::class,
            SystemOperationsSeeder::class,
        ]);
//        $this->command->info(class_basename(static::class) . ' seeded everything successfully!');
    }
}
