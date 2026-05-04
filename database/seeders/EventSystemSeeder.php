<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\SystemLocation;
use App\Models\SystemModule;
use App\Models\SystemStatus;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSystemSeeder extends Seeder
{
    public function run(): void
    {
        $eventModuleId = SystemModule::where('model_type', Event::class)->value('id');
        $statusIds = SystemStatus::where('system_module_id', $eventModuleId)->pluck('id', 'code');
        $creatorIds = User::query()
            ->where(function ($query) {
                $query->where('is_sys_admin', true)
                    ->orWhere('is_admin', true)
                    ->orWhere('is_team_leader', true);
            })
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $rows = [];
        $now = now();

        foreach (SystemLocation::with('chapter')->orderBy('id')->get() as $index => $location) {
            $timezone = $location->timezone ?: 'UTC';
            $creatorId = $creatorIds[$index % count($creatorIds)];

            $pastStart = CarbonImmutable::now($timezone)
                ->subDays(45 + ($index % 14))
                ->setTime(9 + ($index % 3), 0);
            $futureStart = CarbonImmutable::now($timezone)
                ->addDays(21 + ($index % 28))
                ->setTime(9 + ($index % 4), 0);

            $rows[] = [
                'name' => sprintf('%s Community Showcase', $location->chapter?->name ?? $location->name),
                'description' => 'Past seeded event for reporting, equipment usage, and operations testing.',
                'location_id' => $location->id,
                'start_date' => $pastStart->utc()->toDateTimeString(),
                'end_date' => $pastStart->addHours(4)->utc()->toDateTimeString(),
                'status_id' => $statusIds['event_completed'] ?? reset($statusIds),
                'created_by_id' => $creatorId,
                'is_active' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $rows[] = [
                'name' => sprintf('%s Open Training Festival', $location->chapter?->name ?? $location->name),
                'description' => 'Upcoming seeded event for registration, logistics, and equipment checkout testing.',
                'location_id' => $location->id,
                'start_date' => $futureStart->utc()->toDateTimeString(),
                'end_date' => $futureStart->addHours(5)->utc()->toDateTimeString(),
                'status_id' => $statusIds['event_reg_open'] ?? reset($statusIds),
                'created_by_id' => $creatorId,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('events')->insert($rows);
    }
}
