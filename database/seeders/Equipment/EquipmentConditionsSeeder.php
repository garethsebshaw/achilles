<?php

namespace Database\Seeders\Equipment;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EquipmentCondition;

class EquipmentConditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $conditions = [
            [
                'name' => 'Excellent',
                'description' => 'Like new condition, fully functional with no visible wear',
                'rating' => 5,
                'serviceable' => true
            ],
            [
                'name' => 'Good',
                'description' => 'Minor wear but fully functional, no immediate maintenance needed',
                'rating' => 4,
                'serviceable' => true
            ],
            [
                'name' => 'Fair',
                'description' => 'Shows wear, functional but may need maintenance soon',
                'rating' => 3,
                'serviceable' => true
            ],
            [
                'name' => 'Poor',
                'description' => 'Significant wear, requires maintenance before next use',
                'rating' => 2,
                'serviceable' => false
            ],
            [
                'name' => 'Critical',
                'description' => 'Not functional, requires immediate repair or replacement',
                'rating' => 1,
                'serviceable' => false
            ]
        ];

        foreach ($conditions as $condition) {
            EquipmentCondition::create($condition);
        }
    }
}
