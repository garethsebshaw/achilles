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

        Schema::create('languages', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('iso_code')->nullable();

            $table->foreignId('system_category_id')
                ->nullable()
                ->constrained('system_categories')
                ->onDelete('cascade');

            $table->boolean('active')->default(true);

            $table->json('metadata')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('language_proficiencies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('proficiency_status_id')
                ->constrained('system_statuses')
                ->onDelete('cascade');

            $table->foreignId('language_id')
                ->constrained()
                ->onDelete('cascade');

            $table->timestamps();

            // Ensure a user can have only one language per proficiency level
            $table->unique(['user_id', 'language_id'], 'user_language_proficiency');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language_proficiencies');
        Schema::dropIfExists('languages');
    }
};
