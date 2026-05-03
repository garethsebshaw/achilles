<?php

namespace App\Nova\Actions;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Collection;

class ViewSessionUsers extends Action
{
    use InteractsWithQueue, Queueable;

    public $showOnTableRow = true;
    public $showOnIndex = false;

    public function name()
    {
        return __('Check In/Out');
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        $workoutSession = $models->first();
        return Action::visit('/resources/workout-signups?resourceId=' . $workoutSession->id);
    }

    public function fields(NovaRequest $request)
    {
        return [];
    }
}
