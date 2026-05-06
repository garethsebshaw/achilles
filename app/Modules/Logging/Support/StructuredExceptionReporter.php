<?php

namespace App\Modules\Logging\Support;

use App\Modules\Logging\Services\SystemLogger;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;

class StructuredExceptionReporter
{
    public function __construct(
        protected SystemLogger $logger,
    ) {
    }

    public function report(Throwable $throwable): void
    {
        if (! config('rsc_logging.enabled')) {
            return;
        }

        if ($this->shouldSkip($throwable)) {
            return;
        }

        $this->logger->error(
            'exception.reported',
            $throwable->getMessage() !== '' ? $throwable->getMessage() : class_basename($throwable),
            [
                'tenant_id' => request()?->attributes->get('tenant_id'),
                'module_key' => 'logging',
                'source_type' => 'exception',
                'exception_class' => $throwable::class,
                'trace_excerpt' => $this->traceExcerpt($throwable),
                'file' => $throwable->getFile(),
                'line' => $throwable->getLine(),
            ]
        );
    }

    protected function shouldSkip(Throwable $throwable): bool
    {
        if ($throwable instanceof ValidationException
            || $throwable instanceof AuthenticationException
            || $throwable instanceof AuthorizationException
            || $throwable instanceof HttpResponseException) {
            return true;
        }

        return $throwable instanceof HttpExceptionInterface
            && $throwable->getStatusCode() < 500;
    }

    protected function traceExcerpt(Throwable $throwable): string
    {
        return (string) str($throwable->getTraceAsString())->limit(4000);
    }
}
