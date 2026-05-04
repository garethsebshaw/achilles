<?php

namespace Database\Seeders\Users;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserRandomSeeder extends Seeder
{
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
        $this->command->info(class_basename(static::class) . ' seed started: ' . date('Y-m-d H:i:s'));

        $startDate = Carbon::create(2010, 1, 1);
        $today = Carbon::now();

        for ($i = 0; $i < 5000; $i++) {
            $createdAtCarbon = $this->randomDateBetween($startDate, $today);
            $updatedAtCarbon = $this->randomDateBetween($createdAtCarbon, $today);

            $createdAt = $createdAtCarbon->format('Y-m-d H:i:s');
            $updatedAt = $updatedAtCarbon->format('Y-m-d H:i:s');

            $emailVerifiedAt = null;
            if ($this->randomBoolean(90)) {
                $emailVerifiedAt = $this->randomDateBetween($createdAtCarbon, $updatedAtCarbon)->format('Y-m-d H:i:s');
            }

            $fn = self::FIRST_NAMES[array_rand(self::FIRST_NAMES)];
            $ln = self::LAST_NAMES[array_rand(self::LAST_NAMES)];
            $fln = $fn . ' ' . $ln;

            $email = strtolower(Str::slug($fn . '.' . $ln, '.')) . mt_rand(1000, 9999) . '@' . self::EMAIL_DOMAINS[array_rand(self::EMAIL_DOMAINS)];

            $rand = mt_rand(1, 1000);
            $userType = [
                'is_athlete' => $rand <= 260,
                'is_guide' => $rand > 260,
                'is_sys_admin' => $rand === 261,
                'is_admin' => $rand >= 262 && $rand <= 266,
                'is_team_leader' => $rand >= 267 && $rand <= 282
            ];

            // Ensure logical combinations
            if ($userType['is_athlete']) {
                $userType['is_guide'] = false;
                $userType['is_sys_admin'] = false;
                $userType['is_admin'] = false;
                $userType['is_team_leader'] = false;
            }

            try {
                DB::table('users')->insert([
                    'name' => $fln,
                    'first_name' => $fn,
                    'last_name' => $ln,
                    'email' => $email,
                    'created_at' => $createdAt,
                    'email_verified_at' => $emailVerifiedAt,
                    'updated_at' => $updatedAt,
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
                ]);
            } catch (\Exception $e) {
                $this->command->error("Error on iteration {$i}: " . $e->getMessage());
                continue;
            }

            if ($i % 1000 === 0) {
                $this->command->info("Created {$i} users...");
            }
        }

        $this->command->info(class_basename(static::class) . ' seed completed: ' . date('Y-m-d H:i:s'));
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
}
