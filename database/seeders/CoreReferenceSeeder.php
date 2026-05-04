<?php

namespace Database\Seeders;

use Database\Seeders\Certifications\CertificationSeeder;
use Database\Seeders\Certifications\CertificationTypeSeeder;
use Database\Seeders\Equipment\EquipmentComponentTypesSeeder;
use Database\Seeders\Equipment\EquipmentConditionsSeeder;
use Database\Seeders\Equipment\EquipmentMaintenancePrioritiesSeeder;
use Database\Seeders\Equipment\EquipmentManufacturersSeeder;
use Database\Seeders\Geography\SystemChapterSeeder;
use Database\Seeders\Geography\SystemCountrySeeder;
use Database\Seeders\Geography\SystemRegionSeeder;
use Database\Seeders\System\ModuleSurfaceAlignmentSeeder;
use Database\Seeders\System\SystemCategorySeeder;
use Database\Seeders\System\SystemModuleSeeder;
use Database\Seeders\System\SystemStatusSeeder;
use Database\Seeders\Users\UserSeeder;
use Database\Seeders\Users\UserSeederFull;
use Illuminate\Database\Seeder;

class CoreReferenceSeeder extends Seeder
{
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

            UserSeederFull::class,
        ]);
    }
}
