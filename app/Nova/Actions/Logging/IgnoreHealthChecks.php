<?php

namespace App\Nova\Actions\Logging;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Collection;

class IgnoreHealthChecks extends Action
{
    use Queueable;

    public function name()
    {
        return __('Mark Ignored');
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            $details = is_array($model->details) ? $model->details : [];
            $details['ignored'] = true;
            $model->forceFill([
                'details' => $details,
                'resolved_at' => now(),
            ])->save();
        }

        return Action::message(__('Health checks marked ignored.'));
    }
}
