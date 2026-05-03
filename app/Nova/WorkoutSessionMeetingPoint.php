<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Http\Requests\NovaRequest;

class WorkoutSessionMeetingPoint extends Resource
{
    public static $model = \App\Models\WorkoutSessionMeetingPoint::class;

    public static $title = 'id';

    public static $search = [
        'id',
    ];

    public static $group = 'Workout Management';

    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make('Workout Session', 'workoutSession', WorkoutSession::class)
                ->rules('required'),

            BelongsTo::make('Meeting Point', 'meetingPoint', MeetingPoint::class)
                ->rules('required'),

            Boolean::make('Is Primary')
                ->default(false)
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
        return 'workout-meeting-points';
    }

    public static function label() {

        return 'Session Meet. Points';
    }
}
