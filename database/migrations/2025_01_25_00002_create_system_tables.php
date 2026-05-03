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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->string('type')->default('string');
            $table->boolean('is_encrypted')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('system_modules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('model_type')->unique(); // 'App\Models\Equipment'
            $table->text('description')->nullable();
            $table->boolean('active')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('system_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_module_id')
                ->constrained('system_modules')
                ->onDelete('cascade');
            $table->string('name');
            $table->string('description')->nullable();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('system_categories')
                ->onDelete('cascade')
                ->default(null);
            $table->boolean('active')->default(true);
            $table->json('metadata')->nullable();

            $table->timestamps();

            $table->unique(['system_module_id', 'parent_id', 'name'], 'system_categories_unique');
            $table->softDeletes();
        });

        Schema::create('system_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete(); // Instead of cascade
            $table->string('action');
            $table->string('entity_type');
            $table->unsignedBigInteger('entity_id');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['entity_type', 'entity_id']);
            $table->softDeletes();
        });

        Schema::create('system_media_files', function (Blueprint $table) {
            $table->id();
            $table->morphs('model');
            $table->string('collection_name');
            $table->string('file_name');
            $table->string('mime_type');
            $table->string('disk');
            $table->unsignedBigInteger('size');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id', 'collection_name']);
        });

        Schema::create('system_notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->softDeletes();
        });

        Schema::create('system_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('system_tags')
                ->onDelete('cascade');
            $table->string('name');
            $table->string('type')->nullable();
            $table->string('color', 20)->nullable();
            $table->timestamps();

            $table->unique(['name', 'type', 'parent_id']);
            $table->softDeletes();
        });

        Schema::create('system_taggables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')
                    ->constrained('system_tags')
                    ->onDelete('cascade');
            $table->foreignId('system_module_id')
                ->constrained('system_modules')
                ->onDelete('cascade');
            $table->morphs('taggable');

            $table->timestamps();
            $table->unique(
                ['tag_id', 'system_module_id', 'taggable_id', 'taggable_type'],
                'system_taggables_unique'
            );
        });

        Schema::create('system_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_module_id')
                ->constrained('system_modules')
                ->onDelete('cascade');
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('system_statuses')
                ->onDelete('cascade')
                ->default(null);
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('code');
            $table->string('color', 20)
                ->nullable();
            $table->integer('sort_order')
                ->default(0);
            $table->boolean('is_default')
                ->default(false);
            $table->boolean('is_system')
                ->default(false);
            $table->json('metadata')
                ->nullable();
            $table->timestamps();

            $table->unique(['system_module_id', 'code']);
            $table->softDeletes();
        });

        Schema::create('system_countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_name');
            $table->string('iso2', 2);
            $table->string('iso3', 3);
            $table->string('numeric_code', 3);
            $table->string('calling_code');
            $table->string('capital')->nullable();
            $table->string('currency')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->boolean('active')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique('iso2');
            $table->unique('iso3');
            $table->unique('numeric_code');
            $table->softDeletes();
        });

        // Create system_regions table (e.g., North America, Europe, etc.)
        Schema::create('system_regions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // Create system_chapters table
        Schema::create('system_chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_country_id')->constrained();
            $table->foreignId('system_region_id')->constrained();
            $table->string('name');
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('website')->nullable();
            $table->json('social_media')->nullable(); // Store Facebook, Instagram, etc.
            $table->boolean('is_headquarters')->default(false);
            $table->boolean('active')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['system_country_id', 'name']);
            $table->softDeletes();
        });

        // Create system_chapter_contacts table
        Schema::create('system_chapter_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('system_chapter_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete(); // Instead of cascade
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('system_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('system_countries');
            $table->foreignId('region_id')->nullable()->constrained('system_regions');
            $table->foreignId('chapter_id')->nullable()->constrained('system_chapters');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('timezone')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for common queries
            $table->index('name');
            $table->index('city');
            $table->index('postal_code');
            $table->index('is_active');
            $table->index(['country_id', 'region_id']);
        });

        // Create pivot table for location access
        Schema::create('system_location_access', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('system_locations')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('access_type'); // key, code, card, etc.
            $table->string('access_identifier')->nullable(); // key number, card number, etc.
            $table->date('access_granted_date');
            $table->date('access_expiry_date')->nullable();
            $table->foreignId('granted_by_id')->nullable()->constrained('users');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('access_type');
            $table->index('access_identifier');
            $table->index('is_active');
            $table->index('access_expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_chapter_contacts');
        Schema::dropIfExists('system_chapters');
        Schema::dropIfExists('system_regions');
        Schema::dropIfExists('system_countries');
        Schema::dropIfExists('system_location_access');
        Schema::dropIfExists('system_locations');
        Schema::dropIfExists('system_statuses');
        Schema::dropIfExists('system_taggables');
        Schema::dropIfExists('system_tags');
        Schema::dropIfExists('system_notifications');
        Schema::dropIfExists('system_media_files');
        Schema::dropIfExists('system_audit_logs');
        Schema::dropIfExists('system_categories');
        Schema::dropIfExists('system_modules');
        Schema::dropIfExists('system_settings');
    }
};
