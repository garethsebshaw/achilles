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
        $workoutsModuleId = \App\Models\SystemModule::where('name', 'Workouts')->first()->id;

        return [
            ID::make()->sortable(),

            BelongsTo::make('Workout Signup', 'workoutSignup', WorkoutSignup::class)
                ->rules('required'),

            BelongsTo::make('Sport Category', 'sportCategory', SystemCategory::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    $workoutsModuleId = \App\Models\SystemModule::where('name', 'Workouts')->first()->id;
                    return $query->where('system_module_id', $workoutsModuleId);
                }),

            BelongsTo::make('Distance Unit', 'distanceUnit', SystemStatus::class),
            BelongsTo::make('Pace Unit', 'paceUnit', SystemStatus::class),
            BelongsTo::make('Speed Unit', 'speedUnit', SystemStatus::class),

            Number::make('Distance')
                ->nullable(),

            Number::make('Time')
                ->nullable(),

            Number::make('Pace Min')
                ->nullable(),

            Number::make('Pace Max')
                ->nullable(),

            Number::make('Speed Min')
                ->nullable(),

            Number::make('Speed Max')
                ->nullable(),

            Code::make('Additional Details')
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

        return 'Signup Details';
    }
}
