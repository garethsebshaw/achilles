<?php

namespace App\Support\DemoData;

use App\Models\SystemStatus;
use App\Models\User;
use App\Models\WorkoutSession;
use App\Models\WorkoutSignup;
use Illuminate\Support\Collection;

class DemoSessionSignupGenerator
{
    public function generateForSessions(iterable $sessions, array $options = []): array
    {
        $minimumAthletes = max(1, (int) ($options['minimum_athletes'] ?? 8));
        $maximumAthletes = max($minimumAthletes, (int) ($options['maximum_athletes'] ?? 24));
        $maximumGuidesPerAthlete = max(1, (int) ($options['maximum_guides_per_athlete'] ?? 3));
        $heavySessionMode = (bool) ($options['heavy_session_mode'] ?? false);

        $createdAthletes = 0;
        $createdGuides = 0;
        $processedSessions = 0;

        foreach (Collection::wrap($sessions) as $session) {
            if (! $session instanceof WorkoutSession) {
                continue;
            }

            $result = $this->generateForSession(
                $session->loadMissing(['location.chapter.country', 'workout.activityType']),
                $minimumAthletes,
                $maximumAthletes,
                $maximumGuidesPerAthlete,
                $heavySessionMode,
            );

            $processedSessions++;
            $createdAthletes += $result['athletes'];
            $createdGuides += $result['guides'];
        }

        return [
            'sessions' => $processedSessions,
            'athletes' => $createdAthletes,
            'guides' => $createdGuides,
            'signups' => $createdAthletes + $createdGuides,
        ];
    }

    public function generateForSession(
        WorkoutSession $session,
        int $minimumAthletes,
        int $maximumAthletes,
        int $maximumGuidesPerAthlete,
        bool $heavySessionMode = false,
    ): array {
        $existingSignups = WorkoutSignup::query()
            ->where('workout_session_id', $session->id)
            ->get(['user_id', 'athlete_id']);

        $existingUserIds = $existingSignups->pluck('user_id')->map(fn ($id) => (int) $id)->all();
        $existingAthleteIds = $existingSignups
            ->filter(fn ($signup) => is_null($signup->athlete_id))
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        $athleteTarget = $heavySessionMode
            ? random_int(max($minimumAthletes, 60), max($maximumAthletes, 140))
            : random_int($minimumAthletes, $maximumAthletes);

        $athletePool = $this->participantPoolForSession($session, 'athlete', $existingUserIds);
        $guidePool = $this->participantPoolForSession($session, 'guide', $existingUserIds);

        $athletesToCreate = max(0, $athleteTarget - count($existingAthleteIds));
        $selectedAthletes = $athletePool->take($athletesToCreate)->values();
        $athleteIds = array_values(array_unique(array_merge(
            $existingAthleteIds,
            $selectedAthletes->pluck('id')->map(fn ($id) => (int) $id)->all(),
        )));

        $createdAthletes = $this->createAthleteSignups($session, $selectedAthletes);

        $availableGuides = $guidePool->values();
        $guideAssignments = $this->plannedGuideAssignments(
            $athleteIds,
            $availableGuides->count(),
            $maximumGuidesPerAthlete,
            $heavySessionMode,
        );

        $createdGuides = $this->createGuideSignups($session, $availableGuides, $guideAssignments);

        return [
            'athletes' => $createdAthletes,
            'guides' => $createdGuides,
        ];
    }

    protected function createAthleteSignups(WorkoutSession $session, Collection $athletes): int
    {
        $statusId = $this->signupStatusIdFor($session);
        $created = 0;

        foreach ($athletes as $athlete) {
            WorkoutSignup::query()->create([
                'workout_session_id' => $session->id,
                'user_id' => $athlete->id,
                'athlete_id' => null,
                'status_id' => $statusId,
                'preferences' => [
                    'demo_seeded' => true,
                    'role' => 'athlete',
                ],
                'equipment_requirements' => [
                    'adaptive_support' => true,
                ],
            ]);

            $created++;
        }

        return $created;
    }

    protected function createGuideSignups(WorkoutSession $session, Collection $guides, array $guideAssignments): int
    {
        $statusId = $this->signupStatusIdFor($session);
        $created = 0;
        $guideIndex = 0;

        foreach ($guideAssignments as $athleteId) {
            $guide = $guides->get($guideIndex);

            if (! $guide) {
                break;
            }

            WorkoutSignup::query()->create([
                'workout_session_id' => $session->id,
                'user_id' => $guide->id,
                'athlete_id' => $athleteId,
                'status_id' => $statusId,
                'preferences' => [
                    'demo_seeded' => true,
                    'role' => 'guide',
                    'paired_athlete' => $athleteId,
                ],
                'equipment_requirements' => [
                    'adaptive_support' => true,
                ],
            ]);

            $guideIndex++;
            $created++;
        }

        return $created;
    }

    protected function plannedGuideAssignments(
        array $athleteIds,
        int $availableGuideCount,
        int $maximumGuidesPerAthlete,
        bool $heavySessionMode,
    ): array {
        if ($athleteIds === [] || $availableGuideCount === 0) {
            return [];
        }

        $assignments = [];
        $remainingGuides = $availableGuideCount;

        foreach ($athleteIds as $index => $athleteId) {
            if ($remainingGuides <= 0) {
                break;
            }

            $minimumGuides = $remainingGuides >= (count($athleteIds) - $index) ? 1 : 0;
            $preferredMaximum = $heavySessionMode ? max($maximumGuidesPerAthlete, 4) : $maximumGuidesPerAthlete;
            $guideCount = min($remainingGuides, random_int($minimumGuides, $preferredMaximum));

            for ($i = 0; $i < $guideCount; $i++) {
                $assignments[] = $athleteId;
            }

            $remainingGuides -= $guideCount;
        }

        shuffle($assignments);

        return $assignments;
    }

    protected function participantPoolForSession(
        WorkoutSession $session,
        string $role,
        array $excludedUserIds = [],
    ): Collection {
        $chapterId = $session->location?->chapter_id;
        $roleColumn = $role === 'guide' ? 'is_guide' : 'is_athlete';

        $chapterScopedUserIds = WorkoutSignup::query()
            ->select('workout_signups.user_id')
            ->join('workout_sessions', 'workout_sessions.id', '=', 'workout_signups.workout_session_id')
            ->join('system_locations', 'system_locations.id', '=', 'workout_sessions.location_id')
            ->join('users', 'users.id', '=', 'workout_signups.user_id')
            ->whereNull('workout_signups.deleted_at')
            ->whereNull('workout_sessions.deleted_at')
            ->whereNull('users.deleted_at')
            ->where("users.{$roleColumn}", true)
            ->when($chapterId, fn ($query) => $query->where('system_locations.chapter_id', $chapterId))
            ->distinct()
            ->pluck('workout_signups.user_id');

        $preferredPool = User::query()
            ->whereNull('deleted_at')
            ->where($roleColumn, true)
            ->whereIn('id', $chapterScopedUserIds)
            ->whereNotIn('id', $excludedUserIds)
            ->inRandomOrder()
            ->get(['id', 'name']);

        if ($preferredPool->isNotEmpty()) {
            return $preferredPool;
        }

        return User::query()
            ->whereNull('deleted_at')
            ->where($roleColumn, true)
            ->whereNotIn('id', $excludedUserIds)
            ->inRandomOrder()
            ->get(['id', 'name']);
    }

    protected function signupStatusIdFor(WorkoutSession $session): ?int
    {
        $sessionDate = $session->session_date?->toDateString();
        $today = now()->toDateString();

        if ($sessionDate && $sessionDate < $today) {
            return SystemStatus::idForModel(WorkoutSignup::class, 'signup_attended', ['signup_checked_out', 'signup_checked_in']);
        }

        if ($sessionDate === $today) {
            return SystemStatus::idForModel(WorkoutSignup::class, 'signup_checked_in', ['signup_confirmed', 'signup_pending']);
        }

        return SystemStatus::idForModel(WorkoutSignup::class, 'signup_confirmed', ['signup_pending']);
    }
}
