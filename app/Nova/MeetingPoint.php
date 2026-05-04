<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;
//use App\Nova\SystemModule;
//use App\Nova\SystemCategory;
//use App\Nova\SystemChapter;
//use App\Nova\User;
//use App\Nova\Workout;
//use App\Nova\WorkoutSession;

class MeetingPoint extends Resource
{
    public static $model = \App\Models\MeetingPoint::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name', 'address'
    ];

    public static $group = 'Workout Management';

    public function fields(NovaRequest $request)
    {

        $model = 'App\Models\Workout';

        $workoutsModule = \App\Models\SystemModule::where('model_type', $model)->first();

        if (!$workoutsModule) {
            throw new \Exception('Workouts module not found. Please run the SystemModuleSeeder.');
        }

        //$workoutsModule = SystemModule::where('name', 'Workouts')->first();
        $workoutsModuleId = $workoutsModule ? $workoutsModule->id : null;

        return [
            ID::make()->sortable(),

            BelongsTo::make(__('Workout Type'), 'type', SystemCategory::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query, $model=null) use ($workoutsModule) {
                    return $query->where('system_module_id', $workoutsModule->id);
                })
                ->rules('required'),

            Text::make(__('Name'))
                ->rules('required', 'max:255')
                ->sortable(),

            Text::make(__('Address'))
                ->rules('required'),

            Number::make(__('Latitude'))
                ->nullable()
                ->step(0.00000001),

            Number::make(__('Longitude'))
                ->nullable()
                ->step(0.00000001),

            BelongsTo::make(__('Created By'), 'createdBy', User::class)
                ->exceptOnForms(),

            BelongsTo::make(__('Chapter'), 'chapter', SystemChapter::class)
                ->nullable(),

            HasMany::make(__('Workout Templates'), 'workoutTemplates', Workout::class),
            HasMany::make(__('Workout Sessions'), 'workoutSessions', WorkoutSession::class),
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

        return __('W/O Meeting Points');
    }
}
