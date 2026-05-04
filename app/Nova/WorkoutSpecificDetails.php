<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\JSON;
use Laravel\Nova\Http\Requests\NovaRequest;

class WorkoutSpecificDetails extends Resource
{
    public static $model = \App\Models\WorkoutSpecificDetails::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public static $group = 'Workout Management';

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make(__('Workout Signup'), 'workoutSignup', WorkoutSignup::class)
                ->rules('required'),

            BelongsTo::make(__('Sport Category'), 'sportCategory', SystemCategory::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    $workoutsModuleId = \App\Models\SystemModule::where('name', 'Workouts')->first()->id;
                    return $query->where('system_module_id', $workoutsModuleId);
                }),

            BelongsTo::make(__('Distance Unit'), 'distanceUnit', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    return $query->forModelType(\App\Models\WorkoutSpecificDetails::class);
                }),
            BelongsTo::make(__('Pace Unit'), 'paceUnit', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    return $query->forModelType(\App\Models\WorkoutSpecificDetails::class);
                }),
            BelongsTo::make(__('Speed Unit'), 'speedUnit', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    return $query->forModelType(\App\Models\WorkoutSpecificDetails::class);
                }),

            Number::make(__('Distance'))
                ->nullable(),

            Number::make(__('Time'))
                ->nullable(),

            Number::make(__('Pace Min'))
                ->nullable(),

            Number::make(__('Pace Max'))
                ->nullable(),

            Number::make(__('Speed Min'))
                ->nullable(),

            Number::make(__('Speed Max'))
                ->nullable(),

            Code::make(__('Additional Details'))
                ->json()
                ->nullable(),
        ];
    }


    public function cards(NovaRequest $request)
    {
        return [];
    }

    public function filters(NovaRequest $request)
    {
        return [];
    }

    public function lenses(NovaRequest $request)
    {
        return [];
    }

    public function actions(NovaRequest $request)
    {
        return [];
    }
    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'workout-specific-details';
    }

    public static function label() {

        return __('Signup Details');
    }
}
