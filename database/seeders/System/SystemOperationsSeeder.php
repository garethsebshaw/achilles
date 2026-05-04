<?php

namespace Database\Seeders\System;

use App\Models\Certification;
use App\Models\Equipment;
use App\Models\Event;
use App\Models\SystemAuditLog;
use App\Models\SystemLocation;
use App\Models\SystemModule;
use App\Models\SystemSetting;
use App\Models\SystemTag;
use App\Models\User;
use App\Models\UserCertification;
use App\Models\Workout;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SystemOperationsSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedCertificationCoverage();
        $this->seedLocationAccess();
        $this->seedTagsAndTaggables();
        $this->seedNotifications();
        $this->seedMediaFiles();
        $this->seedAuditLogs();
    }

    private function seedSettings(): void
    {
        $settings = [
            ['key' => 'app.default_timezone', 'value' => 'UTC', 'type' => 'string', 'description' => 'Fallback timezone for seeded environments.'],
            ['key' => 'workouts.advance_create_weeks', 'value' => '52', 'type' => 'integer', 'description' => 'How far ahead recurring workout sessions are generated.'],
            ['key' => 'workouts.signup_window_hours', 'value' => '168', 'type' => 'integer', 'description' => 'Signup horizon in hours for scheduled sessions.'],
            ['key' => 'equipment.default_checkout_days', 'value' => '7', 'type' => 'integer', 'description' => 'Default seeded checkout duration in days.'],
            ['key' => 'equipment.maintenance_alert_days', 'value' => '14', 'type' => 'integer', 'description' => 'Lead time for maintenance alerts.'],
            ['key' => 'weather.forecast_refresh_minutes', 'value' => '180', 'type' => 'integer', 'description' => 'Expected weather forecast refresh frequency.'],
            ['key' => 'chapters.default_language', 'value' => 'en', 'type' => 'string', 'description' => 'Default locale for chapter operations.'],
            ['key' => 'notifications.digest_hour_utc', 'value' => '13', 'type' => 'integer', 'description' => 'Daily digest delivery hour in UTC.'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'type' => $setting['type'],
                    'is_encrypted' => false,
                    'description' => $setting['description'],
                ]
            );
        }
    }

    private function seedCertificationCoverage(): void
    {
        $countryIds = DB::table('system_countries')->where('active', true)->orderBy('id')->pluck('id')->all();
        $now = now();

        foreach (Certification::orderBy('id')->get() as $index => $certification) {
            $assigned = array_slice($countryIds, $index % max(1, count($countryIds) - 3), 3);

            foreach ($assigned as $countryId) {
                DB::table('certification_countries')->insert([
                    'certification_id' => $certification->id,
                    'system_country_id' => $countryId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $documentRows = [];

        foreach (UserCertification::orderBy('id')->limit(300)->get() as $index => $userCertification) {
            $documentRows[] = [
                'certification_type_id' => optional(optional($userCertification->certification)->certificationType)->id,
                'name' => sprintf('%s Evidence', $userCertification->name ?: optional($userCertification->certification)->name ?: 'Certification'),
                'description' => 'Seeded supporting certification document.',
                'validity_period' => max(1, (int) $userCertification->validity_period),
                'user_certification_id' => $userCertification->id,
                'file_path' => sprintf('seeded/certifications/%d/document-%03d.pdf', $userCertification->user_id, $index + 1),
                'file_type' => 'pdf',
                'uploaded_at' => $userCertification->uploaded_at ?: now()->subDays(30 - ($index % 15)),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('certification_documents')->insert($documentRows);
    }

    private function seedLocationAccess(): void
    {
        $privilegedIds = User::query()
            ->where(function ($query) {
                $query->where('is_sys_admin', true)
                    ->orWhere('is_admin', true)
                    ->orWhere('is_team_leader', true);
            })
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $grantedById = $privilegedIds[0] ?? null;
        $rows = [];
        $now = now();

        foreach (SystemLocation::orderBy('id')->pluck('id')->all() as $index => $locationId) {
            for ($i = 0; $i < 2; $i++) {
                $userId = $privilegedIds[($index + $i) % count($privilegedIds)];
                $rows[] = [
                    'location_id' => $locationId,
                    'user_id' => $userId,
                    'access_type' => ['code', 'card', 'key'][$i % 3],
                    'access_identifier' => sprintf('LOC-%03d-%02d', $locationId, $i + 1),
                    'access_granted_date' => now()->subDays(90 - ($index % 30))->toDateString(),
                    'access_expiry_date' => now()->addDays(180 + ($index % 30))->toDateString(),
                    'granted_by_id' => $grantedById,
                    'is_active' => true,
                    'notes' => 'Seeded facility access record.',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('system_location_access')->insert($rows);
    }

    private function seedTagsAndTaggables(): void
    {
        $tags = [
            ['name' => 'Adaptive', 'type' => 'theme', 'color' => '#0d6efd'],
            ['name' => 'Outdoor', 'type' => 'environment', 'color' => '#198754'],
            ['name' => 'Indoor', 'type' => 'environment', 'color' => '#6c757d'],
            ['name' => 'Global', 'type' => 'scope', 'color' => '#6610f2'],
            ['name' => 'Priority', 'type' => 'ops', 'color' => '#dc3545'],
        ];

        $tagIds = [];

        foreach ($tags as $tag) {
            $tagIds[$tag['name']] = SystemTag::updateOrCreate(
                ['name' => $tag['name'], 'type' => $tag['type'], 'parent_id' => null],
                ['color' => $tag['color']]
            )->id;
        }

        $moduleIds = SystemModule::whereIn('model_type', [Workout::class, Event::class, Equipment::class])
            ->pluck('id', 'model_type');

        $taggables = [];
        $now = now();

        foreach (Workout::orderBy('id')->limit(40)->get() as $workout) {
            $taggables[] = [
                'tag_id' => $tagIds['Adaptive'],
                'system_module_id' => $moduleIds[Workout::class],
                'taggable_type' => Workout::class,
                'taggable_id' => $workout->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $taggables[] = [
                'tag_id' => $tagIds[$workout->location_id % 2 === 0 ? 'Outdoor' : 'Indoor'],
                'system_module_id' => $moduleIds[Workout::class],
                'taggable_type' => Workout::class,
                'taggable_id' => $workout->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (Event::orderBy('id')->limit(40)->get() as $event) {
            $taggables[] = [
                'tag_id' => $tagIds['Global'],
                'system_module_id' => $moduleIds[Event::class],
                'taggable_type' => Event::class,
                'taggable_id' => $event->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (Equipment::orderBy('id')->limit(40)->get() as $equipment) {
            $taggables[] = [
                'tag_id' => $tagIds['Priority'],
                'system_module_id' => $moduleIds[Equipment::class],
                'taggable_type' => Equipment::class,
                'taggable_id' => $equipment->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('system_taggables')->insert($taggables);
    }

    private function seedNotifications(): void
    {
        $now = now();
        $rows = [];

        foreach (User::query()->where(function ($query) {
            $query->where('is_sys_admin', true)
                ->orWhere('is_admin', true)
                ->orWhere('is_team_leader', true);
        })->orderBy('id')->limit(30)->get() as $index => $user) {
            $rows[] = [
                'id' => (string) Str::uuid(),
                'type' => 'App\\Notifications\\SeededOperationalNotification',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode([
                    'title' => 'Seeded Operations Alert',
                    'message' => 'Demo data refresh completed for your chapter dashboard.',
                ]),
                'read_at' => $index % 3 === 0 ? now()->subHours(5) : null,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];
        }

        DB::table('system_notifications')->insert($rows);
    }

    private function seedMediaFiles(): void
    {
        $rows = [];
        $now = now();

        foreach (Workout::orderBy('id')->limit(10)->get() as $index => $workout) {
            $rows[] = [
                'model_type' => Workout::class,
                'model_id' => $workout->id,
                'collection_name' => 'images',
                'file_name' => sprintf('workout-%03d-banner.png', $index + 1),
                'mime_type' => 'image/png',
                'disk' => 'public',
                'size' => 245760,
                'metadata' => json_encode(['seeded' => true]),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (Equipment::orderBy('id')->limit(10)->get() as $index => $equipment) {
            $rows[] = [
                'model_type' => Equipment::class,
                'model_id' => $equipment->id,
                'collection_name' => 'manuals',
                'file_name' => sprintf('equipment-%03d-manual.pdf', $index + 1),
                'mime_type' => 'application/pdf',
                'disk' => 'public',
                'size' => 512000,
                'metadata' => json_encode(['seeded' => true]),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('system_media_files')->insert($rows);
    }

    private function seedAuditLogs(): void
    {
        $rows = [];
        $now = now();
        $users = User::orderBy('id')->limit(25)->pluck('id')->all();
        $workouts = Workout::orderBy('id')->limit(25)->pluck('id')->all();

        foreach ($workouts as $index => $workoutId) {
            $rows[] = [
                'user_id' => $users[$index % count($users)],
                'action' => $index % 2 === 0 ? 'created' : 'updated',
                'entity_type' => Workout::class,
                'entity_id' => $workoutId,
                'old_values' => json_encode($index % 2 === 0 ? [] : ['status' => 'draft']),
                'new_values' => json_encode(['status' => 'active', 'seeded' => true]),
                'ip_address' => '127.0.0.1',
                'created_at' => $now->copy()->subMinutes($index),
                'updated_at' => $now->copy()->subMinutes($index),
                'deleted_at' => null,
            ];
        }

        DB::table('system_audit_logs')->insert($rows);
    }
}
