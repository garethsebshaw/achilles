<?php

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\MeetingPoint;
use App\Models\SystemCategory;
use App\Models\SystemLocation;
use App\Models\SystemModule;
use App\Models\SystemStatus;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSession;
use App\Models\WorkoutSignup;
use App\Models\WorkoutSpecificDetails;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WorkoutSessionAndSignupSeeder extends Seeder
{
    private const FUTURE_WEEKS = 52;
    private const SIGNUP_WINDOW_WEEKS = 6;
    private const LOOKBACK_WEEKS = 2;

    public function run(): void
    {
        $workoutModuleId = SystemModule::where('model_type', Workout::class)->value('id');
        $assignmentModuleId = SystemModule::where('model_type', \App\Models\WorkoutEquipmentAssignment::class)->value('id');
        $specificDetailsModuleId = SystemModule::where('model_type', WorkoutSpecificDetails::class)->value('id');
        $sessionModuleId = SystemModule::where('model_type', WorkoutSession::class)->value('id');
        $signupModuleId = SystemModule::where('model_type', WorkoutSignup::class)->value('id');

        $sports = SystemCategory::where('system_module_id', $workoutModuleId)
            ->orderBy('name')
            ->get();
        $locations = SystemLocation::with(['chapter.country'])
            ->where('is_active', true)
            ->whereNotNull('chapter_id')
            ->orderBy('id')
            ->get();
        $creatorId = User::query()
            ->where(function ($query) {
                $query->where('is_sys_admin', true)
                    ->orWhere('is_admin', true);
            })
            ->orderBy('id')
            ->value('id');

        $sessionStatusIds = SystemStatus::where('system_module_id', $sessionModuleId)->pluck('id', 'code');
        $signupStatusIds = SystemStatus::where('system_module_id', $signupModuleId)->pluck('id', 'code');
        $unitStatusIds = SystemStatus::where('system_module_id', $specificDetailsModuleId)->pluck('id', 'code');
        $assignmentTypeIds = SystemCategory::where('system_module_id', $assignmentModuleId)->pluck('id', 'name');

        $athleteIds = User::where('is_subscribed', true)->where('is_athlete', true)->orderBy('id')->pluck('id')->all();
        $guideIds = User::where('is_subscribed', true)
            ->where(function ($query) {
                $query->where('is_guide', true)
                    ->orWhere('is_team_leader', true);
            })
            ->orderBy('id')
            ->pluck('id')
            ->all();

        $this->command?->info('Seeding workout templates and meeting points');
        $this->seedWorkoutTemplatesAndMeetingPoints($locations, $sports, $creatorId);
        $this->command?->info('Seeding workout sessions');
        $sessionRows = $this->seedWorkoutSessions($sessionStatusIds);
        $this->command?->info('Seeding workout session meeting points');
        $this->seedSessionMeetingPoints($sessionRows);
        $this->command?->info('Seeding workout signups, details, and equipment assignments');
        $this->seedSignupsAndDetails(
            $sessionRows,
            $signupStatusIds,
            $unitStatusIds,
            $assignmentTypeIds,
            $athleteIds,
            $guideIds
        );
    }

    private function seedWorkoutTemplatesAndMeetingPoints(Collection $locations, Collection $sports, int $creatorId): void
    {
        $now = now();

        foreach ($locations as $locationIndex => $location) {
            foreach ($sports as $sportIndex => $sport) {
                [$weekday, $startTime, $endTime] = $this->scheduleFor($sportIndex);

                $workout = Workout::create([
                    'location_id' => $location->id,
                    'activity_type_id' => $sport->id,
                    'version' => 1,
                    'name' => sprintf('%s %s Weekly Session', $location->chapter->name, $sport->name),
                    'description' => sprintf(
                        'Seeded recurring %s session for %s. This template drives weekly sessions for the next year.',
                        strtolower($sport->name),
                        $location->chapter->name
                    ),
                    'default_start_time' => $startTime,
                    'default_end_time' => $endTime,
                    'is_recurring' => true,
                    'recurrence_pattern' => sprintf('weekly:%d', $weekday),
                    'advance_create_weeks' => self::FUTURE_WEEKS,
                    'default_max_athletes' => 4 + ($sportIndex % 6),
                    'default_max_guides' => 3 + ($sportIndex % 4),
                    'is_template' => true,
                    'is_current_version' => true,
                    'metadata' => [
                        'seeded' => true,
                        'weekday' => $weekday,
                        'timezone' => $location->timezone,
                    ],
                    'created_by' => $creatorId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                $meetingPoint = MeetingPoint::create([
                    'name' => sprintf('%s %s Meetup', $location->chapter->city ?: $location->chapter->name, $sport->name),
                    'address' => trim(implode(', ', array_filter([
                        $location->address_line_1,
                        $location->city,
                        $location->state,
                        $location->postal_code,
                    ]))),
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'type_id' => $sport->id,
                    'created_by' => $creatorId,
                    'chapter_id' => $location->chapter_id,
                    'metadata' => [
                        'seeded' => true,
                        'location_id' => $location->id,
                        'sport' => $sport->name,
                    ],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('workout_template_meeting_points')->insert([
                    'workout_id' => $workout->id,
                    'meeting_point_id' => $meetingPoint->id,
                    'is_primary' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ]);
            }
        }
    }

    private function seedWorkoutSessions(Collection $sessionStatusIds): Collection
    {
        $now = now();
        $sessionRows = collect();

        $workouts = Workout::with(['location.chapter.country', 'activityType'])
            ->orderBy('id')
            ->get();

        foreach ($workouts as $workout) {
            $weekday = (int) ($workout->metadata['weekday'] ?? 1);
            $startOfWeek = CarbonImmutable::now($workout->location->timezone ?: 'UTC')->startOfWeek();
            $startTime = method_exists($workout->default_start_time, 'format')
                ? $workout->default_start_time->format('H:i:s')
                : (string) $workout->default_start_time;
            $endTime = method_exists($workout->default_end_time, 'format')
                ? $workout->default_end_time->format('H:i:s')
                : (string) $workout->default_end_time;

            for ($week = 0; $week <= self::FUTURE_WEEKS; $week++) {
                $sessionDate = $startOfWeek->addWeeks($week)->addDays($weekday - 1);
                $statusCode = $this->sessionStatusFor($sessionDate, $week, $workout->id);

                $sessionRows->push([
                    'workout_id' => $workout->id,
                    'workout_version' => $workout->version,
                    'location_id' => $workout->location_id,
                    'session_date' => $sessionDate->toDateString(),
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'max_athletes' => $workout->default_max_athletes,
                    'max_guides' => $workout->default_max_guides,
                    'status_id' => $sessionStatusIds[$statusCode] ?? reset($sessionStatusIds),
                    'weather_conditions' => json_encode([
                        'seeded' => true,
                        'sport' => $workout->activityType?->name,
                    ]),
                    'cancellation_reason' => $statusCode === 'session_cancelled' ? 'Seeded schedule variance for testing cancelled sessions.' : null,
                    'cancelled_by' => null,
                    'cancelled_at' => $statusCode === 'session_cancelled' ? now()->subDays(2)->toDateTimeString() : null,
                    'notes' => 'Seeded weekly recurring session.',
                    'metadata' => json_encode([
                        'seeded' => true,
                        'week_offset' => $week,
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        foreach ($sessionRows->chunk(1000) as $chunk) {
            DB::table('workout_sessions')->insert($chunk->all());
        }

        return DB::table('workout_sessions')
            ->select('id', 'workout_id', 'location_id', 'session_date')
            ->orderBy('id')
            ->get()
            ->map(function ($session) {
                $session->session_date = (string) $session->session_date;
                return $session;
            });
    }

    private function seedSessionMeetingPoints(Collection $sessions): void
    {
        $meetingPointIds = DB::table('workout_template_meeting_points')
            ->join('workouts', 'workouts.id', '=', 'workout_template_meeting_points.workout_id')
            ->select('workout_template_meeting_points.workout_id', 'workout_template_meeting_points.meeting_point_id')
            ->get()
            ->pluck('meeting_point_id', 'workout_id');

        $rows = [];
        $now = now();

        foreach ($sessions as $session) {
            $meetingPointId = $meetingPointIds[$session->workout_id] ?? null;

            if (! $meetingPointId) {
                continue;
            }

            $rows[] = [
                'workout_session_id' => $session->id,
                'meeting_point_id' => $meetingPointId,
                'is_primary' => true,
                'created_at' => $now,
                'updated_at' => $now,
                'deleted_at' => null,
            ];
        }

        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('workout_session_meeting_points')->insert($chunk);
        }
    }

    private function seedSignupsAndDetails(
        Collection $sessions,
        Collection $signupStatusIds,
        Collection $unitStatusIds,
        Collection $assignmentTypeIds,
        array $athleteIds,
        array $guideIds
    ): void {
        $recentSessions = DB::table('workout_sessions')
            ->select(
                'workout_sessions.id',
                'workout_sessions.location_id',
                'workout_sessions.session_date',
                'workouts.activity_type_id',
                'system_locations.chapter_id',
                'system_countries.iso2 as country_code',
                'system_categories.name as sport_name'
            )
            ->join('workouts', 'workouts.id', '=', 'workout_sessions.workout_id')
            ->join('system_locations', 'system_locations.id', '=', 'workout_sessions.location_id')
            ->leftJoin('system_chapters', 'system_chapters.id', '=', 'system_locations.chapter_id')
            ->leftJoin('system_countries', 'system_countries.id', '=', 'system_chapters.system_country_id')
            ->leftJoin('system_categories', 'system_categories.id', '=', 'workouts.activity_type_id')
            ->whereBetween('workout_sessions.session_date', [
                now()->subWeeks(self::LOOKBACK_WEEKS)->toDateString(),
                now()->addWeeks(self::SIGNUP_WINDOW_WEEKS)->toDateString(),
            ])
            ->orderBy('workout_sessions.id')
            ->get();

        $equipmentByLocationAndSport = $this->equipmentByLocationAndSport();
        $signups = [];
        $signupKeys = [];
        $now = now();

        foreach ($recentSessions as $sessionIndex => $session) {
            $athletesForSession = $this->participantSlice($athleteIds, $sessionIndex * 3, 2 + ($sessionIndex % 3));
            $guidesForSession = $this->participantSlice($guideIds, $sessionIndex * 2, 2 + ($sessionIndex % 2));

            foreach ($athletesForSession as $athleteIndex => $userId) {
                $statusCode = $this->signupStatusFor($session->session_date);
                $key = $session->id.'|'.$userId;
                $signupKeys[$key] = [
                    'role' => 'athlete',
                    'sport_name' => $session->sport_name,
                    'sport_category_id' => $session->activity_type_id,
                    'location_id' => $session->location_id,
                    'country_code' => $session->country_code,
                ];

                $signups[] = [
                    'workout_session_id' => $session->id,
                    'user_id' => $userId,
                    'athlete_id' => null,
                    'status_id' => $signupStatusIds[$statusCode] ?? reset($signupStatusIds),
                    'checked_in_at' => in_array($statusCode, ['signup_checked_in', 'signup_checked_out', 'signup_attended'], true)
                        ? CarbonImmutable::parse($session->session_date.' 08:50:00')->toDateTimeString()
                        : null,
                    'checked_out_at' => in_array($statusCode, ['signup_checked_out', 'signup_attended'], true)
                        ? CarbonImmutable::parse($session->session_date.' 10:20:00')->toDateTimeString()
                        : null,
                    'preferences' => json_encode([
                        'seeded' => true,
                        'role' => 'athlete',
                        'pace_group' => ['easy', 'steady', 'tempo'][$athleteIndex % 3],
                    ]),
                    'equipment_requirements' => json_encode([
                        'adaptive_support' => $this->requiresAdaptiveSupport($session->sport_name),
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ];
            }

            foreach ($guidesForSession as $guideIndex => $userId) {
                $pairedAthlete = $athletesForSession[$guideIndex % count($athletesForSession)];
                $statusCode = $this->signupStatusFor($session->session_date);
                $key = $session->id.'|'.$userId;
                $signupKeys[$key] = [
                    'role' => 'guide',
                    'sport_name' => $session->sport_name,
                    'sport_category_id' => $session->activity_type_id,
                    'location_id' => $session->location_id,
                    'country_code' => $session->country_code,
                ];

                $signups[] = [
                    'workout_session_id' => $session->id,
                    'user_id' => $userId,
                    'athlete_id' => $pairedAthlete,
                    'status_id' => $signupStatusIds[$statusCode] ?? reset($signupStatusIds),
                    'checked_in_at' => in_array($statusCode, ['signup_checked_in', 'signup_checked_out', 'signup_attended'], true)
                        ? CarbonImmutable::parse($session->session_date.' 08:45:00')->toDateTimeString()
                        : null,
                    'checked_out_at' => in_array($statusCode, ['signup_checked_out', 'signup_attended'], true)
                        ? CarbonImmutable::parse($session->session_date.' 10:25:00')->toDateTimeString()
                        : null,
                    'preferences' => json_encode([
                        'seeded' => true,
                        'role' => 'guide',
                        'paired_athlete' => $pairedAthlete,
                    ]),
                    'equipment_requirements' => json_encode([
                        'adaptive_support' => $this->requiresAdaptiveSupport($session->sport_name),
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ];
            }
        }

        foreach (array_chunk($signups, 1000) as $chunk) {
            DB::table('workout_signups')->insert($chunk);
        }

        $this->command?->info('Workout signups inserted');

        $detailRows = [];
        $assignmentRows = [];
        $signupSessionIds = $recentSessions->pluck('id')->all();

        DB::table('workout_signups')
            ->select('id', 'workout_session_id', 'user_id')
            ->whereIn('workout_session_id', $signupSessionIds)
            ->orderBy('id')
            ->chunkById(2000, function ($signupRows) use (
                &$detailRows,
                &$assignmentRows,
                $assignmentTypeIds,
                $equipmentByLocationAndSport,
                $now,
                $signupKeys,
                $unitStatusIds
            ) {
                foreach ($signupRows as $signupRow) {
                    $meta = $signupKeys[$signupRow->workout_session_id.'|'.$signupRow->user_id] ?? null;

                    if (! $meta) {
                        continue;
                    }

                    [$distanceUnitId, $paceUnitId, $speedUnitId] = $this->unitSetForCountry($meta['country_code'], $unitStatusIds);
                    $performance = $this->performanceProfileFor($meta['sport_name'], $meta['role']);

                    $detailRows[] = [
                        'workout_signup_id' => $signupRow->id,
                        'sport_category_id' => $meta['sport_category_id'],
                        'distance_unit_id' => $distanceUnitId,
                        'pace_unit_id' => $paceUnitId,
                        'speed_unit_id' => $speedUnitId,
                        'distance' => $performance['distance'],
                        'time' => $performance['time'],
                        'pace_min' => $performance['pace_min'],
                        'pace_max' => $performance['pace_max'],
                        'speed_min' => $performance['speed_min'],
                        'speed_max' => $performance['speed_max'],
                        'additional_details' => json_encode($performance['additional_details']),
                        'created_at' => $now,
                        'updated_at' => $now,
                        'deleted_at' => null,
                    ];

                    $equipmentId = $equipmentByLocationAndSport[$meta['location_id']][$meta['sport_name']] ?? null;

                    if ($equipmentId) {
                        $assignmentRows[] = [
                            'workout_signup_id' => $signupRow->id,
                            'equipment_id' => $equipmentId,
                            'assignment_type_id' => $assignmentTypeIds[$meta['role'] === 'athlete' ? 'Adaptive Equipment' : 'Shared Equipment']
                                ?? $assignmentTypeIds->first(),
                            'fitting_details' => json_encode([
                                'seeded' => true,
                                'role' => $meta['role'],
                            ]),
                            'created_at' => $now,
                            'updated_at' => $now,
                            'deleted_at' => null,
                        ];
                    }
                }

                if ($detailRows !== []) {
                    DB::table('workout_specific_details')->insert($detailRows);
                    $detailRows = [];
                }

                if ($assignmentRows !== []) {
                    DB::table('workout_equipment_assignments')->insert($assignmentRows);
                    $assignmentRows = [];
                }
            }, 'id');

        $this->command?->info('Workout specific details prepared');
        $this->command?->info('Workout specific details inserted');
        $this->command?->info('Workout equipment assignments inserted');
    }

    private function scheduleFor(int $index): array
    {
        $weekday = ($index % 7) + 1;
        $hour = 6 + (($index * 2) % 10);
        $minute = $index % 2 === 0 ? '00' : '30';
        $start = sprintf('%02d:%s:00', $hour, $minute);
        $end = sprintf('%02d:%s:00', min($hour + 2, 22), $minute);

        return [$weekday, $start, $end];
    }

    private function sessionStatusFor(CarbonImmutable $sessionDate, int $week, int $workoutId): string
    {
        if ($sessionDate->isPast()) {
            return 'session_completed';
        }

        if (($workoutId + $week) % 47 === 0) {
            return 'session_cancelled';
        }

        if (($workoutId + $week) % 59 === 0) {
            return 'session_weather';
        }

        return 'session_scheduled';
    }

    private function signupStatusFor(string $sessionDate): string
    {
        $date = CarbonImmutable::parse($sessionDate);

        if ($date->isPast()) {
            return $date->lt(now()->subDay()) ? 'signup_attended' : 'signup_checked_out';
        }

        if ($date->isToday()) {
            return 'signup_checked_in';
        }

        return 'signup_confirmed';
    }

    private function participantSlice(array $ids, int $offset, int $count): array
    {
        $slice = [];

        for ($i = 0; $i < $count; $i++) {
            $slice[] = $ids[($offset + $i) % count($ids)];
        }

        return array_values(array_unique($slice));
    }

    private function equipmentByLocationAndSport(): array
    {
        $equipmentRows = DB::table('equipment')
            ->join('system_categories', 'system_categories.id', '=', 'equipment.system_category_id')
            ->select('equipment.id', 'equipment.location_id', 'system_categories.name')
            ->get();

        $map = [];

        foreach ($equipmentRows as $row) {
            foreach ($this->sportsSupportedByCategory($row->name) as $sportName) {
                $map[$row->location_id][$sportName] = $map[$row->location_id][$sportName] ?? $row->id;
            }
        }

        return $map;
    }

    private function sportsSupportedByCategory(string $categoryName): array
    {
        return match ($categoryName) {
            'Tandem Bikes' => ['Cycling (Tandem)', 'Triathlon', 'ParaCycling'],
            'Hand Cycles' => ['Cycling (Hand Cycle)', 'Winter Handcycle', 'ParaCycling'],
            'Adaptive Skis' => ['Skiing (Alpine)', 'Skiing (Nordic/Cross-Country)', 'Sit-Ski'],
            'Wetsuits' => ['Swimming', 'Para-Swimming', 'Adaptive Water Polo', 'Triathlon'],
            'Adaptive Kayaks' => ['Kayaking (Adaptive)', 'Dragon Boat Racing (Adaptive)'],
            'Helmets' => ['Wheelchair Rugby', 'Wheelchair Basketball', 'Adaptive Skateboarding', 'Rock Climbing (Adaptive)'],
            'First Aid Equipment' => ['Running', 'Walking', 'Hiking', 'Orienteering'],
            'Mobility Aids' => ['Racing Wheelchair', 'Wheelchair Tennis', 'Power Soccer', 'Goalball', 'Seated Volleyball'],
            default => [],
        };
    }

    private function unitSetForCountry(?string $countryCode, Collection $unitStatusIds): array
    {
        if ($countryCode === 'US') {
            return [
                $unitStatusIds['unit_miles'],
                $unitStatusIds['pace_min_mile'],
                $unitStatusIds['speed_mph'],
            ];
        }

        return [
            $unitStatusIds['unit_kilometers'],
            $unitStatusIds['pace_min_km'],
            $unitStatusIds['speed_kmh'],
        ];
    }

    private function performanceProfileFor(string $sportName, string $role): array
    {
        $isDistanceSport = ! in_array($sportName, [
            'Boccia',
            'Goalball',
            'Power Soccer',
            'Wheelchair Basketball',
            'Wheelchair Rugby',
            'Wheelchair Tennis',
            'Seated Volleyball',
            'Adaptive Dance',
            'Adaptive Martial Arts',
        ], true);

        $distance = $isDistanceSport ? ($role === 'guide' ? 5.0 : 4.0) : null;
        $time = $isDistanceSport ? ($role === 'guide' ? 55.0 : 60.0) : 90.0;
        $paceMin = $isDistanceSport ? ($role === 'guide' ? 8.5 : 9.5) : null;
        $paceMax = $isDistanceSport ? ($role === 'guide' ? 11.0 : 12.0) : null;
        $speedMin = str_contains($sportName, 'Cycling') || str_contains($sportName, 'Kayak') || str_contains($sportName, 'Dragon Boat')
            ? ($role === 'guide' ? 9.0 : 7.0)
            : null;
        $speedMax = $speedMin ? $speedMin + 4.0 : null;

        return [
            'distance' => $distance,
            'time' => $time,
            'pace_min' => $paceMin,
            'pace_max' => $paceMax,
            'speed_min' => $speedMin,
            'speed_max' => $speedMax,
            'additional_details' => [
                'seeded' => true,
                'role' => $role,
                'sport' => $sportName,
            ],
        ];
    }

    private function requiresAdaptiveSupport(string $sportName): bool
    {
        return str_contains($sportName, 'Adaptive')
            || str_contains($sportName, 'Para')
            || str_contains($sportName, 'Wheelchair')
            || in_array($sportName, ['Cycling (Hand Cycle)', 'Sit-Ski', 'Goalball', 'Boccia'], true);
    }
}
