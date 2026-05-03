<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')
                ->after('name');
            $table->string('middle_name')
                ->after('first_name')
                ->nullable();
            $table->string('last_name')
                ->after('middle_name');
            $table->string('preferred_name')
                ->after('last_name')
                ->nullable();
            $table->string('phone',22)
                ->after('email_verified_at')
                ->nullable();
            $table->string('picture')
                ->after('phone')
                ->nullable();
            $table->boolean('is_subscribed')->default(true)
                ->after('phone');
            $table->boolean('is_sys_admin')->default(false)
                ->after('is_subscribed');
            $table->boolean('is_admin')->default(false)
                ->after('is_sys_admin');
            $table->boolean('is_team_leader')->default(false)
                ->after('is_sys_admin');
            $table->boolean('is_athlete')->default(false)
                ->after('is_team_leader');
            $table->boolean('is_guide')->default(false)
                ->after('is_athlete');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'middle_name',
                'last_name',
                'preferred_name',
                'phone',
                'picture',
                'is_subscribed',
                'is_team_leader',
            ]);
        });
    }
};
