<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $this->addIndexes('workout_sessions', function (Blueprint $table) {
            $table->index(['location_id', 'session_date'], 'workout_sessions_location_session_date_idx');
            $table->index(['status_id', 'session_date'], 'workout_sessions_status_session_date_idx');
            $table->index(['workout_id', 'session_date'], 'workout_sessions_workout_session_date_idx');
        });

        $this->addIndexes('workout_signups', function (Blueprint $table) {
            $table->index(['workout_session_id', 'status_id'], 'workout_signups_session_status_idx');
            $table->index(['workout_session_id', 'checked_in_at'], 'workout_signups_session_checked_in_idx');
            $table->index(['user_id', 'status_id'], 'workout_signups_user_status_idx');
            $table->index(['athlete_id', 'workout_session_id'], 'workout_signups_athlete_session_idx');
        });

        $this->addIndexes('system_location_access', function (Blueprint $table) {
            $table->index(['user_id', 'is_active', 'access_expiry_date'], 'system_location_access_user_active_expiry_idx');
            $table->index(['location_id', 'is_active', 'access_expiry_date'], 'system_location_access_location_active_expiry_idx');
        });

        $this->addIndexes('equipment', function (Blueprint $table) {
            $table->index(['location_id', 'status_id', 'is_active'], 'equipment_location_status_active_idx');
            $table->index(['assigned_user_id', 'is_active'], 'equipment_assigned_user_active_idx');
            $table->index(['next_maintenance_date', 'is_active'], 'equipment_next_maintenance_active_idx');
        });

        $this->addIndexes('equipment_checkouts', function (Blueprint $table) {
            $table->index(['equipment_id', 'returned_at'], 'equipment_checkouts_equipment_returned_idx');
            $table->index(['user_id', 'returned_at'], 'equipment_checkouts_user_returned_idx');
        });

        $this->addIndexes('maintenance_requests', function (Blueprint $table) {
            $table->index(['status_id', 'priority_id', 'reported_at'], 'maintenance_requests_status_priority_reported_idx');
            $table->index(['equipment_id', 'status_id'], 'maintenance_requests_equipment_status_idx');
            $table->index(['assigned_to_id', 'status_id'], 'maintenance_requests_assigned_status_idx');
        });
    }

    public function down(): void
    {
        $this->dropIndexes('workout_sessions', [
            'workout_sessions_location_session_date_idx',
            'workout_sessions_status_session_date_idx',
            'workout_sessions_workout_session_date_idx',
        ]);

        $this->dropIndexes('workout_signups', [
            'workout_signups_session_status_idx',
            'workout_signups_session_checked_in_idx',
            'workout_signups_user_status_idx',
            'workout_signups_athlete_session_idx',
        ]);

        $this->dropIndexes('system_location_access', [
            'system_location_access_user_active_expiry_idx',
            'system_location_access_location_active_expiry_idx',
        ]);

        $this->dropIndexes('equipment', [
            'equipment_location_status_active_idx',
            'equipment_assigned_user_active_idx',
            'equipment_next_maintenance_active_idx',
        ]);

        $this->dropIndexes('equipment_checkouts', [
            'equipment_checkouts_equipment_returned_idx',
            'equipment_checkouts_user_returned_idx',
        ]);

        $this->dropIndexes('maintenance_requests', [
            'maintenance_requests_status_priority_reported_idx',
            'maintenance_requests_equipment_status_idx',
            'maintenance_requests_assigned_status_idx',
        ]);
    }

    private function addIndexes(string $tableName, callable $callback): void
    {
        try {
            Schema::table($tableName, $callback);
        } catch (\Throwable $e) {
            // Ignore duplicate-index failures so existing environments can migrate safely.
        }
    }

    private function dropIndexes(string $tableName, array $indexes): void
    {
        Schema::table($tableName, function (Blueprint $table) use ($indexes) {
            foreach ($indexes as $index) {
                try {
                    $table->dropIndex($index);
                } catch (\Throwable $e) {
                    // Ignore missing-index failures on rollback.
                }
            }
        });
    }
};
