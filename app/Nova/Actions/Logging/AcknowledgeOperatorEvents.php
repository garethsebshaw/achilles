<?php

namespace App\Nova\Actions\Logging;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Collection;

class AcknowledgeOperatorEvents extends Action
{
    use Queueable;

    public function name()
    {
        return __('Acknowledge');
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            $model->forceFill(['status' => 'acknowledged'])->save();
        }

        return Action::message(__('Operator events acknowledged.'));
    }
}
