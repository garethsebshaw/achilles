<?php

namespace Database\Seeders\Equipment;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EquipmentMaintenancePriority;

class EquipmentMaintenancePrioritiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $priorities = [
            [
                'name' => 'Critical',
                'description' => 'Safety issue or equipment completely unusable. Requires immediate attention.',
                'level' => 5,
                'response_time_hours' => 24
            ],
            [
                'name' => 'High',
                'description' => 'Significant impact on functionality. Should be addressed within 48 hours.',
                'level' => 4,
                'response_time_hours' => 48
            ],
            [
                'name' => 'Medium',
                'description' => 'Moderate impact on functionality. Address within one week.',
                'level' => 3,
                'response_time_hours' => 168
            ],
            [
                'name' => 'Low',
                'description' => 'Minor issue, equipment still usable. Address when convenient.',
                'level' => 2,
                'response_time_hours' => 336
            ],
            [
                'name' => 'Scheduled',
                'description' => 'Regular maintenance or non-urgent upgrades.',
                'level' => 1,
                'response_time_hours' => 720
            ]
        ];

        foreach ($priorities as $priority) {
            EquipmentMaintenancePriority::create($priority);
        }
    }
}
