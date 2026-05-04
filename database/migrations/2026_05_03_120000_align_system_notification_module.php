<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $newModelType = 'App\\Models\\SystemNotification';
        $legacyModelType = 'App\\Models\\Notification';

        $current = DB::table('system_modules')->where('model_type', $newModelType)->first();
        $legacy = DB::table('system_modules')->where('model_type', $legacyModelType)->first();

        if ($current) {
            DB::table('system_modules')
                ->where('id', $current->id)
                ->update([
                    'name' => 'Notifications',
                    'description' => 'User notifications and alerts',
                    'active' => 1,
                ]);

            if ($legacy) {
                DB::table('system_modules')->where('id', $legacy->id)->update(['active' => 0]);
            }

            return;
        }

        if ($legacy) {
            DB::table('system_modules')
                ->where('id', $legacy->id)
                ->update([
                    'name' => 'Notifications',
                    'model_type' => $newModelType,
                    'description' => 'User notifications and alerts',
                    'active' => 1,
                ]);

            return;
        }

        DB::table('system_modules')->insert([
            'name' => 'Notifications',
            'model_type' => $newModelType,
            'description' => 'User notifications and alerts',
            'active' => 1,
        ]);
    }

    public function down(): void
    {
        DB::table('system_modules')
            ->where('model_type', 'App\\Models\\SystemNotification')
            ->update([
                'model_type' => 'App\\Models\\Notification',
                'active' => 0,
            ]);
    }
};
