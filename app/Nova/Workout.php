<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\Time;
use Laravel\Nova\Panel;

class Workout extends Resource
{
    public static $model = \App\Models\Workout::class;

    public static $title = 'name';

    public static $search = [
        'id', 'name'
    ];

    public static $group = 'Workout Management';

    public function fields(NovaRequest $request)
    {
        $workoutsModuleId = \App\Models\SystemModule::where('name', 'Workouts')->first()->id;

        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->rules('required', 'max:255')
                ->sortable(),

            BelongsTo::make('Location', 'location', SystemLocation::class)
                ->rules('required'),

            BelongsTo::make('Activity Type', 'activityType', SystemCategory::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    $workoutsModuleId = \App\Models\SystemModule::where('model_type', 'App\Models\Workout')->first()->id;
                    return $query->where('system_module_id', $workoutsModuleId);
                }),

            Number::make('Version')
                ->readonly(),

            Text::make('Default Start Time')
                ->rules('required', 'date_format:H:i'),

            Text::make('Default End Time')
                ->rules('required', 'date_format:H:i'),

            Boolean::make('Is Recurring')
                ->default(true),

            Text::make('Recurrence Pattern')
                ->nullable(),

            Number::make('Advance Create Weeks')
                ->default(52)
                ->min(1)
                ->max(52),

            Number::make('Default Max Athletes')
                ->nullable()
                ->min(0),

            Number::make('Default Max Guides')
                ->nullable()
                ->min(0),

            Boolean::make('Is Template')
                ->default(false),

            Boolean::make('Is Current Version')
                ->default(true)
                ->readonly(),

            BelongsTo::make('Created By', 'createdBy', User::class)
                ->exceptOnForms(),

            HasMany::make('Sessions', 'sessions', WorkoutSession::class),
            HasMany::make('Meeting Points', 'meetingPoints', MeetingPoint::class),

            new Panel('Related Lists', [
                \Laravel\Nova\Fields\HasMany::make('Categories', 'categories', SystemCategory::class),
                \Laravel\Nova\Fields\HasMany::make('Statuses', 'statuses', SystemStatus::class),
            ]),
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
        return 'workouts';
    }

    public static function label() {

        return 'W/O Templates';
    }

}
