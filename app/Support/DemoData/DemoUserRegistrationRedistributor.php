<?php

namespace App\Support\DemoData;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoUserRegistrationRedistributor
{
    private const INSERT_CHUNK_SIZE = 500;

    private const FIRST_NAMES = [
        'Alex', 'Jordan', 'Taylor', 'Morgan', 'Casey', 'Riley', 'Avery', 'Parker', 'Skyler', 'Hayden',
        'Quinn', 'Dakota', 'Reese', 'Cameron', 'Kendall', 'Logan', 'Rowan', 'Sydney', 'Emerson', 'Finley',
    ];

    private const LAST_NAMES = [
        'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Wilson', 'Anderson',
        'Thomas', 'Jackson', 'White', 'Harris', 'Martin', 'Thompson', 'Moore', 'Clark', 'Lewis', 'Walker',
    ];

    private const EMAIL_DOMAINS = [
        'gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'aol.com', 'icloud.com',
    ];

    private const PHONE_AREA_CODES = ['201', '212', '305', '312', '415', '512', '617', '718', '917', '973'];

    public function redistributeHistory(int $days, bool $dryRun = false): array
    {
        $userQuery = User::query()
            ->whereNull('deleted_at')
            ->where('is_sys_admin', false)
            ->where('is_admin', false)
            ->orderBy('id');

        $total = (clone $userQuery)->count();

        if ($total === 0) {
            return ['users' => 0, 'days' => $days];
        }

        $start = CarbonImmutable::today()->subDays(max(29, $days - 1));
        $allocations = $this->allocateAcrossDays($total, $days, 'users');
        $currentOffset = 0;
        $remainingForDay = (int) ($allocations[$currentOffset] ?? 0);
        $indexWithinDay = 0;
        $updates = [];

        $userQuery->chunkById(100, function ($users) use (
            $allocations,
            $dryRun,
            $start,
            &$currentOffset,
            &$remainingForDay,
            &$indexWithinDay,
            &$updates
        ) {
            foreach ($users as $user) {
                while ($remainingForDay < 1 && $currentOffset < ($allocations->count() - 1)) {
                    $currentOffset++;
                    $remainingForDay = (int) ($allocations[$currentOffset] ?? 0);
                    $indexWithinDay = 0;
                }

                $day = $start->addDays($currentOffset);
                $createdAt = $this->randomMomentWithinDay($day, $user->id + $indexWithinDay);
                $verifiedAt = $user->email_verified_at
                    ? $createdAt->addMinutes(($user->id * 19) % 960)
                    : null;
                $updatedAt = min(
                    $createdAt->addDays(($user->id % 14))->timestamp,
                    now()->timestamp
                );

                $updates[] = [
                    'id' => $user->id,
                    'created_at' => $createdAt->toDateTimeString(),
                    'updated_at' => CarbonImmutable::createFromTimestamp($updatedAt)->toDateTimeString(),
                    'email_verified_at' => $verifiedAt?->toDateTimeString(),
                ];

                $remainingForDay--;
                $indexWithinDay++;
            }

            if (! $dryRun && $updates !== []) {
                $this->batchUpdateUsers($updates);
            }

            $updates = [];
        });

        return [
            'users' => $total,
            'days' => $days,
        ];
    }

    public function createTodayUsers(int $minimum, int $maximum, bool $dryRun = false): array
    {
        $count = random_int(max(0, $minimum), max($minimum, $maximum));

        if ($count === 0) {
            return ['users' => 0];
        }

        $existingUsers = (int) DB::table('users')->count();
        $rows = [];

        for ($i = 0; $i < $count; $i++) {
            $sequence = $existingUsers + $i + 1;
            $createdAt = $this->randomMomentWithinDay(CarbonImmutable::today(), $sequence);
            $updatedAt = $createdAt->addMinutes(($sequence * 7) % 180);
            $emailVerifiedAt = random_int(1, 100) <= 90
                ? $createdAt->addMinutes(($sequence * 11) % 720)
                : null;

            $firstName = self::FIRST_NAMES[array_rand(self::FIRST_NAMES)];
            $lastName = self::LAST_NAMES[array_rand(self::LAST_NAMES)];
            $profile = $this->userTypeProfile();

            $rows[] = [
                'name' => $firstName.' '.$lastName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $this->emailFor($firstName, $lastName, $sequence),
                'created_at' => $createdAt->toDateTimeString(),
                'email_verified_at' => $emailVerifiedAt?->toDateTimeString(),
                'updated_at' => $updatedAt->toDateTimeString(),
                'is_sys_admin' => $profile['is_sys_admin'],
                'is_admin' => $profile['is_admin'],
                'is_team_leader' => $profile['is_team_leader'],
                'is_athlete' => $profile['is_athlete'],
                'is_guide' => $profile['is_guide'],
                'is_subscribed' => random_int(1, 100) <= 70,
                'password' => Hash::make('password123'),
                'picture' => '',
                'phone' => $this->randomPhoneNumber(),
                'preferred_name' => $firstName,
            ];
        }

        if (! $dryRun) {
            foreach (array_chunk($rows, self::INSERT_CHUNK_SIZE) as $chunk) {
                DB::table('users')->insert($chunk);
            }
        }

        return ['users' => $count];
    }

    /**
     * @return Collection<int, int>
     */
    private function allocateAcrossDays(int $total, int $days, string $seed): Collection
    {
        $weights = [];
        $weightTotal = 0.0;

        foreach (range(0, $days - 1) as $offset) {
            $date = CarbonImmutable::today()->subDays($days - 1 - $offset);
            $weekdayFactor = match ($date->dayOfWeekIso) {
                2, 3, 4 => 1.12,
                5 => 1.06,
                6, 7 => 0.78,
                default => 0.92,
            };
            $burstFactor = ((abs(crc32($seed.'|'.$date->toDateString())) % 17) === 0) ? 0.0 : 1.0;
            $seasonalFactor = 0.92 + (0.22 * sin((($offset + 1) / max(1, $days)) * M_PI * 4));
            $recencyFactor = 0.8 + (($offset / max(1, $days - 1)) * 0.5);
            $noiseFactor = 0.85 + ((abs(crc32('noise|'.$seed.'|'.$date->toDateString())) % 28) / 100);
            $weight = max(0.0, $weekdayFactor * $seasonalFactor * $recencyFactor * $noiseFactor * $burstFactor);

            $weights[$offset] = $weight;
            $weightTotal += $weight;
        }

        if ($weightTotal <= 0.0) {
            return collect(array_fill(0, $days, 0));
        }

        $wholeValues = [];
        $fractions = [];
        $allocated = 0;

        foreach ($weights as $offset => $weight) {
            $raw = ($weight / $weightTotal) * $total;
            $whole = (int) floor($raw);
            $wholeValues[$offset] = $whole;
            $fractions[$offset] = $raw - $whole;
            $allocated += $whole;
        }

        $remaining = $total - $allocated;
        arsort($fractions);

        foreach (array_keys($fractions) as $offset) {
            if ($remaining <= 0) {
                break;
            }

            $wholeValues[$offset]++;
            $remaining--;
        }

        ksort($wholeValues);

        return collect($wholeValues);
    }

    private function randomMomentWithinDay(CarbonImmutable $day, int $seed): CarbonImmutable
    {
        $seconds = abs(crc32('time|'.$seed.'|'.$day->toDateString())) % 86400;

        return $day->startOfDay()->addSeconds($seconds);
    }

    /**
     * @return array{is_athlete: bool, is_guide: bool, is_sys_admin: bool, is_admin: bool, is_team_leader: bool}
     */
    private function userTypeProfile(): array
    {
        $rand = mt_rand(1, 1000);

        $profile = [
            'is_athlete' => $rand <= 260,
            'is_guide' => $rand > 260,
            'is_sys_admin' => false,
            'is_admin' => false,
            'is_team_leader' => false,
        ];

        if (! $profile['is_athlete']) {
            $profile['is_team_leader'] = $rand >= 267 && $rand <= 282;
        }

        return $profile;
    }

    private function randomPhoneNumber(): string
    {
        $areaCode = self::PHONE_AREA_CODES[array_rand(self::PHONE_AREA_CODES)];
        $prefix = str_pad((string) random_int(200, 999), 3, '0', STR_PAD_LEFT);
        $lineNumber = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        return sprintf('%s-%s-%s', $areaCode, $prefix, $lineNumber);
    }

    private function emailFor(string $firstName, string $lastName, int $sequence): string
    {
        $normalized = strtolower(preg_replace('/[^a-z0-9]+/i', '.', $firstName.'.'.$lastName) ?? 'user');
        $normalized = trim($normalized, '.');

        return $normalized.$sequence.'@'.self::EMAIL_DOMAINS[$sequence % count(self::EMAIL_DOMAINS)];
    }

    /**
     * @param  array<int, array{id:int,created_at:string,updated_at:string,email_verified_at:?string}>  $updates
     */
    private function batchUpdateUsers(array $updates): void
    {
        $columns = ['created_at', 'updated_at', 'email_verified_at'];
        $bindings = [];
        $assignments = [];
        $ids = array_column($updates, 'id');

        foreach ($columns as $column) {
            $case = "{$column} = CASE id";

            foreach ($updates as $row) {
                $case .= ' WHEN ? THEN ?';
                $bindings[] = $row['id'];
                $bindings[] = $row[$column];
            }

            $case .= " ELSE {$column} END";
            $assignments[] = $case;
        }

        $placeholders = implode(', ', array_fill(0, count($ids), '?'));
        $bindings = array_merge($bindings, $ids);

        DB::update(
            'UPDATE users SET '.implode(', ', $assignments)." WHERE id IN ({$placeholders})",
            $bindings
        );
    }
}
