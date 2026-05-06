<?php

namespace App\Modules\Logging\Services;

use App\Modules\Logging\Models\AuditLog;
use App\Modules\Logging\Support\LogContextSanitizer;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class AuditLogger
{
    public function __construct(
        protected CurrentTenant $currentTenant,
        protected LogContextSanitizer $sanitizer,
    ) {
    }

    public function record(
        string $action,
        ?Model $auditable = null,
        array $before = [],
        array $after = [],
        array $metadata = []
    ): ?AuditLog {
        try {
            return AuditLog::query()->create([
                'tenant_id' => array_key_exists('tenant_id', $metadata) ? $metadata['tenant_id'] : $this->currentTenant->id(),
                'user_id' => $metadata['user_id'] ?? Auth::id(),
                'actor_type' => $metadata['actor_type'] ?? optional(Auth::user())?->getMorphClass(),
                'actor_id' => $metadata['actor_id'] ?? Auth::id(),
                'action' => $action,
                'auditable_type' => $auditable?->getMorphClass(),
                'auditable_id' => $auditable?->getKey(),
                'before' => $this->sanitizer->sanitize($before),
                'after' => $this->sanitizer->sanitize($after),
                'metadata' => $this->sanitizer->sanitize($metadata),
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
                'request_id' => request()?->header('X-Request-Id'),
            ]);
        } catch (Throwable $throwable) {
            Log::error('Audit log persistence failed', [
                'action' => $action,
                'auditable_type' => $auditable?->getMorphClass(),
                'auditable_id' => $auditable?->getKey(),
                'error' => $throwable->getMessage(),
            ]);

            return null;
        }
    }
}
