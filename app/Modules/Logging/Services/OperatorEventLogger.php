<?php

namespace App\Modules\Logging\Services;

use App\Modules\Logging\Models\OperatorEvent;
use App\Modules\Logging\Support\LogContextSanitizer;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class OperatorEventLogger
{
    public function __construct(
        protected CurrentTenant $currentTenant,
        protected LogContextSanitizer $sanitizer,
    ) {
    }

    public function record(
        string $eventKey,
        string $title,
        ?string $description = null,
        array $metadata = [],
        string $severity = 'info',
        string $status = 'open'
    ): ?OperatorEvent {
        try {
            return OperatorEvent::query()->create([
                'tenant_id' => array_key_exists('tenant_id', $metadata) ? $metadata['tenant_id'] : $this->currentTenant->id(),
                'user_id' => $metadata['user_id'] ?? Auth::id(),
                'module_key' => $metadata['module_key'] ?? null,
                'event_key' => $eventKey,
                'title' => $title,
                'description' => $description,
                'severity' => $severity,
                'status' => $status,
                'metadata' => $this->sanitizer->sanitize($metadata),
            ]);
        } catch (Throwable $throwable) {
            Log::error('Operator event persistence failed', [
                'event_key' => $eventKey,
                'title' => $title,
                'error' => $throwable->getMessage(),
            ]);

            return null;
        }
    }
}
