<?php

namespace App\Modules\Logging\Providers;

use App\Support\Tenancy\CurrentTenant;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Queue\Events\JobQueued;
use App\Modules\Logging\Services\JobLogger;

class LoggingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CurrentTenant::class, fn () => new CurrentTenant());
    }

    public function boot(): void
    {
        Event::listen(JobQueued::class, function (JobQueued $event): void {
            $payload = $event->payload();

            app(JobLogger::class)->queued([
                'job_uuid' => $payload['uuid'] ?? null,
                'job_class' => $payload['displayName'] ?? get_debug_type($event->job),
                'queue' => $event->queue,
                'connection' => $event->connectionName,
                'payload_summary' => $this->payloadSummary($payload),
                'metadata' => [
                    'delay' => $event->delay,
                    'job_id' => $event->id,
                ],
            ]);
        });

        Event::listen(JobProcessing::class, function (JobProcessing $event): void {
            $payload = $event->job->payload();

            app(JobLogger::class)->started([
                'job_uuid' => $payload['uuid'] ?? null,
                'job_class' => $payload['displayName'] ?? get_class($event->job),
                'queue' => $event->job->getQueue(),
                'connection' => $event->connectionName,
                'attempts' => $event->job->attempts(),
                'payload_summary' => $this->payloadSummary($payload),
            ]);
        });

        Event::listen(JobProcessed::class, function (JobProcessed $event): void {
            $payload = $event->job->payload();

            app(JobLogger::class)->succeeded([
                'job_uuid' => $payload['uuid'] ?? null,
                'job_class' => $payload['displayName'] ?? get_class($event->job),
                'queue' => $event->job->getQueue(),
                'connection' => $event->connectionName,
                'attempts' => $event->job->attempts(),
                'finished_at' => now(),
            ]);
        });

        Event::listen(JobFailed::class, function (JobFailed $event): void {
            $payload = $event->job->payload();

            app(JobLogger::class)->failed([
                'job_uuid' => $payload['uuid'] ?? null,
                'job_class' => $payload['displayName'] ?? get_class($event->job),
                'queue' => $event->job->getQueue(),
                'connection' => $event->connectionName,
                'attempts' => $event->job->attempts(),
                'exception_class' => $event->exception::class,
                'exception_message' => $event->exception->getMessage(),
                'trace_excerpt' => (string) str($event->exception->getTraceAsString())->limit(4000),
                'finished_at' => now(),
            ]);
        });
    }

    protected function payloadSummary(array $payload): array
    {
        return [
            'uuid' => $payload['uuid'] ?? null,
            'displayName' => $payload['displayName'] ?? null,
            'job' => $payload['job'] ?? null,
            'maxTries' => $payload['maxTries'] ?? null,
            'timeout' => $payload['timeout'] ?? null,
        ];
    }
}
