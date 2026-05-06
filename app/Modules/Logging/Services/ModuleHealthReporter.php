<?php

namespace App\Modules\Logging\Services;

use App\Modules\Logging\Models\ModuleHealthCheck;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class ModuleHealthReporter
{
    public function __construct(
        protected CurrentTenant $currentTenant,
        protected OperatorEventLogger $operatorEventLogger,
    ) {
    }

    public function healthy(string $moduleKey, string $summary, array $details = []): ?ModuleHealthCheck
    {
        return $this->report($moduleKey, 'healthy', 'low', $summary, $details);
    }

    public function warning(string $moduleKey, string $summary, array $details = []): ?ModuleHealthCheck
    {
        return $this->report($moduleKey, 'warning', 'medium', $summary, $details);
    }

    public function degraded(string $moduleKey, string $summary, array $details = []): ?ModuleHealthCheck
    {
        return $this->report($moduleKey, 'degraded', 'high', $summary, $details);
    }

    public function failing(string $moduleKey, string $summary, array $details = []): ?ModuleHealthCheck
    {
        return $this->report($moduleKey, 'failing', 'critical', $summary, $details);
    }

    public function disabled(string $moduleKey, string $summary, array $details = []): ?ModuleHealthCheck
    {
        return $this->report($moduleKey, 'disabled', 'high', $summary, $details);
    }

    protected function report(string $moduleKey, string $status, string $severity, string $summary, array $details = []): ?ModuleHealthCheck
    {
        $tenantId = array_key_exists('tenant_id', $details) ? $details['tenant_id'] : $this->currentTenant->id();

        try {
            $current = ModuleHealthCheck::query()
                ->where('module_key', $moduleKey)
                ->where('tenant_id', $tenantId)
                ->whereNull('resolved_at')
                ->latest('last_checked_at')
                ->first();

            if ($current
                && $current->status === $status
                && $current->severity === $severity
                && $current->summary === $summary) {
                $current->forceFill([
                    'details' => $details,
                    'last_checked_at' => now(),
                ])->save();

                return $current;
            }

            if ($current) {
                $current->forceFill([
                    'resolved_at' => Carbon::now(),
                ])->save();
            }

            $record = ModuleHealthCheck::query()->create([
                'tenant_id' => $tenantId,
                'module_key' => $moduleKey,
                'status' => $status,
                'severity' => $severity,
                'summary' => $summary,
                'details' => $details,
                'last_checked_at' => now(),
                'resolved_at' => null,
            ]);

            if (config('rsc_logging.operator_events.auto_create_for_unhealthy_modules', true)
                && in_array($status, ['warning', 'degraded', 'failing', 'disabled'], true)) {
                $this->operatorEventLogger->record(
                    'module-health.'.$status,
                    $summary,
                    null,
                    [
                        'tenant_id' => $tenantId,
                        'module_key' => $moduleKey,
                        'health_check_id' => $record->id,
                    ],
                    $status === 'failing' ? 'critical' : 'warning',
                    'open'
                );
            }

            return $record;
        } catch (Throwable $throwable) {
            Log::error('Module health persistence failed', [
                'module_key' => $moduleKey,
                'status' => $status,
                'error' => $throwable->getMessage(),
            ]);

            return null;
        }
    }
}
