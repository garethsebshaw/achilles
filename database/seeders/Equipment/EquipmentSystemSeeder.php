<?php

namespace Database\Seeders\Equipment;

use App\Models\ComponentType;
use App\Models\Equipment;
use App\Models\EquipmentCondition;
use App\Models\EquipmentMaintenancePriority;
use App\Models\Event;
use App\Models\Manufacturer;
use App\Models\SystemCategory;
use App\Models\SystemLocation;
use App\Models\SystemModule;
use App\Models\SystemStatus;
use App\Models\TandemBikePairing;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EquipmentSystemSeeder extends Seeder
{
    public function run(): void
    {
        $equipmentModuleId = SystemModule::where('model_type', Equipment::class)->value('id');
        $maintenanceModuleId = SystemModule::where('model_type', \App\Models\MaintenanceRequest::class)->value('id');
        $pairingModuleId = SystemModule::where('model_type', TandemBikePairing::class)->value('id');

        $categoryIds = SystemCategory::where('system_module_id', $equipmentModuleId)
            ->whereIn('name', [
                'Tandem Bikes',
                'Hand Cycles',
                'Adaptive Skis',
                'Wetsuits',
                'Adaptive Kayaks',
                'Helmets',
                'First Aid Equipment',
                'Mobility Aids',
            ])
            ->pluck('id', 'name');

        $statusIds = SystemStatus::where('system_module_id', $equipmentModuleId)->pluck('id', 'code');
        $maintenanceStatusIds = SystemStatus::where('system_module_id', $maintenanceModuleId)->pluck('id', 'code');
        $pairingStatusIds = SystemStatus::where('system_module_id', $pairingModuleId)->pluck('id', 'code');
        $conditionIds = EquipmentCondition::orderBy('rating')->pluck('id', 'name');
        $priorityIds = EquipmentMaintenancePriority::orderBy('level')->pluck('id', 'name');
        $manufacturerIds = Manufacturer::orderBy('id')->pluck('id')->all();
        $componentTypeIds = ComponentType::pluck('id', 'name');

        $adminIds = User::query()
            ->where(function ($query) {
                $query->where('is_sys_admin', true)
                    ->orWhere('is_admin', true)
                    ->orWhere('is_team_leader', true);
            })
            ->orderBy('id')
            ->pluck('id')
            ->all();
        $guideIds = User::query()
            ->where(function ($query) {
                $query->where('is_guide', true)
                    ->orWhere('is_team_leader', true);
            })
            ->orderBy('id')
            ->pluck('id')
            ->all();
        $athleteIds = User::query()
            ->where('is_athlete', true)
            ->orderBy('id')
            ->pluck('id')
            ->all();
        $eventIdsByLocation = Event::orderBy('id')->get()->groupBy('location_id');

        $now = now();
        $storageRows = [];
        $storageNames = [];

        foreach (SystemLocation::orderBy('id')->pluck('id')->all() as $locationId) {
            foreach (['Main Cage', 'Mobile Van'] as $typeIndex => $name) {
                $storageRows[] = [
                    'location_id' => $locationId,
                    'name' => $name,
                    'type' => $typeIndex === 0 ? 'unit' : 'van',
                    'capacity' => $typeIndex === 0 ? 60 : 20,
                    'notes' => $typeIndex === 0 ? 'Primary seeded equipment storage.' : 'Seeded mobile support van storage.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                $storageNames[$locationId][] = $name;
            }
        }

        DB::table('storage_locations')->insert($storageRows);

        $storageIds = DB::table('storage_locations')
            ->select('id', 'location_id', 'name')
            ->get()
            ->groupBy('location_id')
            ->map(function ($items) {
                return $items->pluck('id', 'name')->all();
            });

        $equipmentBlueprints = [
            [
                'label' => 'Tandem Bike',
                'category' => 'Tandem Bikes',
                'status_code' => 'equip_available',
                'condition' => 'Excellent',
                'components' => ['Tandem Frame', 'Front Wheel', 'Rear Wheel', 'Handlebar', 'Brake Set'],
            ],
            [
                'label' => 'Hand Cycle',
                'category' => 'Hand Cycles',
                'status_code' => 'equip_in_use',
                'condition' => 'Good',
                'components' => ['Handcycle Frame', 'Front Wheel', 'Rear Wheel', 'Crankset', 'Brake Set'],
            ],
            [
                'label' => 'Adaptive Ski Kit',
                'category' => 'Adaptive Skis',
                'status_code' => 'equip_available',
                'condition' => 'Good',
                'components' => ['Sit-Ski Frame', 'Ski Binding', 'Outrigger', 'Helmet Shell'],
            ],
            [
                'label' => 'Open Water Wetsuit',
                'category' => 'Wetsuits',
                'status_code' => 'equip_reserved',
                'condition' => 'Good',
                'components' => ['Wetsuit Zip', 'Swim Buoy', 'Helmet Shell'],
            ],
            [
                'label' => 'Adaptive Kayak',
                'category' => 'Adaptive Kayaks',
                'status_code' => 'equip_available',
                'condition' => 'Excellent',
                'components' => ['Kayak Hull', 'Kayak Paddle', 'Adaptive Seat', 'PFD'],
            ],
            [
                'label' => 'Safety Helmet',
                'category' => 'Helmets',
                'status_code' => 'equip_available',
                'condition' => 'Excellent',
                'components' => ['Helmet Shell', 'Chin Strap'],
            ],
            [
                'label' => 'First Aid Pack',
                'category' => 'First Aid Equipment',
                'status_code' => 'equip_inspection',
                'condition' => 'Good',
                'components' => ['First Aid Kit', 'Radio'],
            ],
            [
                'label' => 'Mobility Support Kit',
                'category' => 'Mobility Aids',
                'status_code' => 'equip_maintenance',
                'condition' => 'Poor',
                'components' => ['Wheelchair Cushion', 'Support Harness', 'Transfer Board'],
            ],
        ];

        $equipmentRows = [];
        $qrCodes = [];

        foreach (SystemLocation::with('chapter')->orderBy('id')->get() as $locationIndex => $location) {
            foreach ($equipmentBlueprints as $blueprintIndex => $blueprint) {
                $qrCode = sprintf('ACH-%03d-%02d', $location->id, $blueprintIndex + 1);
                $qrCodes[] = $qrCode;

                $equipmentRows[] = [
                    'system_category_id' => $categoryIds[$blueprint['category']],
                    'manufacturer_id' => $manufacturerIds[($locationIndex + $blueprintIndex) % count($manufacturerIds)],
                    'name' => sprintf('%s %s', $location->chapter?->city ?: $location->chapter?->name ?: 'Achilles', $blueprint['label']),
                    'model' => sprintf('%s-%02d', Str::upper(Str::slug($blueprint['label'])), ($locationIndex % 30) + 1),
                    'serial_number' => sprintf('SER-%04d-%02d', $location->id, $blueprintIndex + 1),
                    'qr_code' => $qrCode,
                    'location_id' => $location->id,
                    'storage_location_id' => $storageIds[$location->id][$storageNames[$location->id][$blueprintIndex % 2]],
                    'status_id' => $statusIds[$blueprint['status_code']] ?? reset($statusIds),
                    'condition_id' => $conditionIds[$blueprint['condition']] ?? reset($conditionIds),
                    'owner_type' => null,
                    'owner_id' => null,
                    'assigned_user_id' => $guideIds[($locationIndex + $blueprintIndex) % count($guideIds)],
                    'purchase_date' => now()->subMonths(18 + ($locationIndex % 24))->toDateString(),
                    'purchase_price' => 400 + (($locationIndex + 1) * ($blueprintIndex + 3)),
                    'warranty_expiry' => now()->addMonths(12 + ($blueprintIndex * 2))->toDateString(),
                    'last_maintenance_date' => now()->subDays(20 + ($blueprintIndex * 4))->toDateTimeString(),
                    'next_maintenance_date' => now()->addDays(30 + ($blueprintIndex * 6))->toDateTimeString(),
                    'notes' => sprintf('Seeded %s inventory for global chapter testing.', strtolower($blueprint['label'])),
                    'attributes' => json_encode([
                        'seeded' => true,
                        'sports' => [$blueprint['category']],
                        'chapter_id' => $location->chapter_id,
                    ]),
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($equipmentRows, 500) as $chunk) {
            DB::table('equipment')->insert($chunk);
        }

        $equipmentByQrCode = DB::table('equipment')
            ->select('id', 'location_id', 'system_category_id', 'qr_code')
            ->whereIn('qr_code', $qrCodes)
            ->get()
            ->keyBy('qr_code');

        $equipmentByLocation = $equipmentByQrCode->groupBy('location_id');
        $componentRows = [];
        $compatibilityRows = [];

        foreach (SystemLocation::orderBy('id')->pluck('id')->all() as $locationId) {
            foreach ($equipmentBlueprints as $blueprintIndex => $blueprint) {
                $equipment = $equipmentByQrCode[sprintf('ACH-%03d-%02d', $locationId, $blueprintIndex + 1)] ?? null;

                if (! $equipment) {
                    continue;
                }

                foreach ($blueprint['components'] as $componentIndex => $componentName) {
                    $typeId = $componentTypeIds[$componentName] ?? null;

                    if (! $typeId) {
                        continue;
                    }

                    $componentRows[] = [
                        'equipment_id' => $equipment->id,
                        'component_type_id' => $typeId,
                        'manufacturer_id' => $manufacturerIds[($equipment->id + $componentIndex) % count($manufacturerIds)],
                        'model' => sprintf('%s-COMP-%02d', Str::upper(Str::slug($componentName)), ($componentIndex + 1)),
                        'serial_number' => sprintf('COMP-%05d-%02d', $equipment->id, $componentIndex + 1),
                        'status_id' => $statusIds['equip_available'] ?? reset($statusIds),
                        'condition_id' => $conditionIds['Excellent'] ?? reset($conditionIds),
                        'installation_date' => now()->subMonths(8 + $componentIndex)->toDateString(),
                        'warranty_expiry' => now()->addMonths(10 + ($componentIndex * 2))->toDateString(),
                        'is_monitored' => $componentIndex < 2,
                        'maintenance_interval_miles' => in_array($componentName, ['Front Wheel', 'Rear Wheel', 'Crankset'], true) ? 500 : null,
                        'maintenance_interval_months' => 6 + $componentIndex,
                        'last_maintenance_date' => now()->subDays(15 + ($componentIndex * 7))->toDateTimeString(),
                        'next_maintenance_date' => now()->addDays(25 + ($componentIndex * 9))->toDateTimeString(),
                        'notes' => 'Seeded component record.',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
            }
        }

        foreach (array_chunk($componentRows, 1000) as $chunk) {
            DB::table('equipment_components')->insert($chunk);
        }

        $compatibilityMap = [
            'Tandem Frame' => ['Front Wheel', 'Rear Wheel', 'Handlebar', 'Brake Set'],
            'Handcycle Frame' => ['Front Wheel', 'Rear Wheel', 'Crankset', 'Brake Set'],
            'Kayak Hull' => ['Kayak Paddle', 'Adaptive Seat', 'PFD'],
            'Sit-Ski Frame' => ['Ski Binding', 'Outrigger', 'Helmet Shell'],
        ];

        foreach ($compatibilityMap as $componentName => $compatibleNames) {
            $sourceId = $componentTypeIds[$componentName] ?? null;

            if (! $sourceId) {
                continue;
            }

            foreach ($compatibleNames as $compatibleName) {
                $compatibleId = $componentTypeIds[$compatibleName] ?? null;

                if (! $compatibleId) {
                    continue;
                }

                $compatibilityRows[] = [
                    'component_type_id' => $sourceId,
                    'compatible_with_id' => $compatibleId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('component_compatibility')->insert($compatibilityRows);

        $componentIdsByEquipment = DB::table('equipment_components')
            ->select('id', 'equipment_id')
            ->get()
            ->groupBy('equipment_id')
            ->map(fn ($items) => $items->pluck('id')->all());

        $requestRows = [];
        $logRows = [];
        $checkoutRows = [];
        $pairingRows = [];
        $pairingCheckRows = [];

        foreach ($equipmentByQrCode as $equipment) {
            $locationEvents = $eventIdsByLocation->get($equipment->location_id, collect());
            $eventId = optional($locationEvents->first())->id;
            $componentId = $componentIdsByEquipment[$equipment->id][0] ?? null;
            $adminId = $adminIds[$equipment->id % count($adminIds)];
            $guideId = $guideIds[$equipment->id % count($guideIds)];
            $athleteId = $athleteIds[$equipment->id % count($athleteIds)];

            if ($equipment->id % 4 === 0) {
                $requestRows[] = [
                    'equipment_id' => $equipment->id,
                    'component_id' => $componentId,
                    'reported_by_id' => $guideId,
                    'assigned_to_id' => $adminId,
                    'status_id' => $maintenanceStatusIds['maintreq_completed'] ?? reset($maintenanceStatusIds),
                    'priority_id' => $priorityIds['Medium'] ?? reset($priorityIds),
                    'description' => 'Seeded maintenance tune-up request.',
                    'reported_at' => now()->subDays(12)->toDateTimeString(),
                    'assigned_at' => now()->subDays(11)->toDateTimeString(),
                    'estimated_time' => 90,
                    'actual_time' => 80,
                    'estimated_cost' => 120,
                    'actual_cost' => 95,
                    'completed_at' => now()->subDays(9)->toDateTimeString(),
                    'parent_request_id' => null,
                    'notes' => 'Completed seeded maintenance request.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            } elseif ($equipment->id % 5 === 0) {
                $requestRows[] = [
                    'equipment_id' => $equipment->id,
                    'component_id' => $componentId,
                    'reported_by_id' => $guideId,
                    'assigned_to_id' => $adminId,
                    'status_id' => $maintenanceStatusIds['maintreq_progress'] ?? reset($maintenanceStatusIds),
                    'priority_id' => $priorityIds['High'] ?? reset($priorityIds),
                    'description' => 'Seeded in-progress service request.',
                    'reported_at' => now()->subDays(3)->toDateTimeString(),
                    'assigned_at' => now()->subDays(2)->toDateTimeString(),
                    'estimated_time' => 120,
                    'actual_time' => null,
                    'estimated_cost' => 180,
                    'actual_cost' => null,
                    'completed_at' => null,
                    'parent_request_id' => null,
                    'notes' => 'Waiting on technician completion.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            $checkoutRows[] = [
                'equipment_id' => $equipment->id,
                'user_id' => $guideId,
                'event_id' => $eventId,
                'checked_out_at' => now()->subDays(($equipment->id % 6) + 1)->toDateTimeString(),
                'expected_return_at' => now()->addDays(($equipment->id % 3) + 1)->toDateTimeString(),
                'returned_at' => $equipment->id % 3 === 0 ? now()->subHours(5)->toDateTimeString() : null,
                'distance_traveled' => 5 + ($equipment->id % 30),
                'condition_out_id' => $conditionIds['Excellent'] ?? reset($conditionIds),
                'condition_in_id' => $equipment->id % 3 === 0 ? ($conditionIds['Good'] ?? reset($conditionIds)) : null,
                'notes' => 'Seeded operational checkout.',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if ($equipment->system_category_id === ($categoryIds['Tandem Bikes'] ?? null)) {
                $pairingRows[] = [
                    'bike_id' => $equipment->id,
                    'pilot_user_id' => $guideId,
                    'stoker_user_id' => $athleteId,
                    'compatibility_status_id' => $pairingStatusIds['compat_green'] ?? reset($pairingStatusIds),
                    'weight_compatibility_score' => 92.5,
                    'compatibility_details' => json_encode([
                        'seeded' => true,
                        'notes' => 'Guide and athlete pair well on this tandem setup.',
                    ]),
                    'last_checked_at' => now()->subDays(7)->toDateTimeString(),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $pairingCheckRows[] = [
                    'bike_id' => $equipment->id,
                    'checked_at' => now()->subDays(6)->toDateTimeString(),
                    'check_results' => json_encode([
                        'brakes' => 'pass',
                        'tires' => 'pass',
                        'fit' => 'pass',
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ];
            }
        }

        if ($requestRows !== []) {
            DB::table('maintenance_requests')->insert($requestRows);
        }

        $requestIdsByEquipment = DB::table('maintenance_requests')
            ->select('id', 'equipment_id')
            ->get()
            ->groupBy('equipment_id')
            ->map(fn ($items) => $items->pluck('id')->all());

        foreach ($requestIdsByEquipment as $equipmentId => $requestIds) {
            $componentId = $componentIdsByEquipment[$equipmentId][0] ?? null;
            $logRows[] = [
                'maintenance_request_id' => $requestIds[0],
                'equipment_id' => $equipmentId,
                'component_id' => $componentId,
                'performed_by_id' => $adminIds[$equipmentId % count($adminIds)],
                'work_type' => 'service',
                'description' => 'Seeded maintenance work log entry.',
                'performed_at' => now()->subDays(8)->toDateTimeString(),
                'time_spent' => 75,
                'cost' => 95,
                'parts_used' => json_encode(['lubricant', 'replacement bolt']),
                'notes' => 'Service completed successfully.',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($logRows !== []) {
            DB::table('maintenance_logs')->insert($logRows);
        }

        DB::table('equipment_checkouts')->insert($checkoutRows);

        if ($pairingRows !== []) {
            DB::table('tandem_bike_pairings')->insert($pairingRows);
        }

        if ($pairingCheckRows !== []) {
            DB::table('tandem_bike_pairing_checks')->insert($pairingCheckRows);
        }
    }
}
