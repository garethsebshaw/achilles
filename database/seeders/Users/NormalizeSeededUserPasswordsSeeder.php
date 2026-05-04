<?php

namespace Database\Seeders\Users;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class NormalizeSeededUserPasswordsSeeder extends Seeder
{
    public function run(): void
    {
        $plaintextPasswords = DB::table('users')
            ->select('password')
            ->where('password', 'not like', '$2y$%')
            ->distinct()
            ->pluck('password');

        foreach ($plaintextPasswords as $plaintextPassword) {
            DB::table('users')
                ->where('password', $plaintextPassword)
                ->update(['password' => Hash::make($plaintextPassword)]);
        }
    }
}
