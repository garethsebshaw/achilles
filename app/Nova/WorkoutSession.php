<?php

namespace App\Nova;

use App\Nova\Lenses\SessionWithin3Hours;
use App\Nova\Lenses\SessionWithin6Hours;
use App\Nova\Lenses\SessionWithin12Hours;
use Illuminate\Http\Request;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Contracts\Database\Eloquent\Builder;
use App\Nova\Lenses\SessionTimeLens;
use Sietse85\NovaButton\Button;
use Illuminate\Support\Facades\DB;

use App\Nova\Filters\TimeWindowFilter;
use App\Nova\Filters\ChapterFilter;
use App\Nova\Filters\WorkoutFilter;

class WorkoutSession extends Resource
{
    public static $model = \App\Models\WorkoutSession::class;

    public function title()
    {
        return $this->workout ? $this->workout->name : 'Unnamed Workout';
    }

    public static $search = [
        'session_date',
        'notes'
    ];

    /**
     * Get the searchable columns for the resource.
     *
     * @return array
     */
    public static function searchableColumns()
    {
        return ['session_date', 'notes', 'workout.name', 'location.name', 'status.name'];
    }
    /**
     * Get the relationships that should be eager loaded when performing an index query.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, Builder $query): Builder
    {
        return $query->with(['workout', 'location', 'status']);
    }

    public static $group = 'Workout Management';

    public function fields(NovaRequest $request)
    {
        $workoutsModuleId = \App\Models\SystemModule::where('name', 'Workouts')->first()->id;
        $withinWindow = $this->isWithinCheckInWindow();

        return [
            ID::make()->sortable(),

            BelongsTo::make('Location', 'location', SystemLocation::class)
                ->rules('required')
                ->sortable(),

            Text::make('Sport', function () {
                return optional($this->workout->activityType)->name ?? 'Unknown Sport';
            })
                ->sortable()
                ->filterable(),

            BelongsTo::make('Workout')
                ->nullable()
                ->hideFromIndex(),

            Number::make('W/O Ver.', 'workout_version')
                ->default(1)
                ->hideFromIndex(),

            Button::make('Check In Users')
                ->link('/resources/workout-signups?resourceId=' . $this->id, '_self')
                ->style('success')
                ->visible($this->isWithinCheckInWindow()),

            Text::make('Session Date')
                ->rules('required')
                ->displayUsing(fn ($value) => $value ? $value->format('D d/m/Y') : '')
                ->showOnIndex()
                ->sortable(),

            Text::make('Start Time')
                ->rules('required')
                ->displayUsing(fn ($value) => $value ? $value->format('g:ia') : '')
                ->sortable(),

            Text::make('End Time')
                ->rules('required')
                ->displayUsing(fn ($value) => $value ? $value->format('g:ia') : '')
                ->sortable(),

            Number::make('Total Signups', function() {
                return DB::table('workout_signups')
                    ->where('workout_session_id', $this->id)
                    ->distinct('user_id')
                    ->count('user_id');
            })
                ->sortable()
                ->textAlign('center'),

            Number::make('Athletes', function() {
                return DB::table('workout_signups')
                    ->join('users', 'workout_signups.user_id', '=', 'users.id')
                    ->where('workout_session_id', $this->id)
                    ->where('users.is_athlete', true)
                    ->distinct('workout_signups.user_id')
                    ->count('workout_signups.user_id');
            })
                ->sortable()
                ->textAlign('center'),

            Number::make('Guides', function() {
                return DB::table('workout_signups')
                    ->join('users', 'workout_signups.user_id', '=', 'users.id')
                    ->where('workout_session_id', $this->id)
                    ->where('users.is_guide', true)
                    ->distinct('workout_signups.user_id')
                    ->count('workout_signups.user_id');
            })
                ->sortable()
                ->textAlign('center'),

            Number::make('Max Athletes')
                ->nullable()
                ->min(0)
                ->hideFromIndex(),

            Number::make('Max Guides')
                ->nullable()
                ->min(0)
                ->hideFromIndex(),

            BelongsTo::make('Status', 'status', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    $workoutsModuleId = \App\Models\SystemModule::where('model_type', 'App\Models\Workout')->first()->id;
                    return $query->where('system_module_id', $workoutsModuleId);
                }),

            Text::make('Cancellation Reason')
                ->nullable()
            ->hideFromIndex(),

            BelongsTo::make('Cancelled By', 'cancelledBy', User::class)
                ->onlyOnDetail()
                ->nullable(),

            DateTime::make('Cancelled At')
                ->onlyOnDetail()
                ->nullable(),

            Text::make('Notes')
                ->hideFromIndex()
                ->nullable(),


            HasMany::make('Signups', 'signups', WorkoutSignup::class),

            HasMany::make('Meeting Points', 'meetingPoints', MeetingPoint::class),
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [];
    }

    public function filters(NovaRequest $request)
    {
        return [
            new Filters\TimeWindowFilter(),
            new Filters\ChapterFilter(), // Chapter should be first
            new Filters\LocationFilter(), // Location updates based on Chapter
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [
//            new Lenses\WorkoutSessionUsers,
//            new SessionWithin3Hours(),
//            new SessionWithin6Hours(),
//            new SessionWithin12Hours(),
//            new SessionTimeLens(),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [
            (new Actions\ViewSessionUsers)
                ->withoutConfirmation()
                ->showInline(),
        ];
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'workout-sessions';
    }

    public static function label() {

        return 'Sessions';
    }

// Helper method to check if session is within check-in window
    protected function isWithinCheckInWindow(): bool {
        if (!$this->session_date) {
            return false;
        }

        $sessionDateTime = $this->session_date->setTimeFrom($this->start_time);
        $now = now();

        // Get time differences in hours
        $hoursUntilSession = $now->diffInHours($sessionDateTime, false);

        // Return true if within ±12 hours
        return abs($hoursUntilSession) <= 12;
    }
}
