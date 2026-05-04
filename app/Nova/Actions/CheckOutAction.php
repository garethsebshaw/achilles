<?php

namespace App\Nova\Actions;

use App\Models\SystemStatus;
use App\Models\WorkoutSignup;
use Carbon\Carbon;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Closure;

class CheckOutAction extends Action
{
    public function name()
    {
        return __('Check Out');
    }

    /**
     * Handle the action execution.
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            if (!$model->checked_in_at) {
                return Action::danger(__('User has not checked in yet.'));
            }

            if ($model->checked_out_at) {
                return Action::danger(__('User Already Checked Out.'));
            }

            $model->checked_out_at = Carbon::now();
            $model->status_id = SystemStatus::idForModel(WorkoutSignup::class, 'signup_checked_out', ['signup_attended', 'signup_checked_in']);
            $model->save();
        }

        return Action::message(__('User Checked Out Successfully!'));
    }

    /**
     * Fixes the method signature issue with `canRun()`
     */
    public function canRun(Closure $callback)
    {
        return $callback(function ($model) {
            return !is_null($model->checked_in_at) && is_null($model->checked_out_at);
        });
    }
}
