<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')
                ->nullable()
                ->after('id')
                ->constrained('tenants')
                ->nullOnDelete()
                ->index();
        });

        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('module_key')->nullable();
            $table->string('source_type')->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('level');
            $table->string('event_key');
            $table->text('message');
            $table->json('context')->nullable();
            $table->string('request_id')->nullable();
            $table->string('correlation_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('url')->nullable();
            $table->string('method', 20)->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('user_id');
            $table->index('module_key');
            $table->index('source_type');
            $table->index('level');
            $table->index('event_key');
            $table->index('request_id');
            $table->index('correlation_id');
            $table->index('created_at');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_type')->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('action');
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('request_id')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('user_id');
            $table->index('action');
            $table->index('auditable_type');
            $table->index('auditable_id');
            $table->index('request_id');
            $table->index('created_at');
        });

        Schema::create('job_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('job_uuid')->nullable();
            $table->string('job_class');
            $table->string('queue')->nullable();
            $table->string('connection')->nullable();
            $table->string('status');
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->unsignedBigInteger('duration_ms')->nullable();
            $table->string('exception_class')->nullable();
            $table->text('exception_message')->nullable();
            $table->text('trace_excerpt')->nullable();
            $table->json('payload_summary')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('job_uuid');
            $table->index('job_class');
            $table->index('queue');
            $table->index('status');
            $table->index('started_at');
            $table->index('finished_at');
            $table->index('created_at');
        });

        Schema::create('module_health_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->string('module_key');
            $table->string('status');
            $table->string('severity');
            $table->string('summary');
            $table->json('details')->nullable();
            $table->timestamp('last_checked_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('module_key');
            $table->index('status');
            $table->index('severity');
            $table->index('last_checked_at');
            $table->index('resolved_at');
            $table->index('created_at');
        });

        Schema::create('operator_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('tenants')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('module_key')->nullable();
            $table->string('event_key');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('severity');
            $table->string('status');
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('tenant_id');
            $table->index('user_id');
            $table->index('module_key');
            $table->index('event_key');
            $table->index('severity');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operator_events');
        Schema::dropIfExists('module_health_checks');
        Schema::dropIfExists('job_logs');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('system_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });
    }
};
