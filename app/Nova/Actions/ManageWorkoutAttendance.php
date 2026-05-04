<?php

namespace App\Nova\Actions;

use App\Models\SystemStatus;
use App\Models\WorkoutSignup;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Collection;

class ManageWorkoutAttendance extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Get the displayable name of the action.
     *
     * @return string
     */
    public function name()
    {
        return __('Manage Attendance');
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $model) {
            switch ($fields->action) {
                case 'check-in':
                    $model->checked_in_at = now();
                    $model->status_id = SystemStatus::idForModel(WorkoutSignup::class, 'signup_checked_in', ['signup_confirmed', 'signup_pending']);
                    break;
                case 'check-out':
                    $model->checked_out_at = now();
                    $model->status_id = SystemStatus::idForModel(WorkoutSignup::class, 'signup_checked_out', ['signup_attended', 'signup_checked_in']);
                    break;
                case 'cancel-check-in':
                    $model->checked_in_at = null;
                    $model->checked_out_at = null;
                    $model->status_id = SystemStatus::idForModel(WorkoutSignup::class, 'signup_confirmed', ['signup_pending']);
                    break;
            }

            $model->save();
        }

        return Action::message(__('Attendance updated successfully'));
    }

    /**
     * Get the fields available on the action.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            Select::make(__('Action'))
                ->options([
                    'check-in' => __('Check In'),
                    'check-out' => __('Check Out'),
                    'cancel-check-in' => __('Cancel Check In'),
                ])
                ->rules('required'),
        ];
    }
}
