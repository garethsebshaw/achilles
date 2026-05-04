<?php

namespace Database\Seeders\Users;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRandomSeeder extends Seeder
{
    private const DEFAULT_TOTAL_USERS = 151000;
    // Keep MySQL bulk inserts below the prepared statement placeholder limit.
    private const INSERT_CHUNK_SIZE = 1000;

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

    public function run()
    {
        $this->command?->getOutput()->setVerbosity(\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_QUIET);

        $startDate = Carbon::create(2010, 1, 1);
        $today = Carbon::now();
        $existingUsers = (int) DB::table('users')->count();
        // Never let Cloud or local demo rebuilds silently shrink below the supported
        // large-dataset baseline just because an environment variable is stale.
        $configuredTargetUsers = (int) env('SEED_TOTAL_USERS', self::DEFAULT_TOTAL_USERS);
        $strictTarget = filter_var(env('SEED_TOTAL_USERS_STRICT', false), FILTER_VALIDATE_BOOL);
        $targetTotalUsers = $strictTarget
            ? max($configuredTargetUsers, $existingUsers)
            : max(
                $configuredTargetUsers,
                self::DEFAULT_TOTAL_USERS,
                $existingUsers
            );
        $usersToCreate = max(0, $targetTotalUsers - $existingUsers);

        if ($usersToCreate === 0) {
            return;
        }

        $rows = [];

        for ($i = 0; $i < $usersToCreate; $i++) {
            $sequence = $existingUsers + $i + 1;
            $createdAtCarbon = $this->randomDateBetween($startDate, $today);
            $updatedAtCarbon = $this->randomDateBetween($createdAtCarbon, $today);

            $emailVerifiedAt = null;

            if ($this->randomBoolean(90)) {
                $emailVerifiedAt = $this->randomDateBetween($createdAtCarbon, $updatedAtCarbon)->format('Y-m-d H:i:s');
            }

            $fn = self::FIRST_NAMES[array_rand(self::FIRST_NAMES)];
            $ln = self::LAST_NAMES[array_rand(self::LAST_NAMES)];
            $fln = $fn . ' ' . $ln;
            $email = $this->emailFor($fn, $ln, $sequence);
            $userType = $this->userTypeProfile();

            $rows[] = [
                'name' => $fln,
                'first_name' => $fn,
                'last_name' => $ln,
                'email' => $email,
                'created_at' => $createdAtCarbon->format('Y-m-d H:i:s'),
                'email_verified_at' => $emailVerifiedAt,
                'updated_at' => $updatedAtCarbon->format('Y-m-d H:i:s'),
                'is_sys_admin' => $userType['is_sys_admin'],
                'is_admin' => $userType['is_admin'],
                'is_team_leader' => $userType['is_team_leader'],
                'is_athlete' => $userType['is_athlete'],
                'is_guide' => $userType['is_guide'],
                'is_subscribed' => $this->randomBoolean(70),
                'password' => 'password123',
                'picture' => '',
                'phone' => $this->randomPhoneNumber(),
                'preferred_name' => $fn,
            ];

            if (count($rows) === self::INSERT_CHUNK_SIZE) {
                DB::table('users')->insert($rows);
                $rows = [];
            }
        }

        if ($rows !== []) {
            DB::table('users')->insert($rows);
        }

    }

    /**
     * @return array{is_athlete: bool, is_guide: bool, is_sys_admin: bool, is_admin: bool, is_team_leader: bool}
     */
    private function userTypeProfile(): array
    {
        $rand = mt_rand(1, 1000);

        $userType = [
            'is_athlete' => $rand <= 260,
            'is_guide' => $rand > 260,
            'is_sys_admin' => $rand === 261,
            'is_admin' => $rand >= 262 && $rand <= 266,
            'is_team_leader' => $rand >= 267 && $rand <= 282,
        ];

        if ($userType['is_athlete']) {
            $userType['is_guide'] = false;
            $userType['is_sys_admin'] = false;
            $userType['is_admin'] = false;
            $userType['is_team_leader'] = false;
        }

        return $userType;
    }

    private function randomBoolean(int $percentTrue): bool
    {
        return mt_rand(1, 100) <= $percentTrue;
    }

    private function randomDateBetween(Carbon $start, Carbon $end): Carbon
    {
        $startTimestamp = $start->getTimestamp();
        $endTimestamp = $end->getTimestamp();

        if ($endTimestamp <= $startTimestamp) {
            return $start->copy();
        }

        return Carbon::createFromTimestamp(mt_rand($startTimestamp, $endTimestamp));
    }

    private function randomPhoneNumber(): string
    {
        $areaCode = self::PHONE_AREA_CODES[array_rand(self::PHONE_AREA_CODES)];
        $prefix = str_pad((string) mt_rand(200, 999), 3, '0', STR_PAD_LEFT);
        $lineNumber = str_pad((string) mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);

        return sprintf('%s-%s-%s', $areaCode, $prefix, $lineNumber);
    }

    private function emailFor(string $firstName, string $lastName, int $sequence): string
    {
        return strtolower(Str::slug($firstName . '.' . $lastName, '.'))
            . $sequence
            . '@'
            . self::EMAIL_DOMAINS[$sequence % count(self::EMAIL_DOMAINS)];
    }
}
