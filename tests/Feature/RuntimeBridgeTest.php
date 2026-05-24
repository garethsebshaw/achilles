<?php

namespace Tests\Feature;

use App\Modules\Logging\Models\JobLog;
use App\Modules\Logging\Services\SystemLogger;
use App\Support\RuntimeBridge\BridgeRequestSigner;
use Database\Seeders\Logging\DefaultTenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class RuntimeBridgeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DefaultTenantSeeder::class);

        config()->set('runtime_bridge.enabled', true);
        config()->set('runtime_bridge.consumer', 'codex');
        config()->set('runtime_bridge.shared_secret', 'test-runtime-bridge-secret');
        config()->set('runtime_bridge.max_skew_seconds', 300);
    }

    public function test_runtime_bridge_rejects_unsigned_requests(): void
    {
        $this->getJson('/internal/runtime/health')
            ->assertUnauthorized()
            ->assertJson([
                'error' => 'Invalid runtime bridge signature.',
            ]);
    }

    public function test_runtime_bridge_health_endpoint_returns_platform_summary(): void
    {
        $response = $this->getJson('/internal/runtime/health', $this->signedHeaders('/internal/runtime/health'));

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'generated_at',
                'application' => ['name', 'environment', 'laravel', 'php', 'url'],
                'tenant' => ['resolved_id', 'default_key', 'count'],
                'database' => ['default_connection'],
                'logging' => ['system_logs', 'open_operator_events', 'unhealthy_modules'],
            ])
            ->assertJson([
                'status' => 'ok',
                'tenant' => [
                    'default_key' => 'achilles',
                ],
            ]);
    }

    public function test_runtime_bridge_logs_endpoint_returns_filtered_structured_logs(): void
    {
        app(SystemLogger::class)->info('portal.loaded', 'Portal loaded', [
            'module_key' => 'portal',
        ]);

        app(SystemLogger::class)->warning('logging.warning', 'Logging warning', [
            'module_key' => 'logging',
        ]);

        $response = $this->getJson(
            '/internal/runtime/logs?module=portal&limit=5',
            $this->signedHeaders('/internal/runtime/logs?module=portal&limit=5')
        );

        $response->assertOk()
            ->assertJsonPath('filters.module', 'portal')
            ->assertJsonPath('filters.limit', 5)
            ->assertJsonCount(1, 'logs')
            ->assertJsonPath('logs.0.event_key', 'portal.loaded');
    }

    public function test_runtime_bridge_queue_summary_reports_recent_failures(): void
    {
        JobLog::query()->create([
            'job_uuid' => 'job-123',
            'job_class' => 'App\\Jobs\\ExampleJob',
            'queue' => 'default',
            'status' => 'failed',
            'attempts' => 2,
            'exception_class' => 'RuntimeException',
            'exception_message' => 'Queue failure',
            'finished_at' => now(),
        ]);

        $response = $this->getJson(
            '/internal/runtime/queue-summary',
            $this->signedHeaders('/internal/runtime/queue-summary')
        );

        $response->assertOk()
            ->assertJsonPath('job_status_counts.failed', 1)
            ->assertJsonPath('recent_failures.0.job_uuid', 'job-123');
    }

    private function signedHeaders(string $uri): array
    {
        $timestamp = (string) now()->timestamp;
        $request = Request::create($uri, 'GET');
        $consumer = config('runtime_bridge.consumer');
        $secret = config('runtime_bridge.shared_secret');
        $signature = app(BridgeRequestSigner::class)->signatureFor($request, $consumer, $timestamp, $secret);

        return [
            'X-Bridge-Consumer' => $consumer,
            'X-Bridge-Timestamp' => $timestamp,
            'X-Bridge-Signature' => $signature,
        ];
    }
}
