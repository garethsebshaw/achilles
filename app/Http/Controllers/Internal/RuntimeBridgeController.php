<?php

namespace App\Http\Controllers\Internal;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Modules\Logging\Models\JobLog;
use App\Modules\Logging\Models\ModuleHealthCheck;
use App\Modules\Logging\Models\OperatorEvent;
use App\Modules\Logging\Models\SystemLog;
use App\Support\Tenancy\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RuntimeBridgeController extends Controller
{
    public function health(CurrentTenant $currentTenant): JsonResponse
    {
        return response()->json([
            'status' => 'ok',
            'generated_at' => now()->toIso8601String(),
            'application' => [
                'name' => config('app.name'),
                'environment' => config('app.env'),
                'laravel' => app()->version(),
                'php' => PHP_VERSION,
                'url' => config('app.url'),
            ],
            'tenant' => [
                'resolved_id' => $currentTenant->id(),
                'default_key' => config('rsc_logging.default_tenant_key', 'achilles'),
                'count' => Tenant::query()->count(),
            ],
            'database' => [
                'default_connection' => config('database.default'),
            ],
            'logging' => [
                'system_logs' => SystemLog::query()->count(),
                'open_operator_events' => OperatorEvent::query()->where('status', 'open')->count(),
                'unhealthy_modules' => ModuleHealthCheck::query()
                    ->whereIn('status', ['warning', 'degraded', 'failing', 'disabled'])
                    ->count(),
            ],
        ]);
    }

    public function logs(Request $request): JsonResponse
    {
        $limit = max(1, min($request->integer('limit', 25), (int) config('runtime_bridge.max_log_limit', 100)));
        $level = $request->string('level')->toString();
        $moduleKey = $request->string('module')->toString();

        $query = SystemLog::query()
            ->select([
                'id',
                'tenant_id',
                'user_id',
                'module_key',
                'level',
                'event_key',
                'message',
                'request_id',
                'correlation_id',
                'created_at',
            ])
            ->latest('id');

        if ($level !== '') {
            $query->where('level', $level);
        }

        if ($moduleKey !== '') {
            $query->where('module_key', $moduleKey);
        }

        return response()->json([
            'generated_at' => now()->toIso8601String(),
            'filters' => [
                'level' => $level ?: null,
                'module' => $moduleKey ?: null,
                'limit' => $limit,
            ],
            'logs' => $query->limit($limit)->get(),
        ]);
    }

    public function queueSummary(): JsonResponse
    {
        $jobStatusCounts = JobLog::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $failedJobsCount = Schema::hasTable('failed_jobs')
            ? DB::table('failed_jobs')->count()
            : null;

        $recentFailures = JobLog::query()
            ->where('status', 'failed')
            ->latest('id')
            ->limit(10)
            ->get([
                'id',
                'job_uuid',
                'job_class',
                'queue',
                'attempts',
                'exception_class',
                'exception_message',
                'finished_at',
            ]);

        return response()->json([
            'generated_at' => now()->toIso8601String(),
            'job_status_counts' => $jobStatusCounts,
            'failed_jobs_table_count' => $failedJobsCount,
            'recent_failures' => $recentFailures,
        ]);
    }
}
