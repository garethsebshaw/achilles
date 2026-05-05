<?php

namespace App\Nova\Actions;

use App\Support\Attendance\CheckInSessionContext;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Collection;

class RefreshSessionWeather extends Action
{
    use InteractsWithQueue, Queueable;

    public function name()
    {
        return __('Refresh Weather');
    }

    public function handle(ActionFields $fields, Collection $models)
    {
        $context = app(CheckInSessionContext::class);
        $session = $context->currentSession();

        if (! $session) {
            return Action::danger(__('No active check-in session.'));
        }

        $context->refreshWeatherIfStale($session, true);

        return Action::message(__('Weather refreshed for the active session.'));
    }

    public function fields(NovaRequest $request)
    {
        return [];
    }
}
