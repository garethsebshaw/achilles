<?php

namespace Database\Seeders\Equipment;

use Illuminate\Database\Seeder;
use App\Models\Manufacturer;
use App\Models\ComponentType;
use App\Models\EquipmentCondition;
use App\Models\EquipmentMaintenancePriority;
use App\Models\SystemCategory;

class EquipmentManufacturersSeeder extends Seeder
{
    public function run()
    {
        $manufacturers = [
            // Bike Manufacturers
            ['name' => 'Trek', 'website' => 'https://www.trekbikes.com'],
            ['name' => 'Specialized', 'website' => 'https://www.specialized.com'],
            ['name' => 'Giant', 'website' => 'https://www.giant-bicycles.com'],
            ['name' => 'Cannondale', 'website' => 'https://www.cannondale.com'],
            ['name' => 'Santa Cruz', 'website' => 'https://www.santacruzbicycles.com'],

            // Component Manufacturers
            ['name' => 'Shimano', 'website' => 'https://bike.shimano.com'],
            ['name' => 'SRAM', 'website' => 'https://www.sram.com'],
            ['name' => 'Fox Racing', 'website' => 'https://www.foxracing.com'],
            ['name' => 'RockShox', 'website' => 'https://www.sram.com/rockshox'],
            ['name' => 'Continental', 'website' => 'https://www.continental-tires.com/bicycle'],

            // Adaptive Equipment Manufacturers
            ['name' => 'Freedom Concepts', 'website' => 'https://www.freedomconcepts.com'],
            ['name' => 'Invacare', 'website' => 'https://www.invacare.com'],
            ['name' => 'Sunrise Medical', 'website' => 'https://www.sunrisemedical.com'],

            // Ski Equipment
            ['name' => 'Rossignol', 'website' => 'https://www.rossignol.com'],
            ['name' => 'K2', 'website' => 'https://www.k2sports.com'],
            ['name' => 'Salomon', 'website' => 'https://www.salomon.com'],
            ['name' => 'DynaStar', 'website' => 'https://www.dynastar.com'],

            // Water Sports Equipment
            ['name' => 'O\'Neill', 'website' => 'https://www.oneill.com'],
            ['name' => 'Rip Curl', 'website' => 'https://www.ripcurl.com'],
            ['name' => 'Body Glove', 'website' => 'https://www.bodyglove.com'],
        ];

        foreach ($manufacturers as $manufacturer) {
            Manufacturer::create($manufacturer);
        }
    }
}
