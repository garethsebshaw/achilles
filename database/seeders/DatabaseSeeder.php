<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Nova\SystemCategory;
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

            WorkoutSessionAndSignupSeeder::class
        ]);
//        $this->command->info(class_basename(static::class) . ' seeded everything successfully!');
    }
}
