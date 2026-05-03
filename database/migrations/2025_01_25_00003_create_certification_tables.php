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
        Schema::create('certification_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();

            $table->unique(['name']);
            $table->softDeletes();
        });

        Schema::create('certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certification_type_id')
                ->constrained('certification_types')
                ->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('validity_period')->nullable(); // in months
            $table->boolean('requires_document')->default(false);
            $table->foreignId('system_status_id')
                ->constrained('system_statuses');
            $table->timestamps();

            $table->unique(['certification_type_id', 'name']);
            $table->softDeletes();
        });

        Schema::create('user_certifications', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                    ->constrained()
                    ->onDelete('cascade');

            $table->foreignId('certification_id')
                ->constrained('certifications')
                ->onDelete('cascade');

            $table->string('name')
                ->nullable()
                ->default(null);

            $table->date('certified_at');

            $table->date('expires_at')
                ->nullable();
            $table->tinyInteger('validity_period');
            $table->mediumText('description')
                ->nullable()
                ->default(null);
            $table->string('file_path')
                ->nullable()
                ->default(null);
            $table->string('file_type')
                ->nullable()
                ->default(null);
            $table->timestamp('uploaded_at');

            $table->foreignId('system_status_id')
                ->constrained('system_statuses');

            $table->text('notes')->nullable();

            $table->timestamps();
        });

        Schema::create('certification_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certification_type_id')->nullable()->index();
            $table->string('name')
                ->nullable()
                ->default(null);
            $table->mediumText('description')
                ->nullable()
                ->default(null);
            $table->tinyInteger('validity_period');


            $table->foreignId('user_certification_id')
                ->constrained('user_certifications')
                ->onDelete('cascade');
            $table->string('file_path')
                ->nullable()
                ->default(null);
            $table->string('file_type')
                ->nullable()
                ->default(null);
            $table->timestamp('uploaded_at');

            $table->timestamps();
        });

        // Create the pivot table
        Schema::create('certification_countries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certification_id')->constrained()->onDelete('cascade');
            $table->foreignId('system_country_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['certification_id', 'system_country_id'],'certification_countries_unique');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certification_countries');
        Schema::dropIfExists('certification_documents');
        Schema::dropIfExists('user_certifications');
        Schema::dropIfExists('certifications');
        Schema::dropIfExists('certification_types');
    }
};
