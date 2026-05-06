<?php

namespace App\Modules\Logging\Services;

use App\Modules\Logging\Models\JobLog;
use App\Modules\Logging\Support\LogContextSanitizer;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class JobLogger
{
    public function __construct(
        protected CurrentTenant $currentTenant,
        protected LogContextSanitizer $sanitizer,
        protected OperatorEventLogger $operatorEventLogger,
    ) {
    }

    public function queued(array $attributes): ?JobLog
    {
        return $this->persist('queued', $attributes);
    }

    public function started(array $attributes): ?JobLog
    {
        return $this->persist('running', array_merge($attributes, [
            'started_at' => $attributes['started_at'] ?? now(),
        ]));
    }

    public function succeeded(array $attributes): ?JobLog
    {
        return $this->persist('succeeded', array_merge($attributes, [
            'finished_at' => $attributes['finished_at'] ?? now(),
        ]));
    }

    public function failed(array $attributes): ?JobLog
    {
        $log = $this->persist('failed', array_merge($attributes, [
            'finished_at' => $attributes['finished_at'] ?? now(),
        ]));

        if ($log && config('rsc_logging.operator_events.auto_create_for_failed_jobs', true)) {
            $this->operatorEventLogger->record(
                'job.failed',
                'Job failed: '.$log->job_class,
                $log->exception_message,
                [
                    'tenant_id' => $log->tenant_id,
                    'job_log_id' => $log->id,
                    'module_key' => 'logging',
                    'job_uuid' => $log->job_uuid,
                    'queue' => $log->queue,
                ],
                'error',
                'open'
            );
        }

        return $log;
    }

    public function released(array $attributes): ?JobLog
    {
        return $this->persist('released', $attributes);
    }

    protected function persist(string $status, array $attributes): ?JobLog
    {
        try {
            $jobUuid = $attributes['job_uuid'] ?? null;
            $tenantId = array_key_exists('tenant_id', $attributes) ? $attributes['tenant_id'] : $this->currentTenant->id();
            $record = $jobUuid
                ? JobLog::query()->firstOrNew(['job_uuid' => $jobUuid])
                : new JobLog();

            $record->fill([
                'tenant_id' => $tenantId,
                'job_uuid' => $jobUuid,
                'job_class' => $attributes['job_class'],
                'queue' => $attributes['queue'] ?? null,
                'connection' => $attributes['connection'] ?? null,
                'status' => $status,
                'attempts' => $attributes['attempts'] ?? 0,
                'started_at' => $attributes['started_at'] ?? $record->started_at,
                'finished_at' => $attributes['finished_at'] ?? $record->finished_at,
                'duration_ms' => $this->resolveDuration($attributes, $record),
                'exception_class' => $attributes['exception_class'] ?? null,
                'exception_message' => $attributes['exception_message'] ?? null,
                'trace_excerpt' => $attributes['trace_excerpt'] ?? null,
                'payload_summary' => $this->sanitizer->sanitize($attributes['payload_summary'] ?? []),
                'metadata' => $this->sanitizer->sanitize($attributes['metadata'] ?? []),
            ]);
            $record->save();

            return $record->fresh();
        } catch (Throwable $throwable) {
            Log::error('Job log persistence failed', [
                'status' => $status,
                'job_class' => $attributes['job_class'] ?? null,
                'error' => $throwable->getMessage(),
            ]);

            return null;
        }
    }

    protected function resolveDuration(array $attributes, JobLog $record): ?int
    {
        if (! empty($attributes['duration_ms'])) {
            return (int) $attributes['duration_ms'];
        }

        $startedAt = $attributes['started_at'] ?? $record->started_at;
        $finishedAt = $attributes['finished_at'] ?? null;

        if (! $startedAt || ! $finishedAt) {
            return $record->duration_ms;
        }

        return Carbon::parse($startedAt)->diffInMilliseconds(Carbon::parse($finishedAt));
    }
}
