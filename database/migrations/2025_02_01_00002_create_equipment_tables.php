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
        Schema::create('manufacturers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('contact_info')->nullable();
            $table->string('website')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('component_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_category_id')->constrained('system_categories');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('default_maintenance_interval_miles')->nullable();
            $table->integer('default_maintenance_interval_months')->nullable();
            $table->json('attributes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('storage_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('system_locations');
            $table->string('name');
            $table->string('type'); // unit, van, static_van
            $table->integer('capacity')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('equipment_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('rating'); // 1-5 or similar scale
            $table->boolean('serviceable'); // Can the equipment be used in this condition
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('equipment_maintenance_priorities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('level'); // 1-5 or similar
            $table->integer('response_time_hours')->nullable(); // Target response time
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_category_id')->constrained('system_categories');
            $table->foreignId('manufacturer_id')->constrained('manufacturers');
            $table->string('name');
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('qr_code')->unique();
            $table->foreignId('location_id')->constrained('system_locations');
            $table->foreignId('storage_location_id')->nullable()->constrained('storage_locations');
            $table->foreignId('status_id')->constrained('system_statuses');
            $table->foreignId('condition_id')->constrained('equipment_conditions');
            $table->string('owner_type')->nullable(); // For polymorphic: athlete, partner
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->foreignId('assigned_user_id')->nullable()->constrained('users');
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 10, 2)->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->timestamp('last_maintenance_date')->nullable();
            $table->timestamp('next_maintenance_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('attributes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['owner_type', 'owner_id'], 'owner_type_owner_id');
        });
        Schema::create('equipment_checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('event_id')->nullable()->constrained('events');
            $table->timestamp('checked_out_at');
            $table->timestamp('expected_return_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->integer('distance_traveled')->nullable();
            $table->foreignId('condition_out_id')->constrained('equipment_conditions');
            $table->foreignId('condition_in_id')->nullable()->constrained('equipment_conditions');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('equipment_components', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->foreignId('component_type_id')->constrained('component_types');
            $table->foreignId('manufacturer_id')->constrained('manufacturers');
            $table->string('model')->nullable();
            $table->string('serial_number')->nullable();
            $table->foreignId('status_id')->constrained('system_statuses');
            $table->foreignId('condition_id')->constrained('equipment_conditions');
            $table->date('installation_date')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->boolean('is_monitored')->default(false);
            $table->integer('maintenance_interval_miles')->nullable();
            $table->integer('maintenance_interval_months')->nullable();
            $table->timestamp('last_maintenance_date')->nullable();
            $table->timestamp('next_maintenance_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('component_compatibility', function (Blueprint $table) {
            $table->id();
            $table->foreignId('component_type_id')->constrained('component_types');
            $table->foreignId('compatible_with_id')->constrained('component_types');
            $table->timestamps();

            $table->unique(['component_type_id', 'compatible_with_id'],'component_compatibility_unique');
        });


        //** Maintenance */

        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->foreignId('component_id')->nullable()->constrained('equipment_components');
            $table->foreignId('reported_by_id')->constrained('users');
            $table->foreignId('assigned_to_id')->nullable()->constrained('users');
            $table->foreignId('status_id')->constrained('system_statuses');
            $table->foreignId('priority_id')->constrained('equipment_maintenance_priorities');
            $table->text('description');
            $table->timestamp('reported_at');
            $table->timestamp('assigned_at')->nullable();
            $table->integer('estimated_time')->nullable(); // In minutes
            $table->integer('actual_time')->nullable(); // In minutes
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->decimal('actual_cost', 10, 2)->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('parent_request_id')->nullable()->constrained('maintenance_requests');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_request_id')->nullable()->constrained('maintenance_requests');
            $table->foreignId('equipment_id')->constrained('equipment');
            $table->foreignId('component_id')->nullable()->constrained('equipment_components');
            $table->foreignId('performed_by_id')->constrained('users');
            $table->string('work_type'); // service, repair, inspection
            $table->text('description');
            $table->timestamp('performed_at');
            $table->integer('time_spent')->nullable(); // In minutes
            $table->decimal('cost', 10, 2)->nullable();
            $table->json('parts_used')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
        Schema::dropIfExists('equipment_components');
        Schema::dropIfExists('manufacturers');
        Schema::dropIfExists('component_types');
        Schema::dropIfExists('component_compatibility');
        Schema::dropIfExists('storage_locations');
        Schema::dropIfExists('equipment_conditions');
        Schema::dropIfExists('equipment_maintenance_priorities');
        Schema::dropIfExists('maintenance_requests');
        Schema::dropIfExists('maintenance_logs');
        Schema::dropIfExists('equipment_checkouts');
    }
};
