<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->index('created_at', 'users_created_at_idx');
            $table->index('email_verified_at', 'users_email_verified_at_idx');
            $table->index('is_subscribed', 'users_is_subscribed_idx');
            $table->index('is_athlete', 'users_is_athlete_idx');
            $table->index('is_guide', 'users_is_guide_idx');
            $table->index('is_team_leader', 'users_is_team_leader_idx');
            $table->index('is_admin', 'users_is_admin_idx');
            $table->index('is_sys_admin', 'users_is_sys_admin_idx');
            $table->index(['is_sys_admin', 'is_admin', 'is_team_leader'], 'users_privileged_roles_idx');
        });

        Schema::table('system_location_access', function (Blueprint $table) {
            $table->index(['user_id', 'is_active', 'access_expiry_date'], 'location_access_user_validity_idx');
            $table->index(['location_id', 'is_active'], 'location_access_location_active_idx');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_created_at_idx');
            $table->dropIndex('users_email_verified_at_idx');
            $table->dropIndex('users_is_subscribed_idx');
            $table->dropIndex('users_is_athlete_idx');
            $table->dropIndex('users_is_guide_idx');
            $table->dropIndex('users_is_team_leader_idx');
            $table->dropIndex('users_is_admin_idx');
            $table->dropIndex('users_is_sys_admin_idx');
            $table->dropIndex('users_privileged_roles_idx');
        });

        Schema::table('system_location_access', function (Blueprint $table) {
            $table->dropIndex('location_access_user_validity_idx');
            $table->dropIndex('location_access_location_active_idx');
        });
    }
};
