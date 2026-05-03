<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('system_locations');
            $table->foreignId('activity_type_id')->constrained('system_categories');
            $table->unsignedInteger('version')->default(1);
            $table->string('name');
            $table->text('description')->nullable();
            $table->time('default_start_time');
            $table->time('default_end_time');
            $table->boolean('is_recurring')->default(true);
            $table->string('recurrence_pattern')->nullable(); // weekly, monthly, etc.
            $table->integer('advance_create_weeks')->default(52);
            $table->integer('default_max_athletes')->nullable();
            $table->integer('default_max_guides')->nullable();
            $table->boolean('is_template')->default(false);
            $table->boolean('is_current_version')->default(true);
            $table->json('metadata')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->softDeletes();
        });

        Schema::create('workout_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->nullable()->constrained('workouts');
            $table->unsignedInteger('workout_version')->default(1);
            $table->foreignId('location_id')->constrained('system_locations');
            $table->date('session_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('max_athletes')->nullable();
            $table->integer('max_guides')->nullable();
            $table->foreignId('status_id')->constrained('system_statuses');
            $table->json('weather_conditions')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users');
            $table->timestamp('cancelled_at')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->softDeletes();
        });
        // Workout Signup Table
        Schema::create('workout_signups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_session_id')->constrained('workout_sessions');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('athlete_id')->nullable()->constrained('users')->after('user_id');
            $table->timestamp('checked_in_at')->nullable()->after('status_id');
            $table->timestamp('checked_out_at')->nullable()->after('checked_in_at');
            $table->foreignId('status_id')->nullable()->constrained('system_statuses');
            $table->json('preferences')->nullable();
            $table->json('equipment_requirements')->nullable();
            $table->timestamps();

            $table->softDeletes();
        });

        // Equipment Assignment Tracking
        Schema::create('workout_equipment_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_signup_id')->constrained('workout_signups');
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->foreignId('assignment_type_id')->constrained('system_categories');
            $table->json('fitting_details')->nullable(); // Height, weight, etc.
            $table->timestamps();

            $table->softDeletes();
        });

        Schema::create('tandem_bike_pairings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bike_id')->constrained('equipment');
            $table->foreignId('pilot_user_id')->constrained('users');
            $table->foreignId('stoker_user_id')->constrained('users');
            $table->foreignId('compatibility_status_id')->constrained('system_statuses');
            $table->decimal('weight_compatibility_score', 5, 2)->nullable();
            $table->json('compatibility_details')->nullable(); // Store specific measurement comparisons
            $table->timestamp('last_checked_at')->nullable();
            $table->timestamps();

            // Ensure unique combination of bike, pilot, and stoker
            $table->unique(['bike_id', 'pilot_user_id', 'stoker_user_id']);
        });

        Schema::create('tandem_bike_pairing_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bike_id')->constrained('equipment');
            $table->timestamp('checked_at');
            $table->json('check_results')->nullable(); // Store full check details
            $table->timestamps();

            $table->softDeletes();
        });

        Schema::create('workout_specific_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_signup_id')->constrained('workout_signups');
            $table->foreignId('sport_category_id')->constrained('system_categories');

            // Units for distance and pace
            $table->foreignId('distance_unit_id')->constrained('system_statuses'); // e.g., Miles, Kilometers, Meters
            $table->foreignId('pace_unit_id')->constrained('system_statuses'); // e.g., Min/Mile, Min/KM
            $table->foreignId('speed_unit_id')->constrained('system_statuses'); // e.g., MPH, KMH, M/S

            $table->decimal('distance', 10, 2)->nullable();
            $table->decimal('time', 10, 2)->nullable();
            $table->decimal('pace_min', 10, 2)->nullable();
            $table->decimal('pace_max', 10, 2)->nullable();
            $table->decimal('speed_min', 10, 2)->nullable();
            $table->decimal('speed_max', 10, 2)->nullable();

            // JSON for sport-specific additional data
            $table->json('additional_details')->nullable();

            $table->timestamps();

            $table->softDeletes();
        });

        // Standalone meeting points table
        Schema::create('meeting_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('type_id')->constrained('system_categories');
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('chapter_id')->nullable()->constrained('system_chapters');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->softDeletes();
        });

        // Join table for workout templates to meeting points
        Schema::create('workout_template_meeting_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained('workouts');
            $table->foreignId('meeting_point_id')->constrained('meeting_points');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->softDeletes();
        });

        // Join table for workout sessions to meeting points
        Schema::create('workout_session_meeting_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_session_id')->constrained('workout_sessions');
            $table->foreignId('meeting_point_id')->constrained('meeting_points');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->softDeletes();
        });

    }

    public function down()
    {
        Schema::dropIfExists('workout_session_meeting_points');
        Schema::dropIfExists('workout_template_meeting_points');
        Schema::dropIfExists('meeting_points');
        Schema::dropIfExists('tandem_bike_pairing_checks');
        Schema::dropIfExists('tandem_bike_pairings');
        Schema::dropIfExists('workout_equipment_assignments');
        Schema::dropIfExists('workout_signups');
        Schema::dropIfExists('workout_specific_details');
        Schema::dropIfExists('workout_sessions');
        Schema::dropIfExists('workouts');
    }
};
