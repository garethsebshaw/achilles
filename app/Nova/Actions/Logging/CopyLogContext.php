<?php

namespace App\Nova\Actions\Logging;

use Illuminate\Bus\Queueable;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Support\Collection;

class CopyLogContext extends Action
{
    use Queueable;

    public function name()
    {
        return __('Copy Context');
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        $model = $models->first();

        return Action::message(json_encode($model?->context ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
