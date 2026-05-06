<?php

namespace Tests\Feature\Logging;

use App\Models\Tenant;
use App\Models\User;
use App\Modules\Logging\Models\AuditLog;
use App\Modules\Logging\Models\JobLog;
use App\Modules\Logging\Models\ModuleHealthCheck;
use App\Modules\Logging\Models\OperatorEvent;
use App\Modules\Logging\Models\SystemLog;
use App\Modules\Logging\Services\AuditLogger;
use App\Modules\Logging\Services\JobLogger;
use App\Modules\Logging\Services\ModuleHealthReporter;
use App\Modules\Logging\Services\OperatorEventLogger;
use App\Modules\Logging\Services\SystemLogger;
use App\Modules\Logging\Support\LogContextSanitizer;
use App\Nova\Actions\Logging\AcknowledgeOperatorEvents;
use App\Nova\Actions\Logging\ResolveOperatorEvents;
use App\Nova\SystemLog as SystemLogResource;
use Database\Seeders\Logging\DefaultTenantSeeder;
use Illuminate\Contracts\Queue\Job as QueueJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Queue\Events\JobFailed;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Mockery;
use RuntimeException;
use Illuminate\Support\Collection;
use Tests\TestCase;

class LoggingModuleTest extends TestCase
{
    use RefreshDatabase;

    protected Tenant $defaultTenant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DefaultTenantSeeder::class);
        $this->defaultTenant = Tenant::query()->where('key', 'achilles')->firstOrFail();
    }

    public function test_default_tenant_exists(): void
    {
        $this->assertDatabaseHas('tenants', [
            'key' => 'achilles',
            'name' => 'Achilles',
        ]);
    }

    public function test_system_logger_creates_tenant_scoped_and_platform_logs(): void
    {
        $user = User::factory()->create([
            'tenant_id' => $this->defaultTenant->id,
            'is_admin' => true,
        ]);

        $this->actingAs($user);

        app(SystemLogger::class)->info('logging.test.info', 'Tenant scoped log', [
            'module_key' => 'logging',
            'token' => 'super-secret',
        ]);

        app(SystemLogger::class)->critical('logging.test.platform', 'Platform event', [
            'tenant_id' => null,
            'module_key' => 'logging',
        ]);

        $tenantLog = SystemLog::query()->where('event_key', 'logging.test.info')->firstOrFail();
        $platformLog = SystemLog::query()->where('event_key', 'logging.test.platform')->firstOrFail();

        $this->assertSame($this->defaultTenant->id, $tenantLog->tenant_id);
        $this->assertSame('[redacted]', $tenantLog->context['token']);
        $this->assertNull($platformLog->tenant_id);
    }

    public function test_tenant_user_cannot_view_another_tenants_logs_and_platform_admin_can_view_all(): void
    {
        $tenantA = Tenant::query()->create(['key' => 'tenant-a', 'name' => 'Tenant A']);
        $tenantB = Tenant::query()->create(['key' => 'tenant-b', 'name' => 'Tenant B']);

        $tenantAdmin = User::factory()->create([
            'tenant_id' => $tenantA->id,
            'is_admin' => true,
        ]);

        $platformAdmin = User::factory()->create([
            'tenant_id' => $tenantA->id,
            'is_sys_admin' => true,
        ]);

        $tenantALog = SystemLog::query()->create([
            'tenant_id' => $tenantA->id,
            'user_id' => $tenantAdmin->id,
            'level' => 'info',
            'event_key' => 'tenant-a.log',
            'message' => 'Tenant A log',
        ]);

        $tenantBLog = SystemLog::query()->create([
            'tenant_id' => $tenantB->id,
            'level' => 'info',
            'event_key' => 'tenant-b.log',
            'message' => 'Tenant B log',
        ]);

        $tenantRequest = NovaRequest::create('/nova-api/system-logs', 'GET');
        $tenantRequest->setUserResolver(fn () => $tenantAdmin);
        $tenantScopedIds = SystemLogResource::indexQuery($tenantRequest, SystemLog::query())->pluck('id')->all();

        $platformRequest = NovaRequest::create('/nova-api/system-logs', 'GET');
        $platformRequest->setUserResolver(fn () => $platformAdmin);
        $platformScopedIds = SystemLogResource::indexQuery($platformRequest, SystemLog::query())->pluck('id')->all();

        $this->assertContains($tenantALog->id, $tenantScopedIds);
        $this->assertNotContains($tenantBLog->id, $tenantScopedIds);
        $this->assertContains($tenantALog->id, $platformScopedIds);
        $this->assertContains($tenantBLog->id, $platformScopedIds);

        $this->assertFalse($tenantAdmin->can('view', $tenantBLog));
        $this->assertTrue($platformAdmin->can('view', $tenantBLog));
    }

    public function test_audit_logger_records_before_after_values_and_redacts_secrets(): void
    {
        $user = User::factory()->create([
            'tenant_id' => $this->defaultTenant->id,
            'is_admin' => true,
        ]);

        $this->actingAs($user);

        $target = User::factory()->create(['tenant_id' => $this->defaultTenant->id]);

        $log = app(AuditLogger::class)->record(
            'user.updated',
            $target,
            ['api_key' => 'old-secret', 'name' => 'Old'],
            ['api_key' => 'new-secret', 'name' => 'New'],
            ['module_key' => 'users']
        );

        $this->assertInstanceOf(AuditLog::class, $log);
        $this->assertSame('[redacted]', $log->before['api_key']);
        $this->assertSame('[redacted]', $log->after['api_key']);
        $this->assertSame('New', $log->after['name']);
    }

    public function test_sanitizer_redacts_sensitive_keys(): void
    {
        $sanitized = app(LogContextSanitizer::class)->sanitize([
            'password' => 'secret',
            'nested' => [
                'access_token' => 'abc',
                'safe' => 'value',
            ],
        ]);

        $this->assertSame('[redacted]', $sanitized['password']);
        $this->assertSame('[redacted]', $sanitized['nested']['access_token']);
        $this->assertSame('value', $sanitized['nested']['safe']);
    }

    public function test_job_failure_event_creates_job_log(): void
    {
        $job = Mockery::mock(QueueJob::class);
        $job->shouldReceive('payload')->andReturn([
            'uuid' => 'job-123',
            'displayName' => 'Tests\\Jobs\\ExampleJob',
        ]);
        $job->shouldReceive('getQueue')->andReturn('default');
        $job->shouldReceive('attempts')->andReturn(2);

        event(new JobFailed('database', $job, new RuntimeException('Boom')));

        $this->assertDatabaseHas('job_logs', [
            'job_uuid' => 'job-123',
            'job_class' => 'Tests\\Jobs\\ExampleJob',
            'status' => 'failed',
            'attempts' => 2,
        ]);
    }

    public function test_module_health_reporter_creates_and_updates_health_checks(): void
    {
        $reporter = app(ModuleHealthReporter::class);

        $warning = $reporter->warning('logging', 'Queue worker unavailable', ['tenant_id' => $this->defaultTenant->id]);
        $updated = $reporter->warning('logging', 'Queue worker unavailable', ['tenant_id' => $this->defaultTenant->id, 'check' => 'repeat']);
        $healthy = $reporter->healthy('logging', 'Queue worker restored', ['tenant_id' => $this->defaultTenant->id]);

        $this->assertInstanceOf(ModuleHealthCheck::class, $warning);
        $this->assertSame($warning->id, $updated->id);
        $this->assertNotSame($warning->id, $healthy->id);
        $this->assertSame('healthy', $healthy->status);
    }

    public function test_operator_event_can_be_acknowledged_and_resolved(): void
    {
        $event = app(OperatorEventLogger::class)->record(
            'logging.test.operator',
            'Test operator event',
            'Testing transitions',
            ['tenant_id' => $this->defaultTenant->id],
            'warning',
            'open'
        );

        (new AcknowledgeOperatorEvents())->handle(new ActionFields(new Collection(), new Collection()), collect([$event]));
        $this->assertSame('acknowledged', $event->fresh()->status);

        (new ResolveOperatorEvents())->handle(new ActionFields(new Collection(), new Collection()), collect([$event->fresh()]));
        $this->assertSame('resolved', $event->fresh()->status);
    }

    public function test_logging_service_does_not_fatal_if_database_write_fails(): void
    {
        \Schema::drop('system_logs');

        $result = app(SystemLogger::class)->error('logging.test.db-fail', 'This should not fatal', [
            'module_key' => 'logging',
        ]);

        $this->assertNull($result);
        $this->assertFalse(\Schema::hasTable('system_logs'));
    }
}
