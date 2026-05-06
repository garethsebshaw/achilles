<?php

namespace App\Modules\Logging\Services;

use App\Modules\Logging\Models\SystemLog;
use App\Modules\Logging\Support\LogContextSanitizer;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class SystemLogger
{
    protected const RESERVED_CONTEXT_KEYS = [
        'tenant_id',
        'user_id',
        'module_key',
        'source_type',
        'source_id',
        'request_id',
        'correlation_id',
        'ip_address',
        'user_agent',
        'url',
        'method',
    ];

    public function __construct(
        protected CurrentTenant $currentTenant,
        protected LogContextSanitizer $sanitizer,
        protected OperatorEventLogger $operatorEventLogger,
    ) {
    }

    public function debug(string $eventKey, string $message, array $context = []): ?SystemLog
    {
        return $this->write('debug', $eventKey, $message, $context);
    }

    public function info(string $eventKey, string $message, array $context = []): ?SystemLog
    {
        return $this->write('info', $eventKey, $message, $context);
    }

    public function warning(string $eventKey, string $message, array $context = []): ?SystemLog
    {
        return $this->write('warning', $eventKey, $message, $context);
    }

    public function error(string $eventKey, string $message, array $context = []): ?SystemLog
    {
        return $this->write('error', $eventKey, $message, $context);
    }

    public function critical(string $eventKey, string $message, array $context = []): ?SystemLog
    {
        return $this->write('critical', $eventKey, $message, $context);
    }

    protected function write(string $level, string $eventKey, string $message, array $context = []): ?SystemLog
    {
        $payload = $this->buildPayload($level, $eventKey, $message, $context);
        $sanitizedContext = $payload['context'] ?? [];

        $this->writeLaravelLog($level, $eventKey, $message, $sanitizedContext);

        if (! config('rsc_logging.enabled') || ! config('rsc_logging.database_logging')) {
            return null;
        }

        try {
            $log = SystemLog::query()->create($payload);

            if ($this->shouldCreateOperatorEvent($level)) {
                $this->operatorEventLogger->record(
                    $eventKey,
                    $message,
                    null,
                    [
                        'log_id' => $log->id,
                        'level' => $level,
                        'module_key' => $payload['module_key'],
                        'tenant_id' => $payload['tenant_id'],
                    ],
                    $level,
                    'open'
                );
            }

            return $log;
        } catch (Throwable $throwable) {
            Log::error('Structured system log persistence failed', [
                'event_key' => $eventKey,
                'message' => $message,
                'error' => $throwable->getMessage(),
            ]);

            return null;
        }
    }

    protected function writeLaravelLog(string $level, string $eventKey, string $message, array $context): void
    {
        $logContext = array_merge($context, [
            'event_key' => $eventKey,
            'tenant_id' => $context['tenant_id'] ?? $this->currentTenant->id(),
            'user_id' => $context['user_id'] ?? Auth::id(),
        ]);

        try {
            Log::log($level, $message, $logContext);

            if (config('logging.channels.rsc_structured')) {
                Log::channel('rsc_structured')->log($level, $message, $logContext);
            }
        } catch (Throwable $throwable) {
            Log::channel('stack')->error('Laravel log write failed while handling structured log event', [
                'event_key' => $eventKey,
                'error' => $throwable->getMessage(),
            ]);
        }
    }

    protected function buildPayload(string $level, string $eventKey, string $message, array $context): array
    {
        $sanitized = $this->sanitizer->sanitize($context);

        return [
            'tenant_id' => array_key_exists('tenant_id', $context) ? $context['tenant_id'] : $this->currentTenant->id(),
            'user_id' => $context['user_id'] ?? Auth::id(),
            'module_key' => $context['module_key'] ?? null,
            'source_type' => $context['source_type'] ?? null,
            'source_id' => $context['source_id'] ?? null,
            'level' => $level,
            'event_key' => $eventKey,
            'message' => $message,
            'context' => $this->filteredContext($sanitized),
            'request_id' => $context['request_id'] ?? request()?->header('X-Request-Id'),
            'correlation_id' => $context['correlation_id'] ?? request()?->header('X-Correlation-Id'),
            'ip_address' => $context['ip_address'] ?? request()?->ip(),
            'user_agent' => $context['user_agent'] ?? request()?->userAgent(),
            'url' => $context['url'] ?? request()?->fullUrl(),
            'method' => $context['method'] ?? request()?->method(),
        ];
    }

    protected function filteredContext(array $context): array
    {
        return collect($context)
            ->except(self::RESERVED_CONTEXT_KEYS)
            ->all();
    }

    protected function shouldCreateOperatorEvent(string $level): bool
    {
        return config('rsc_logging.operator_events.auto_create_for_critical_logs', true)
            && in_array($level, ['critical', 'alert', 'emergency'], true);
    }
}
