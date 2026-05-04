<?php

namespace Database\Seeders;

use Database\Seeders\Certifications\UserCertificateSeeder;
use Database\Seeders\Users\LanguageSeeder;
use Database\Seeders\Users\NormalizeSeededUserPasswordsSeeder;
use Database\Seeders\Users\UserRandomSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CoreReferenceSeeder::class,

            UserRandomSeeder::class,
            NormalizeSeededUserPasswordsSeeder::class,
            LanguageSeeder::class,
            UserCertificateSeeder::class,

            OperationalDemoSeeder::class,
        ]);
    }
}
