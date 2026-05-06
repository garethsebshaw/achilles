<?php

namespace App\Nova\Actions\Logging;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Collection;

class ResolveOperatorEvents extends Action
{
    use Queueable;

    public function name()
    {
        return __('Resolve');
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            $model->forceFill(['status' => 'resolved'])->save();
        }

        return Action::message(__('Operator events resolved.'));
    }
}
