<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('system_modules') || ! Schema::hasTable('system_statuses')) {
            return;
        }

        $this->ensureModuleStatuses('App\Models\WorkoutSession', [
            ['name' => 'Scheduled', 'code' => 'session_scheduled', 'color' => '#17a2b8', 'sort_order' => 10, 'is_default' => true],
            ['name' => 'In Progress', 'code' => 'session_progress', 'color' => '#28a745', 'sort_order' => 20],
            ['name' => 'Completed', 'code' => 'session_completed', 'color' => '#20c997', 'sort_order' => 30],
            ['name' => 'Cancelled', 'code' => 'session_cancelled', 'color' => '#dc3545', 'sort_order' => 40],
            ['name' => 'Weather Hold', 'code' => 'session_weather', 'color' => '#ffc107', 'sort_order' => 50],
        ]);

        $this->ensureModuleStatuses('App\Models\WorkoutSignup', [
            ['name' => 'Pending', 'code' => 'signup_pending', 'color' => '#ffc107', 'sort_order' => 10, 'is_default' => true],
            ['name' => 'Confirmed', 'code' => 'signup_confirmed', 'color' => '#28a745', 'sort_order' => 20],
            ['name' => 'Cancelled', 'code' => 'signup_cancelled', 'color' => '#dc3545', 'sort_order' => 30],
            ['name' => 'Attended', 'code' => 'signup_attended', 'color' => '#007bff', 'sort_order' => 40],
            ['name' => 'No Show', 'code' => 'signup_no_show', 'color' => '#6c757d', 'sort_order' => 50],
            ['name' => 'Checked In', 'code' => 'signup_checked_in', 'color' => '#17a2b8', 'sort_order' => 60],
            ['name' => 'Checked Out', 'code' => 'signup_checked_out', 'color' => '#fd7e14', 'sort_order' => 70],
            ['name' => 'Waitlisted', 'code' => 'signup_waitlisted', 'color' => '#e83e8c', 'sort_order' => 80],
            ['name' => 'Late Cancellation', 'code' => 'signup_late_cancel', 'color' => '#ff851b', 'sort_order' => 90],
            ['name' => 'Banned', 'code' => 'signup_banned', 'color' => '#343a40', 'sort_order' => 100],
        ]);

        $this->mergeLegacySignupStatus('signup_waitlist', 'signup_waitlisted');
        $this->mergeLegacySignupStatus('signup_noshow', 'signup_no_show');
    }

    public function down(): void
    {
        // Intentionally non-destructive.
    }

    private function ensureModuleStatuses(string $modelType, array $statuses): void
    {
        $moduleId = DB::table('system_modules')->where('model_type', $modelType)->value('id');

        if (! $moduleId) {
            return;
        }

        DB::table('system_statuses')
            ->where('system_module_id', $moduleId)
            ->update(['is_default' => false]);

        foreach ($statuses as $status) {
            DB::table('system_statuses')->updateOrInsert(
                [
                    'system_module_id' => $moduleId,
                    'code' => $status['code'],
                ],
                [
                    'name' => $status['name'],
                    'color' => $status['color'],
                    'sort_order' => $status['sort_order'],
                    'is_default' => $status['is_default'] ?? false,
                    'is_system' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }

    private function mergeLegacySignupStatus(string $legacyCode, string $canonicalCode): void
    {
        $moduleId = DB::table('system_modules')->where('model_type', 'App\Models\WorkoutSignup')->value('id');

        if (! $moduleId) {
            return;
        }

        $canonicalId = DB::table('system_statuses')
            ->where('system_module_id', $moduleId)
            ->where('code', $canonicalCode)
            ->value('id');

        if (! $canonicalId) {
            return;
        }

        $legacyIds = DB::table('system_statuses')
            ->where('system_module_id', $moduleId)
            ->where('code', $legacyCode)
            ->pluck('id');

        if ($legacyIds->isEmpty()) {
            return;
        }

        DB::table('workout_signups')
            ->whereIn('status_id', $legacyIds)
            ->update(['status_id' => $canonicalId]);

        DB::table('system_statuses')
            ->whereIn('id', $legacyIds)
            ->delete();
    }
};
