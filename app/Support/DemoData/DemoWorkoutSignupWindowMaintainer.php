<?php

namespace App\Support\DemoData;

use App\Models\SystemStatus;
use App\Models\WorkoutSession;
use App\Models\WorkoutSignup;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DemoWorkoutSignupWindowMaintainer
{
    public function __construct(
        private readonly DemoSessionSignupGenerator $generator,
    ) {
    }

    public function redistributeExistingHistory(int $historyDays, int $futureDays, bool $dryRun = false): array
    {
        $updated = 0;

        WorkoutSignup::query()
            ->select([
                'workout_signups.id as id',
                'workout_signups.checked_in_at',
                'workout_signups.checked_out_at',
                'workout_sessions.session_date',
                'workout_sessions.start_time',
            ])
            ->join('workout_sessions', 'workout_sessions.id', '=', 'workout_signups.workout_session_id')
            ->whereBetween('workout_sessions.session_date', [
                now()->subDays($historyDays)->toDateString(),
                now()->addDays($futureDays)->toDateString(),
            ])
            ->orderBy('workout_signups.id')
            ->chunkById(100, function ($rows) use (&$updated, $dryRun) {
                $updates = [];

                foreach ($rows as $row) {
                    $sessionStart = $this->sessionStartFromRow($row->session_date, $row->start_time);
                    $createdAt = $this->createdAtForSession($sessionStart, $row->id);
                    $updatedAt = $this->updatedAtForSignup($createdAt, $row->checked_in_at, $row->checked_out_at);

                    $updates[] = [
                        'id' => $row->id,
                        'created_at' => $createdAt->toDateTimeString(),
                        'updated_at' => $updatedAt->toDateTimeString(),
                    ];
                }

                if (! $dryRun && $updates !== []) {
                    $this->batchUpdateSignups($updates, ['created_at', 'updated_at']);
                }

                $updated += count($updates);
            }, 'workout_signups.id', 'id');

        return ['updated_signups' => $updated];
    }

    public function backfillHistoricSessions(int $historyDays, bool $dryRun = false): array
    {
        $sessions = $this->candidateSessions(
            now()->subDays($historyDays)->toDateString(),
            now()->yesterday()->toDateString()
        );

        return $this->fillSessions($sessions, false, $dryRun);
    }

    public function maintainUpcomingSessions(int $futureDays, bool $dryRun = false): array
    {
        $sessions = $this->candidateSessions(
            now()->toDateString(),
            now()->addDays($futureDays)->toDateString()
        );

        return $this->fillSessions($sessions, true, $dryRun);
    }

    private function fillSessions(EloquentCollection $sessions, bool $upcoming, bool $dryRun): array
    {
        $processed = 0;
        $created = 0;

        foreach ($sessions as $session) {
            $processed++;
            $athleteTarget = $this->targetAthleteCount($session, $upcoming);
            $guideMultiplier = $this->guideMultiplierFor($session);
            $desiredGuideCount = (int) round($athleteTarget * $guideMultiplier);

            $existingAthletes = WorkoutSignup::query()
                ->where('workout_session_id', $session->id)
                ->whereNull('athlete_id')
                ->distinct('user_id')
                ->count('user_id');
            $existingGuides = WorkoutSignup::query()
                ->where('workout_session_id', $session->id)
                ->whereNotNull('athlete_id')
                ->distinct('user_id')
                ->count('user_id');

            $neededAthletes = max(0, $athleteTarget - $existingAthletes);
            $neededGuides = max(0, $desiredGuideCount - $existingGuides);

            if ($neededAthletes === 0 && $neededGuides === 0) {
                continue;
            }

            $beforeMaxId = (int) WorkoutSignup::query()
                ->where('workout_session_id', $session->id)
                ->max('id');

            if (! $dryRun) {
                $this->generator->generateForSession(
                    $session->loadMissing(['location.chapter.country', 'workout.activityType']),
                    max(1, $neededAthletes),
                    max(1, $neededAthletes),
                    min((int) config('demo_activity.guides.maximum_per_athlete', 3), max(1, $neededGuides)),
                    $athleteTarget >= 70,
                    $neededGuides,
                );

                $newSignups = WorkoutSignup::query()
                    ->where('workout_session_id', $session->id)
                    ->where('id', '>', $beforeMaxId)
                    ->orderBy('id')
                    ->get();

                $created += $newSignups->count();
                $this->retimeNewSignups($session, $newSignups, $upcoming);
            } else {
                $created += ($neededAthletes + $neededGuides);
            }
        }

        return [
            'processed_sessions' => $processed,
            'created_signups' => $created,
        ];
    }

    private function candidateSessions(string $fromDate, string $toDate): EloquentCollection
    {
        $scheduledStatuses = array_filter([
            SystemStatus::idForModel(WorkoutSession::class, 'session_scheduled'),
            SystemStatus::idForModel(WorkoutSession::class, 'session_completed'),
            SystemStatus::idForModel(WorkoutSession::class, 'session_weather'),
        ]);

        return WorkoutSession::query()
            ->with(['location.chapter.country', 'workout.activityType'])
            ->whereBetween('session_date', [$fromDate, $toDate])
            ->when($scheduledStatuses !== [], fn ($query) => $query->whereIn('status_id', $scheduledStatuses))
            ->orderBy('session_date')
            ->orderBy('id')
            ->get();
    }

    private function targetAthleteCount(WorkoutSession $session, bool $upcoming): int
    {
        $minimum = (int) config('demo_activity.athletes.minimum', 10);
        $maximum = (int) config('demo_activity.athletes.maximum', 150);
        $heavyChance = (float) config('demo_activity.athletes.heavy_session_chance', 0.12);
        $seed = abs(crc32('session-target|'.$session->id.'|'.$session->session_date?->toDateString()));
        $isHeavy = ($seed % 1000) / 1000 <= $heavyChance;

        $baseMinimum = $isHeavy ? max(24, $minimum) : $minimum;
        $baseMaximum = $isHeavy ? $maximum : min(40, $maximum);
        $rawTarget = $baseMinimum + ($seed % max(1, $baseMaximum - $baseMinimum + 1));

        if (! $upcoming) {
            return $rawTarget;
        }

        $daysOut = max(0, now()->startOfDay()->diffInDays($session->session_date, false));
        $window = max(1, (int) config('demo_activity.future_days', 28));
        $fillFactor = max(0.12, 0.78 - (($daysOut / $window) * 0.56));

        return max($minimum, (int) ceil($rawTarget * $fillFactor));
    }

    private function guideMultiplierFor(WorkoutSession $session): float
    {
        $minimum = (float) config('demo_activity.guides.minimum_multiplier', 0.9);
        $maximum = (float) config('demo_activity.guides.maximum_multiplier', 2.0);
        $seed = abs(crc32('guide-multiplier|'.$session->id));
        $ratio = ($seed % 1000) / 1000;

        return $minimum + (($maximum - $minimum) * $ratio);
    }

    private function retimeNewSignups(WorkoutSession $session, Collection $signups, bool $upcoming): void
    {
        if ($signups->isEmpty()) {
            return;
        }

        $sessionStart = $this->sessionStart($session);
        $attendedStatuses = array_filter([
            SystemStatus::idForModel(WorkoutSignup::class, 'signup_attended', ['signup_checked_out', 'signup_checked_in']),
            SystemStatus::idForModel(WorkoutSignup::class, 'signup_checked_out'),
            SystemStatus::idForModel(WorkoutSignup::class, 'signup_checked_in'),
            SystemStatus::idForModel(WorkoutSignup::class, 'signup_confirmed', ['signup_pending']),
        ]);

        $updates = [];

        foreach ($signups as $signup) {
            $createdAt = $upcoming
                ? $this->createdAtForUpcomingSession($sessionStart, $signup->id)
                : $this->createdAtForSession($sessionStart, $signup->id);
            $checkedInAt = $upcoming ? null : $sessionStart->subMinutes(10);
            $checkedOutAt = $upcoming ? null : $sessionStart->addMinutes(95);
            $statusId = $upcoming
                ? SystemStatus::idForModel(WorkoutSignup::class, 'signup_confirmed', ['signup_pending'])
                : $this->historicStatusFor($signup->id, $attendedStatuses);
            $updatedAt = $this->updatedAtForSignup($createdAt, $checkedInAt, $checkedOutAt);

            $updates[] = [
                'id' => $signup->id,
                'created_at' => $createdAt->toDateTimeString(),
                'updated_at' => $updatedAt->toDateTimeString(),
                'checked_in_at' => $checkedInAt?->toDateTimeString(),
                'checked_out_at' => $checkedOutAt?->toDateTimeString(),
                'status_id' => $statusId,
            ];
        }

        $this->batchUpdateSignups($updates, ['created_at', 'updated_at', 'checked_in_at', 'checked_out_at', 'status_id']);
    }

    private function historicStatusFor(int $signupId, array $statusIds): ?int
    {
        $attended = $statusIds[0] ?? null;
        $checkedOut = $statusIds[1] ?? $attended;
        $checkedIn = $statusIds[2] ?? $checkedOut;

        return match ($signupId % 6) {
            0 => $checkedIn,
            1 => $checkedOut,
            default => $attended,
        };
    }

    private function createdAtForSession(CarbonImmutable $sessionStart, int $seed): CarbonImmutable
    {
        $leadDays = 1 + ($seed % 84);
        $leadHours = 2 + ($seed % 10);
        $candidate = $sessionStart->subDays($leadDays)->subHours($leadHours);

        return $candidate->lessThan($sessionStart->subHour())
            ? $candidate
            : $sessionStart->subHour();
    }

    private function createdAtForUpcomingSession(CarbonImmutable $sessionStart, int $seed): CarbonImmutable
    {
        $latest = min(now()->timestamp, $sessionStart->subHour()->timestamp);
        $earliest = max(now()->subDays(14)->timestamp, $sessionStart->subDays(60)->timestamp);

        if ($latest <= $earliest) {
            return CarbonImmutable::createFromTimestamp($earliest);
        }

        $offset = abs(crc32('upcoming|'.$seed.'|'.$sessionStart->toDateTimeString())) % ($latest - $earliest);

        return CarbonImmutable::createFromTimestamp($earliest + $offset);
    }

    private function updatedAtForSignup(CarbonImmutable $createdAt, mixed $checkedInAt, mixed $checkedOutAt): CarbonImmutable
    {
        $timestamps = [
            $createdAt->timestamp,
        ];

        if ($checkedInAt) {
            $timestamps[] = CarbonImmutable::parse($checkedInAt)->timestamp;
        }

        if ($checkedOutAt) {
            $timestamps[] = CarbonImmutable::parse($checkedOutAt)->timestamp;
        }

        return CarbonImmutable::createFromTimestamp(max($timestamps));
    }

    private function sessionStart(WorkoutSession $session): CarbonImmutable
    {
        return $this->sessionStartFromRow(
            $session->session_date?->toDateString(),
            $session->getRawOriginal('start_time') ?: '09:00:00'
        );
    }

    private function sessionStartFromRow(?string $date, ?string $startTime): CarbonImmutable
    {
        $normalizedDate = $date
            ? CarbonImmutable::parse($date)->toDateString()
            : now()->toDateString();

        return CarbonImmutable::parse(trim($normalizedDate.' '.($startTime ?: '09:00:00')));
    }

    /**
     * @param  array<int, array<string, mixed>>  $updates
     * @param  array<int, string>  $columns
     */
    private function batchUpdateSignups(array $updates, array $columns): void
    {
        $bindings = [];
        $assignments = [];
        $ids = array_column($updates, 'id');

        foreach ($columns as $column) {
            $case = "{$column} = CASE id";

            foreach ($updates as $row) {
                $case .= ' WHEN ? THEN ?';
                $bindings[] = $row['id'];
                $bindings[] = $row[$column] ?? null;
            }

            $case .= " ELSE {$column} END";
            $assignments[] = $case;
        }

        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $bindings = array_merge($bindings, $ids);

        DB::update(
            'UPDATE workout_signups SET '.implode(', ', $assignments)." WHERE id IN ({$placeholders})",
            $bindings
        );
    }
}
