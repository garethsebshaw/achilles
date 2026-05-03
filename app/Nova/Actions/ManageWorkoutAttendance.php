<?php

namespace App\Nova\Actions;

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
        return 'Manage Attendance';
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
                    break;
                case 'check-out':
                    $model->checked_out_at = now();
                    break;
                case 'cancel-check-in':
                    $model->checked_in_at = null;
                    $model->checked_out_at = null;
                    break;
            }

            $model->save();
        }

        return Action::message('Attendance updated successfully');
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
            Select::make('Action')
                ->options([
                    'check-in' => 'Check In',
                    'check-out' => 'Check Out',
                    'cancel-check-in' => 'Cancel Check In',
                ])
                ->rules('required'),
        ];
    }
}
