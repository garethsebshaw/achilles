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

            Text::make(__('Name'))
                ->rules('required', 'max:255')
                ->sortable(),

            BelongsTo::make(__('Location'), 'location', SystemLocation::class)
                ->rules('required'),

            BelongsTo::make(__('Activity Type'), 'activityType', SystemCategory::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    $workoutsModuleId = \App\Models\SystemModule::where('model_type', 'App\Models\Workout')->first()->id;
                    return $query->where('system_module_id', $workoutsModuleId);
                }),

            Number::make(__('Version'))
                ->readonly(),

            Text::make(__('Default Start Time'))
                ->rules('required', 'date_format:H:i'),

            Text::make(__('Default End Time'))
                ->rules('required', 'date_format:H:i'),

            Boolean::make(__('Is Recurring'))
                ->default(true),

            Text::make(__('Recurrence Pattern'))
                ->nullable(),

            Number::make(__('Advance Create Weeks'))
                ->default(52)
                ->min(1)
                ->max(52),

            Number::make(__('Default Max Athletes'))
                ->nullable()
                ->min(0),

            Number::make(__('Default Max Guides'))
                ->nullable()
                ->min(0),

            Boolean::make(__('Is Template'))
                ->default(false),

            Boolean::make(__('Is Current Version'))
                ->default(true)
                ->readonly(),

            BelongsTo::make(__('Created By'), 'createdBy', User::class)
                ->exceptOnForms(),

            HasMany::make(__('Sessions'), 'sessions', WorkoutSession::class),
            HasMany::make(__('Meeting Points'), 'meetingPoints', MeetingPoint::class),

            new Panel(__('Related Lists'), [
                \Laravel\Nova\Fields\HasMany::make(__('Categories'), 'categories', SystemCategory::class),
                \Laravel\Nova\Fields\HasMany::make(__('Statuses'), 'statuses', SystemStatus::class),
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

        return __('W/O Templates');
    }

}
