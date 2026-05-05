<?php

namespace App\Nova\Actions;

use App\Support\Attendance\CheckInSessionContext;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Collection;

class EndCheckInStaffSession extends Action
{
    use InteractsWithQueue, Queueable;

    public function name()
    {
        return __('End Check-In');
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        app(CheckInSessionContext::class)->deactivate();

        return Action::message(__('Check-in staff session ended.'));
    }

    public function fields(NovaRequest $request)
    {
        return [];
    }
}
