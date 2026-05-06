<?php

namespace App\Nova;

use App\Models\WorkoutSession as WorkoutSessionModel;
use App\Support\Attendance\CheckInSessionContext;
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
use App\Nova\Filters\SessionSchedulePresetFilter;
use App\Nova\Filters\SessionSignupPresenceFilter;
use App\Nova\Actions\GenerateDemoSessionSignups;

class WorkoutSession extends Resource
{
    public static $model = \App\Models\WorkoutSession::class;

    public static $perPageOptions = [25, 50, 100];

    public function title()
    {
        return $this->workout ? $this->workout->name : __('Unnamed Workout');
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
        $query = $query
            ->with(['workout.activityType', 'location', 'status'])
            ->withCount([
                'signups as total_signups_count',
                'signups as athlete_signups_count' => function ($signupQuery) {
                    $signupQuery->whereHas('user', function ($userQuery) {
                        $userQuery->where('is_athlete', true);
                    });
                },
                'signups as guide_signups_count' => function ($signupQuery) {
                    $signupQuery->whereHas('user', function ($userQuery) {
                        $userQuery->where('is_guide', true);
                    });
                },
            ]);

        if (! $request->filled('orderBy')) {
            $query->orderBy('session_date')
                ->orderBy('start_time')
                ->orderBy('id');
        }

        return $query;
    }

    public static $group = 'Workout Management';

    public function fields(NovaRequest $request)
    {
        $withinWindow = $this->isWithinCheckInWindow();

        return [
            ID::make()->sortable(),

            BelongsTo::make(__('Location'), 'location', SystemLocation::class)
                ->rules('required')
                ->sortable(),

            Text::make(__('Sport'), function () {
                return optional($this->workout->activityType)->name ?? __('Unknown Sport');
            })
                ->sortable()
                ->filterable(),

            BelongsTo::make(__('Workout'))
                ->nullable()
                ->hideFromIndex(),

            Number::make(__('W/O Ver.'), 'workout_version')
                ->default(1)
                ->hideFromIndex(),

            Button::make($this->checkInButtonLabel($request))
                ->link($this->checkInButtonLink($request), '_self')
                ->style($this->checkInButtonStyle($request))
                ->visible($this->shouldShowCheckInButton($request)),

            Text::make(__('Session Date'))
                ->rules('required')
                ->displayUsing(fn ($value) => $value ? $value->format('D d/m/Y') : '')
                ->showOnIndex()
                ->sortable(),

            Text::make(__('Start Time'))
                ->rules('required')
                ->displayUsing(fn ($value) => $value ? $value->format('g:ia') : '')
                ->sortable(),

            Text::make(__('End Time'))
                ->rules('required')
                ->displayUsing(fn ($value) => $value ? $value->format('g:ia') : '')
                ->sortable(),

            Number::make(__('Total Signups'), 'total_signups_count')
                ->sortable()
                ->textAlign('center'),

            Number::make(__('Athletes'), 'athlete_signups_count')
                ->sortable()
                ->textAlign('center'),

            Number::make(__('Guides'), 'guide_signups_count')
                ->sortable()
                ->textAlign('center'),

            Number::make(__('Max Athletes'))
                ->nullable()
                ->min(0)
                ->hideFromIndex(),

            Number::make(__('Max Guides'))
                ->nullable()
                ->min(0)
                ->hideFromIndex(),

            BelongsTo::make(__('Status'), 'status', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    return $query->forModelType(\App\Models\WorkoutSession::class);
                }),

            Text::make(__('Cancellation Reason'))
                ->nullable()
            ->hideFromIndex(),

            BelongsTo::make(__('Cancelled By'), 'cancelledBy', User::class)
                ->onlyOnDetail()
                ->nullable(),

            DateTime::make(__('Cancelled At'))
                ->onlyOnDetail()
                ->nullable(),

            Text::make(__('Notes'))
                ->hideFromIndex()
                ->nullable(),


            HasMany::make(__('Signups'), 'signups', WorkoutSignup::class),

            HasMany::make(__('Meeting Points'), 'meetingPoints', MeetingPoint::class),
        ];
    }

    public function cards(NovaRequest $request)
    {
        return [
            new Metrics\SessionWindowValue(),
            new Metrics\SignupWindowValue(),
        ];
    }

    public function filters(NovaRequest $request)
    {
        return [
            new Filters\SessionSchedulePresetFilter(),
            new Filters\SessionSignupPresenceFilter(),
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
            (new GenerateDemoSessionSignups())
                ->canSee(fn (NovaRequest $actionRequest) => (bool) $actionRequest->user()?->isAdmin() || (bool) $actionRequest->user()?->isSysAdmin()),
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

        return __('Sessions');
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

    protected function shouldShowCheckInButton(NovaRequest $request): bool
    {
        if (! $this->resource || ! $this->resource->exists || ! $this->resource->getKey()) {
            return false;
        }

        if (! $this->isWithinCheckInWindow()) {
            return false;
        }

        $user = $request->user();

        if (! $user) {
            return false;
        }

        return app(CheckInSessionContext::class)->canManageSession($user, $this->resource);
    }

    protected function checkInButtonLabel(NovaRequest $request): string
    {
        if (! $this->resource || ! $this->resource->exists || ! $this->resource->getKey()) {
            return __('Start Check-In');
        }

        $context = app(CheckInSessionContext::class);

        if ($context->isActiveForSession($this->resource)) {
            return __('Stop Check-In');
        }

        return __('Start Check-In');
    }

    protected function checkInButtonLink(NovaRequest $request): string
    {
        if (! $this->resource || ! $this->resource->exists || ! $this->resource->getKey()) {
            return '#';
        }

        $context = app(CheckInSessionContext::class);

        if ($context->isActiveForSession($this->resource)) {
            return route('attendance.sessions.deactivate');
        }

        return route('attendance.sessions.activate', ['session' => $this->id]);
    }

    protected function checkInButtonStyle(NovaRequest $request): string
    {
        $context = app(CheckInSessionContext::class);

        return $context->isActiveForSession($this->resource) ? 'danger' : 'success';
    }
}
