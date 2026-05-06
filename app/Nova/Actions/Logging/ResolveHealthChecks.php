<?php

namespace App\Nova\Actions\Logging;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Collection;

class ResolveHealthChecks extends Action
{
    use Queueable;

    public function name()
    {
        return __('Mark Resolved');
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            $model->forceFill(['resolved_at' => now()])->save();
        }

        return Action::message(__('Health checks marked resolved.'));
    }
}
