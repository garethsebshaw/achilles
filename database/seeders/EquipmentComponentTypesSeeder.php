<?php

namespace Database\Seeders;

use App\Models\ComponentType;
use App\Models\SystemCategory;
use App\Models\SystemModule;
use Illuminate\Database\Seeder;

class EquipmentComponentTypesSeeder extends Seeder
{
    public function run(): void
    {
        $componentModuleId = SystemModule::where('model_type', ComponentType::class)->value('id');

        $definitions = [
            'Bikes' => [
                ['name' => 'Tandem Frame', 'description' => 'Primary frame for tandem bike setups.', 'months' => 12],
                ['name' => 'Handcycle Frame', 'description' => 'Primary frame for handcycle setups.', 'months' => 12],
                ['name' => 'Front Wheel', 'description' => 'Front wheel assembly.', 'miles' => 500, 'months' => 6],
                ['name' => 'Rear Wheel', 'description' => 'Rear wheel assembly.', 'miles' => 500, 'months' => 6],
                ['name' => 'Handlebar', 'description' => 'Steering and control bar.', 'months' => 12],
                ['name' => 'Crankset', 'description' => 'Drive crank assembly.', 'miles' => 700, 'months' => 9],
                ['name' => 'Brake Set', 'description' => 'Brake system for stopping and control.', 'months' => 6],
            ],
            'Winter Sports' => [
                ['name' => 'Sit-Ski Frame', 'description' => 'Adaptive sit-ski support frame.', 'months' => 12],
                ['name' => 'Ski Binding', 'description' => 'Binding assembly for skis.', 'months' => 6],
                ['name' => 'Outrigger', 'description' => 'Adaptive outrigger for balance and support.', 'months' => 6],
            ],
            'Water Sports' => [
                ['name' => 'Kayak Hull', 'description' => 'Primary kayak shell.', 'months' => 12],
                ['name' => 'Kayak Paddle', 'description' => 'Adaptive kayak paddle.', 'months' => 6],
                ['name' => 'Adaptive Seat', 'description' => 'Supportive adaptive seating insert.', 'months' => 12],
                ['name' => 'PFD', 'description' => 'Personal flotation device component.', 'months' => 12],
                ['name' => 'Swim Buoy', 'description' => 'Open-water safety flotation buoy.', 'months' => 12],
                ['name' => 'Wetsuit Zip', 'description' => 'Wetsuit zipper and closure assembly.', 'months' => 12],
            ],
            'Safety Equipment' => [
                ['name' => 'Helmet Shell', 'description' => 'Protective outer helmet shell.', 'months' => 12],
                ['name' => 'Chin Strap', 'description' => 'Helmet retention strap.', 'months' => 6],
                ['name' => 'First Aid Kit', 'description' => 'Serviceable medical supply kit.', 'months' => 3],
                ['name' => 'Radio', 'description' => 'Portable communications radio.', 'months' => 12],
            ],
            'Adaptive Equipment' => [
                ['name' => 'Support Harness', 'description' => 'Body support harness for adaptive movement.', 'months' => 6],
                ['name' => 'Transfer Board', 'description' => 'Sliding transfer board.', 'months' => 12],
                ['name' => 'Wheelchair Cushion', 'description' => 'Pressure-relief seating cushion.', 'months' => 6],
            ],
            'Personal Equipment' => [
                ['name' => 'Prosthetic Attachment', 'description' => 'Attachment hardware for adaptive prosthetics.', 'months' => 12],
            ],
        ];

        foreach ($definitions as $categoryName => $types) {
            $categoryId = SystemCategory::firstOrCreate(
                [
                    'system_module_id' => $componentModuleId,
                    'name' => $categoryName,
                ],
                [
                    'description' => sprintf('%s component types', $categoryName),
                    'active' => true,
                ]
            )->id;

            foreach ($types as $type) {
                ComponentType::updateOrCreate(
                    [
                        'system_category_id' => $categoryId,
                        'name' => $type['name'],
                    ],
                    [
                        'description' => $type['description'],
                        'default_maintenance_interval_miles' => $type['miles'] ?? null,
                        'default_maintenance_interval_months' => $type['months'] ?? null,
                        'attributes' => null,
                    ]
                );
            }
        }
    }
}
