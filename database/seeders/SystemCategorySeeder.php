<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SystemCategory;
use App\Models\SystemModule;
use App\Models\Language;
use Illuminate\Support\Facades\DB;

class SystemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Equipment Categories
        $equipmentModule = SystemModule::firstOrCreate([
            'name' => 'Equipment Management',
            'model_type' => \App\Models\Equipment::class
        ]);

        $equipmentCategories = [
            // Cycling Equipment
            'Bikes' => [
                'Standard Bikes',
                'Tandem',
                'Recumbent Bikes',
                'Push Rim',
                'Hand Cycles',
                'Tandem Bikes',
                'Tricycles',
                'Electric Bikes',
                'Mountain Bikes',
                'Road Bikes',
            ],

            // Winter Sports Equipment
            'Winter Sports' => [
                'Adaptive Skis',
                'Sit Skis',
                'Cross-Country Skis',
                'Snowboards',
                'Adaptive Snowboards',
                'Winter Safety Equipment',
            ],

            // Water Sports Equipment
            'Water Sports' => [
                'Wetsuits',
                'Life Vests',
                'Adaptive Surfboards',
                'Kayaks',
                'Adaptive Kayaks',
                'Swimming Aids',
            ],

            // Safety Equipment
            'Safety Equipment' => [
                'Helmets',
                'Protective Pads',
                'Reflective Gear',
                'First Aid Equipment',
                'Communication Devices',
            ],

            // Adaptive Equipment
            'Adaptive Equipment' => [
                'Mobility Aids',
                'Balance Aids',
                'Support Harnesses',
                'Transfer Devices',
                'Communication Aids',
            ],

            // Components & Parts
            'Components' => [
                'Bike Components',
                'Ski Components',
                'Safety Components',
                'Adaptive Components',
                'Electronic Components',
            ],

            // Storage & Transport
            'Storage & Transport' => [
                'Equipment Cases',
                'Transport Racks',
                'Storage Units',
                'Vehicle Mounts',
                'Protective Covers',
            ],

            // Maintenance Equipment
            'Maintenance' => [
                'Tools',
                'Spare Parts',
                'Cleaning Supplies',
                'Repair Kits',
                'Diagnostic Equipment',
            ],

            // Personal Equipment
            'Personal Equipment' => [
                'Prosthetics',
                'Orthotics',
                'Personal Adaptive Devices',
                'Custom Equipment',
            ],

            // Training Equipment
            'Training Equipment' => [
                'Training Aids',
                'Practice Equipment',
                'Assessment Tools',
                'Teaching Aids',
            ]
        ];

        foreach ($equipmentCategories as $mainCategory => $subCategories) {
            $parentCategory = SystemCategory::firstOrCreate([
                'system_module_id' => $equipmentModule->id,
                'name' => $mainCategory
            ]);

            foreach ($subCategories as $subCategory) {
                SystemCategory::firstOrCreate([
                    'system_module_id' => $equipmentModule->id,
                    'parent_id' => $parentCategory->id,
                    'name' => $subCategory
                ]);
            }
        }

        // Sports

        $workoutsModuleId = DB::table('system_modules')
            ->where('model_type', 'App\Models\Workout')
            ->value('id');


        /// add sports to the categories table
        $sports = [
            // Running and Walking
            [
                'name' => 'Running',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 100],
                        'pace' => ['type' => 'object', 'properties' => [
                            'min' => ['type' => 'number', 'unit' => 'min/mile'],
                            'max' => ['type' => 'number', 'unit' => 'min/mile']
                        ]],
                        'terrain' => ['type' => 'select', 'options' => ['Road', 'Trail', 'Track', 'Mixed']]
                    ]
                ])
            ],
            [
                'name' => 'Walking',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 50],
                        'pace' => ['type' => 'object', 'properties' => [
                            'min' => ['type' => 'number', 'unit' => 'min/mile'],
                            'max' => ['type' => 'number', 'unit' => 'min/mile']
                        ]],
                        'accessibility' => ['type' => 'select', 'options' => ['Fully Accessible', 'Partial Accessibility', 'Limited Accessibility']]
                    ]
                ])
            ],

            // Cycling
            [
                'name' => 'Cycling (Tandem)',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 200],
                        'speed' => ['type' => 'object', 'properties' => [
                            'min' => ['type' => 'number', 'unit' => 'mph'],
                            'max' => ['type' => 'number', 'unit' => 'mph']
                        ]],
                        'bike_type' => ['type' => 'select', 'options' => ['Road Tandem', 'Mountain Tandem', 'Hybrid Tandem']]
                    ]
                ])
            ],
            [
                'name' => 'Cycling (Hand Cycle)',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 150],
                        'speed' => ['type' => 'object', 'properties' => [
                            'min' => ['type' => 'number', 'unit' => 'mph'],
                            'max' => ['type' => 'number', 'unit' => 'mph']
                        ]],
                        'hand_cycle_type' => ['type' => 'select', 'options' => ['Racing', 'Recreational', 'Mountain']]
                    ]
                ])
            ],
            [
                'name' => 'ParaCycling',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'discipline' => ['type' => 'select', 'options' => ['Road', 'Track', 'Time Trial']],
                        'classification' => ['type' => 'select', 'options' => ['H1-H5', 'T1-T2', 'B1-B3']]
                    ]
                ])
            ],
            [
                'name' => 'Winter Handcycle',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 50],
                        'surface' => ['type' => 'select', 'options' => ['Packed Snow', 'Ice Trail', 'Mixed']]
                    ]
                ])
            ],

            // Skiing
            [
                'name' => 'Skiing (Alpine)',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 50],
                        'skill_level' => ['type' => 'select', 'options' => ['Beginner', 'Intermediate', 'Advanced', 'Expert']],
                        'terrain' => ['type' => 'select', 'options' => ['Groomed', 'Powder', 'Mixed', 'Backcountry']]
                    ]
                ])
            ],
            [
                'name' => 'Skiing (Nordic/Cross-Country)',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 100],
                        'technique' => ['type' => 'select', 'options' => ['Classic', 'Skate', 'Backcountry']],
                        'terrain' => ['type' => 'select', 'options' => ['Groomed Trails', 'Ungroomed', 'Mixed']]
                    ]
                ])
            ],
            [
                'name' => 'Sit-Ski',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 30],
                        'skill_level' => ['type' => 'select', 'options' => ['Beginner', 'Intermediate', 'Advanced']],
                        'terrain' => ['type' => 'select', 'options' => ['Groomed', 'Powder', 'Mixed']]
                    ]
                ])
            ],

            // Water Sports
            [
                'name' => 'Swimming',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'meters', 'min' => 0, 'max' => 5000],
                        'stroke' => ['type' => 'select', 'options' => ['Freestyle', 'Backstroke', 'Breaststroke', 'Butterfly']],
                        'pool_type' => ['type' => 'select', 'options' => ['Indoor', 'Outdoor', 'Open Water']]
                    ]
                ])
            ],
            [
                'name' => 'Para-Swimming',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'meters', 'min' => 0, 'max' => 400],
                        'classification' => ['type' => 'select', 'options' => ['S1-S10', 'S11-S13', 'S14']]
                    ]
                ])
            ],

            // Endurance Sports
            [
                'name' => 'Triathlon',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'swim_distance' => ['type' => 'number', 'unit' => 'meters', 'min' => 0, 'max' => 3800],
                        'bike_distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 112],
                        'run_distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 26.2],
                        'race_type' => ['type' => 'select', 'options' => ['Sprint', 'Olympic', 'Half Ironman', 'Full Ironman']]
                    ]
                ])
            ],

            // Mobility Sports
            [
                'name' => 'Racing Wheelchair',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 26.2],
                        'wheelchair_type' => ['type' => 'select', 'options' => ['Racing', 'Sports', 'Everyday']],
                        'surface' => ['type' => 'select', 'options' => ['Road', 'Track', 'Mixed']]
                    ]
                ])
            ],

            // Team and Adaptive Sports
            [
                'name' => 'Wheelchair Basketball',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'game_type' => ['type' => 'select', 'options' => ['Practice', 'Competitive']],
                        'court_type' => ['type' => 'select', 'options' => ['Indoor', 'Outdoor']]
                    ]
                ])
            ],
            [
                'name' => 'Seated Volleyball',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'game_type' => ['type' => 'select', 'options' => ['Practice', 'Competitive']],
                        'skill_level' => ['type' => 'select', 'options' => ['Beginner', 'Intermediate', 'Advanced']]
                    ]
                ])
            ],
            [
                'name' => 'Para-Athletics',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'event_type' => ['type' => 'select', 'options' => ['Track', 'Field', 'Combined']],
                        'classification' => ['type' => 'select', 'options' => ['T11-13', 'T20', 'T35-38', 'T40-47', 'T51-54']]
                    ]
                ])
            ],

            // Precision Sports
            [
                'name' => 'Para-Archery',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'classification' => ['type' => 'select', 'options' => ['Open Compound', 'Open Recurve', 'Visually Impaired']],
                        'distance' => ['type' => 'number', 'unit' => 'meters', 'min' => 0, 'max' => 70]
                    ]
                ])
            ],
            [
                'name' => 'Wheelchair Fencing',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'weapon' => ['type' => 'select', 'options' => ['Foil', 'Epee', 'Sabre']],
                        'classification' => ['type' => 'select', 'options' => ['A', 'B', 'C']]
                    ]
                ])
            ],
            [
                'name' => 'Boccia',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'competition_type' => ['type' => 'select', 'options' => ['Individual', 'Pairs', 'Teams']],
                        'classification' => ['type' => 'select', 'options' => ['BC1', 'BC2', 'BC3', 'BC4']]
                    ]
                ])
            ],

            // Additional Adventure Sports
            [
                'name' => 'Rock Climbing (Adaptive)',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'difficulty' => ['type' => 'select', 'options' => ['Indoor', 'Beginner Outdoor', 'Intermediate Outdoor', 'Advanced']],
                        'climb_type' => ['type' => 'select', 'options' => ['Top Rope', 'Lead', 'Bouldering']],
                        'accessibility' => ['type' => 'select', 'options' => ['Seated', 'Standing', 'Prosthetic-Friendly']]
                    ]
                ])
            ],
            [
                'name' => 'Orienteering',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 20],
                        'difficulty' => ['type' => 'select', 'options' => ['Beginner', 'Intermediate', 'Advanced']]
                    ]
                ])
            ],
            [
                'name' => 'Hiking',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 50],
                        'difficulty' => ['type' => 'select', 'options' => ['Easy', 'Moderate', 'Challenging', 'Strenuous']],
                        'terrain' => ['type' => 'select', 'options' => ['Flat', 'Rolling', 'Mountainous', 'Mixed']]
                    ]
                ])
            ],
            // Additional Water Sports
            [
                'name' => 'Kayaking (Adaptive)',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'miles', 'min' => 0, 'max' => 50],
                        'water_type' => ['type' => 'select', 'options' => ['Flat Water', 'River', 'Sea']]
                    ]
                ])
            ],
            // Martial Arts
            [
                'name' => 'Adaptive Martial Arts',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'discipline' => ['type' => 'select', 'options' => ['Judo', 'Karate', 'Taekwondo', 'Brazilian Jiu-Jitsu']],
                        'skill_level' => ['type' => 'select', 'options' => ['Beginner', 'Intermediate', 'Advanced']]
                    ]
                ])
            ],

            // Additional Extreme Sports
            [
                'name' => 'Adaptive Surfing',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'wave_type' => ['type' => 'select', 'options' => ['Small', 'Medium', 'Large']],
                        'board_type' => ['type' => 'select', 'options' => ['Sit-Down Board', 'Adaptive Standing Board']]
                    ]
                ])
            ],
            [
                'name' => 'Adaptive Skateboarding',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'terrain' => ['type' => 'select', 'options' => ['Skatepark', 'Street', 'Smooth Surface']],
                        'skill_level' => ['type' => 'select', 'options' => ['Beginner', 'Intermediate', 'Advanced']]
                    ]
                ])
            ],

            // Dance and Movement
            [
                'name' => 'Adaptive Dance',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'style' => ['type' => 'select', 'options' => ['Ballet', 'Contemporary', 'Hip Hop', 'Ballroom']],
                        'movement_type' => ['type' => 'select', 'options' => ['Seated', 'Standing', 'Mixed']]
                    ]
                ])
            ],

            // Team Sports
            [
                'name' => 'Goalball',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'game_type' => ['type' => 'select', 'options' => ['Practice', 'Competitive']],
                        'vision_classification' => ['type' => 'select', 'options' => ['B1', 'B2', 'B3']]
                    ]
                ])
            ],
            // Aquatic Sports
            [
                'name' => 'Adaptive Water Polo',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'game_type' => ['type' => 'select', 'options' => ['Practice', 'Competitive']],
                        'pool_type' => ['type' => 'select', 'options' => ['Indoor', 'Outdoor']]
                    ]
                ])
            ],
            [
                'name' => 'Dragon Boat Racing (Adaptive)',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'distance' => ['type' => 'number', 'unit' => 'meters', 'min' => 0, 'max' => 1000],
                        'boat_type' => ['type' => 'select', 'options' => ['Standard', 'Adaptive Seating']]
                    ]
                ])
            ],


            // Outdoor and Adventure Sports
            [
                'name' => 'Adaptive Fishing',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'fishing_type' => ['type' => 'select', 'options' => ['Boat', 'Shore', 'Pier']],
                        'accessibility' => ['type' => 'select', 'options' => ['Fully Accessible', 'Partially Accessible']]
                    ]
                ])
            ],
            [
                'name' => 'Adaptive Horseback Riding',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'riding_style' => ['type' => 'select', 'options' => ['Western', 'English', 'Therapeutic']],
                        'skill_level' => ['type' => 'select', 'options' => ['Beginner', 'Intermediate', 'Advanced']]
                    ]
                ])
            ],

            // Emerging and Unique Sports
            [
                'name' => 'Power Soccer',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'game_type' => ['type' => 'select', 'options' => ['Practice', 'Competitive']],
                        'wheelchair_type' => ['type' => 'select', 'options' => ['Power', 'Sports']]
                    ]
                ])
            ],
            [
                'name' => 'Wheelchair Tennis',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'match_type' => ['type' => 'select', 'options' => ['Singles', 'Doubles']],
                        'classification' => ['type' => 'select', 'options' => ['Open', 'Quad']]
                    ]
                ])
            ],
            // Additional Unique Sports
            [
                'name' => 'Wheelchair Rugby',
                'system_module_id' => $workoutsModuleId,
                'metadata' => json_encode([
                    'fields' => [
                        'game_type' => ['type' => 'select', 'options' => ['Practice', 'Competitive']],
                        'classification' => ['type' => 'select', 'options' => ['Open', 'Elite']]
                    ]
                ])
            ],


        ];

        DB::table('system_categories')->insert($sports);

        // Meetings points for workouts

        $meetingPointsModelId = DB::table('system_modules')
            ->where('model_type', 'App\Models\WorkoutMeetingPoint')
            ->value('id');

        // Meetings points created by...
        $meetingPoints = [
            [
                'name' => 'System',
                'system_module_id' => $meetingPointsModelId,
            ],
            [
                'name' => 'Admin',
                'system_module_id' => $meetingPointsModelId,
            ],
            [
                'name' => 'User',
                'system_module_id' => $meetingPointsModelId,
            ],
        ];

        DB::table('system_categories')->insert($meetingPoints);

        // Meetings points for workouts

        $meetingPointsModelId = DB::table('system_modules')
            ->where('model_type', 'App\Models\WorkoutEquipmentAssignment')
            ->value('id');

        // Meetings points created by...
        $assignmentTypes = [
            ['name' => 'Primary Equipment', 'system_module_id' => $meetingPointsModelId],
            ['name' => 'Backup Equipment', 'system_module_id' => $meetingPointsModelId],
            ['name' => 'Shared Equipment', 'system_module_id' => $meetingPointsModelId],
            ['name' => 'Adaptive Equipment', 'system_module_id' => $meetingPointsModelId],
            ['name' => 'Personal Equipment', 'system_module_id' => $meetingPointsModelId],
            ['name' => 'Unavailable Equipment', 'system_module_id' => $meetingPointsModelId],
            ['name' => 'Unassigned Equipment', 'system_module_id' => $meetingPointsModelId],
        ];

        DB::table('system_categories')->insert($assignmentTypes);

    }
}
