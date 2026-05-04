<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\JSON;
use Laravel\Nova\Http\Requests\NovaRequest;

class WorkoutEquipmentAssignment extends Resource
{
    public static $model = \App\Models\WorkoutEquipmentAssignment::class;

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

            BelongsTo::make(__('Equipment'))
                ->rules('required'),

            BelongsTo::make(__('Assignment Type'), 'assignmentType', SystemCategory::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    $workoutAssignmentModuleId = \App\Models\SystemModule::where('model_type', \App\Models\WorkoutEquipmentAssignment::class)->value('id');

                    return $query->where('system_module_id', $workoutAssignmentModuleId);
                }),

            Code::make(__('Fitting Details'))
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

    public static function label() {

        return __('W/O Equip. Assign.');
    }
}
