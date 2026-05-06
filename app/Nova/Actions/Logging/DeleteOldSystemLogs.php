<?php

namespace App\Nova\Actions\Logging;

use App\Modules\Logging\Models\SystemLog;
use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Collection;

class DeleteOldSystemLogs extends Action
{
    use Queueable;

    public $standalone = true;

    public function name()
    {
        return __('Delete Old Logs');
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        $days = max((int) ($fields->days ?? 30), 1);

        $deleted = SystemLog::query()
            ->where('created_at', '<', now()->subDays($days))
            ->delete();

        return Action::message(__('Deleted :count logs older than :days days.', [
            'count' => $deleted,
            'days' => $days,
        ]));
    }

    public function fields(NovaRequest $request)
    {
        return [
            Number::make(__('Days'), 'days')
                ->default(30)
                ->min(1)
                ->rules('required', 'integer', 'min:1'),
        ];
    }
}
