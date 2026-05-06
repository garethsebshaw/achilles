<?php

namespace App\Nova\Actions\Logging;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class ExportAuditLogs extends Action
{
    use Queueable;

    public function name()
    {
        return __('Export Audit Logs');
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        $filename = 'audit-logs-'.now()->format('YmdHis').'.csv';
        $absolutePath = storage_path('app/public/tmp/'.$filename);

        File::ensureDirectoryExists(dirname($absolutePath));

        $handle = fopen($absolutePath, 'w');
        fputcsv($handle, ['id', 'tenant_id', 'user_id', 'action', 'auditable_type', 'auditable_id', 'created_at']);

        foreach ($models as $model) {
            fputcsv($handle, [
                $model->id,
                $model->tenant_id,
                $model->user_id,
                $model->action,
                $model->auditable_type,
                $model->auditable_id,
                optional($model->created_at)->toIso8601String(),
            ]);
        }

        fclose($handle);

        return Action::download(url('/storage/tmp/'.$filename), $filename);
    }
}
