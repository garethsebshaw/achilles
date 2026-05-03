<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Carbon\Carbon;

class UserRandomSeeder extends Seeder
{
    public function run()
    {
        $this->command->info(class_basename(static::class) . ' seed started: ' . date('Y-m-d H:i:s'));

        $faker = Faker::create();

        // Set up date range
        $startDate = Carbon::create(2010, 1, 1);
        $today = Carbon::now();

        // Create 25000 test users
        for ($i = 0; $i < 5000; $i++) {
            // Generate dates ensuring proper format
            $createdAt = Carbon::instance($faker->dateTimeBetween($startDate, $today))->format('Y-m-d H:i:s');

            // Generate a random date between created_at and today for updated_at
            $updatedAt = Carbon::instance($faker->dateTimeBetween($createdAt, $today))->format('Y-m-d H:i:s');

            // Optionally generate email_verified_at (90% chance of having one)
            $emailVerifiedAt = null;
            if ($faker->boolean(90)) {
                $emailVerifiedAt = Carbon::instance($faker->dateTimeBetween($createdAt, $updatedAt))->format('Y-m-d H:i:s');
            }

            // Generate name and email
            $fn = $faker->firstName;
            $ln = $faker->lastName;
            $fln = $fn . " " . $ln;

            // Generate email with random domain
            $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'aol.com', 'icloud.com'];
            $email = strtolower($fn . $ln . rand(1000, 9999) . '@' . $domains[array_rand($domains)]);

            // Determine user type
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
                    'is_subscribed' => $faker->boolean(70),
                    'password' => 'password123',
                    'picture' => '', //$faker->imageUrl(),
                    'phone' => $faker->phoneNumber,
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
}
