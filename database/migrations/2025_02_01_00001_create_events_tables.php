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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('system_locations');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->foreignId('status_id')->constrained('system_statuses');
            $table->foreignId('created_by_id')->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('start_date');
            $table->index('end_date');
            $table->index('is_active');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
