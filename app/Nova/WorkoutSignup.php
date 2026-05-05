<?php

namespace App\Nova;

use App\Models\WorkoutSession as WorkoutSessionModel;
use App\Support\Attendance\CheckInSessionContext;
use App\Nova\Metrics\SessionContextMetric;
use App\Nova\Metrics\SessionAttendanceMetric;
use App\Nova\Metrics\SessionWeatherMetric;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Database\Eloquent\Builder;
use Sietse85\NovaButton\Button;
use Laravel\Nova\Panel;
use App\Events\CheckInUser;
use App\Events\CheckOutUser;
use App\Nova\Actions\CheckInAction;
use App\Nova\Actions\CheckOutAction;
use App\Nova\Actions\EndCheckInStaffSession;
use App\Nova\Actions\RefreshSessionWeather;
use Illuminate\Support\Collection;


class WorkoutSignup extends Resource
{
    public static $model = \App\Models\WorkoutSignup::class;

    public static $title = 'id';

    public static $perPageOptions = [25, 50, 100];

    public static $search = [
        'id',
        'status_id',
        'preferences'
    ];

    public static $group = 'Workout Management';

    protected static array $sessionRosterCache = [];

    /**
     * Get the searchable columns for the resource.
     */
    public static function searchableColumns(): array
    {
        return [
            'id',
            'preferences',
            'user.name',
            'user.email',
            'status.name',
            'workoutSession.session_date',
            'workoutSession.workout.name'
        ];
    }

    /**
     * Build an "index" query for the given resource.
     */
//    public static function indexQuery(NovaRequest $request, $query): Builder
//    {
//        $query = parent::indexQuery($request, $query);
//
//        // Add eager loading
//        $query->with(['user', 'status', 'workoutSession', 'workoutSession.workout']);
//
//        // Filter by workout session if provided
//        if ($request->input('workout_session_id')) {
//            $query->where('workout_session_id', $request->input('workout_session_id'));
//        }
//
//        return $query;
//    }
    public static function indexQuery(NovaRequest $request, $query): Builder
    {
        $query = parent::indexQuery($request, $query);
        $query->with([
            'user',
            'athleteUser',
            'status',
            'workoutSession.workout.activityType',
            'workoutSession.location',
        ]);

        $resourceId = static::resolvedSessionId($request);

        if ($resourceId !== null) {
            return $query
                ->where('workout_session_id', (int) $resourceId)
                ->orderBy('checked_in_at')
                ->orderBy('user_id');
        }

        if (! static::hasExplicitIndexScope($request)) {
            return $query->whereRaw('1 = 0');
        }

        return $query->orderByDesc('id');
    }

    public function fields(NovaRequest $request)
    {
        $resourceId = static::resolvedSessionId($request);
        $attendanceContextActive = static::hasActiveAttendanceContext($request, $this->workoutSession);
        $assignedAthleteOptions = static::athleteOptionsForSession(
            $this->workout_session_id ?: static::resolvedSessionId($request)
        );

        return [
            ID::make()->sortable(),

            Text::make(__('Role'), function () {
                $label = $this->user?->is_athlete ? 'A' : ($this->user?->is_guide ? 'G' : '?');
                $classes = $this->user?->is_athlete
                    ? 'bg-blue-100 text-blue-700'
                    : 'bg-emerald-100 text-emerald-700';

                return sprintf(
                    '<span class="inline-flex items-center justify-center min-w-[1.75rem] rounded-full px-2 py-1 text-xs font-semibold %s">%s</span>',
                    $classes,
                    e($label)
                );
            })->asHtml()->onlyOnIndex()->showOnIndex(! is_null($resourceId)),

            BelongsTo::make(__('User'), 'user', 'App\Nova\User')
                ->rules('required'),

// 🏆 Sport Name (Sortable & Filterable)
            Text::make(__('Sport'), function () {
                return optional($this->workoutSession->workout->activityType)->name ?? __('Unknown Sport');
            })
                ->sortable()
                ->filterable()
                ->showOnIndex(is_null($resourceId)),


            // Check-in status message (only shown outside check-in window)
            Text::make(__('Check-in Status'), function() {
                return $this->getCheckInStatusMessage();
            })
                ->asHtml()
                ->onlyOnIndex()
                ->showOnIndex(!is_null($resourceId) && !$this->isWithinCheckInWindow()),

            // Check In button
            Button::make($this->checked_in_at ? __('Cancel Check In') : __('Check In'))
                ->event(CheckInUser::class)
                ->style($this->checked_in_at ? 'warning' : 'success')
                ->loadingText($this->checked_in_at ? __('Cancelling...') : __('Checking In...'))
                ->successText($this->checked_in_at ? __('Check In Cancelled') : __('Checked In'))
                ->errorText(__('Error updating check-in status'))
                ->showOnIndex(!is_null($resourceId) && $attendanceContextActive),

            // Check Out button
            Button::make(__('Check Out'))
                ->event(CheckOutUser::class)
                ->style('danger')
                ->loadingText(__('Checking Out...'))
                ->successText(__('Checked Out'))
                ->errorText(__('Error updating check-out status'))
                ->visible(!is_null($this->checked_in_at) && is_null($this->checked_out_at))
                ->showOnIndex(!is_null($resourceId) && $attendanceContextActive),

// 📍 Location Name (Sortable & Filterable)
            Text::make(__('Location'), function () {
                return optional($this->workoutSession->location)->name ?? __('Unknown Location');
            })
                ->sortable()
                ->filterable()
                ->showOnIndex(is_null($resourceId)),

// 🗓️ Session Date (Sortable & Filterable)
            DateTime::make(__('Session Date'), function () {
                return optional($this->workoutSession)->session_date;
            })
                ->displayUsing(function ($value) {
                    return optional($value)->format('D, M jS Y'); // Example: Tue, Mar 14th 2025
                })
                ->sortable()
                ->filterable()
                ->showOnIndex(is_null($resourceId)),

// ⏰ Start Time (Sortable & Filterable)
            Text::make(__('Start Time'), function () {
                return optional($this->workoutSession)->start_time
                    ? $this->workoutSession->start_time->format('H:i')
                    : __('No Start Time');
            })
                ->sortable()
                ->filterable()
                ->showOnIndex(is_null($resourceId)),

// ⏳ End Time (Sortable & Filterable)
            Text::make(__('End Time'), function () {
                return optional($this->workoutSession)->end_time
                    ? $this->workoutSession->end_time->format('H:i')
                    : __('No End Time');
            })
                ->sortable()
                ->filterable()
                ->showOnIndex(is_null($resourceId)),

            Text::make(__('Athlete'), function () use ($request) {
                if ($this->user?->is_athlete) {
                    return sprintf(
                        '<span class="inline-flex items-center rounded px-2 py-1 text-xs font-semibold bg-slate-100 text-slate-700">%s</span>',
                        e(__('Athlete'))
                    );
                }

                if ($this->athleteUser) {
                    return sprintf(
                        '<a href="%s"><b>%s</b></a>',
                        e(static::searchUrlFor($request, $this->athleteUser->name)),
                        e($this->athleteUser->name)
                    );
                }

                return __('Unassigned');
            })->asHtml()->sortable()->showOnIndex(!is_null($resourceId)),

            Text::make(__('Guides'), function () use ($request) {
                if ($this->user->is_athlete) {
                    $guides = static::sessionGuidesForAthlete(
                        (int) $this->workout_session_id,
                        (int) $this->user_id
                    );

                    if ($guides->isEmpty()) {
                        return '<i class="danger">'.__('No Guides').'</i>';
                    }

                    return $guides->map(function ($guide) use ($request) {
                        return sprintf(
                            '<div class="flex justify-between items-center">
                    <a href="%s">%s</a>
                </div>',
                            e(static::searchUrlFor($request, $guide->name)),
                            e($guide->name)
                        );
                    })->implode('');
                }

                if (! $this->athleteUser) {
                    return '<i class="danger">'.__('No Athlete Assigned').'</i>';
                }

                $guides = static::sessionGuidesForAthlete(
                    (int) $this->workout_session_id,
                    (int) $this->athlete_id,
                    (int) $this->user_id
                );

                if ($guides->isEmpty()) {
                    return '<i class="danger">'.__('No Additional Guides').'</i>';
                }

                return $guides->map(function ($guide) use ($request) {
                    return sprintf(
                        '<div class="flex justify-between items-center">
                    <a href="%s">%s</a>
                </div>',
                        e(static::searchUrlFor($request, $guide->name)),
                        e($guide->name)
                    );
                })->implode('');
            })->asHtml()->showOnIndex(!is_null($resourceId)),

            Button::make(__('Check In Users'))
                ->link('/resources/workout-signups?resourceId=' . $this->workout_session_id, '_self')
                ->style('primary')
                ->visible($this->isWithinCheckInWindow())
                ->showOnIndex(is_null($resourceId)),

            BelongsTo::make(__('Status'), 'status', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    return $query->forModelType(\App\Models\WorkoutSignup::class);
                })
                ->filterable(),

            Select::make(__('Assigned Athlete'), 'athlete_id')
                ->options($assignedAthleteOptions)
                ->displayUsingLabels()
                ->nullable()
                ->hideFromIndex()
                ->help(__('Assign a guide row to an athlete who is signed up for the same session.')),

            Code::make(__('Preferences'))
                ->json()
                ->nullable(),

            HasOne::make(__('Specific Details'), 'specificDetails', WorkoutSpecificDetails::class),

            HasMany::make(__('Equipment Assignments'), 'equipmentAssignments', WorkoutEquipmentAssignment::class),

            /*Panel::make(__('Check-in Details'), [
                DateTime::make(__('Check-in Date'), 'checkin_date')
                    ->onlyOnDetail()
                    ->sortable(),

                DateTime::make(__('Check-out Date'), 'checkout_date')
                    ->onlyOnDetail()
                    ->sortable(),
            ]),*/
        ];
    }

    public function guides()
    {
        return $this->hasMany(WorkoutSignup::class, 'athlete_id', 'user_id')
            ->where('workout_session_id', $this->workout_session_id) // ✅ Ensure session matches
            ->whereHas('user', function ($query) {
                $query->where('is_athlete', false);
            });
    }

    public function cards(NovaRequest $request)
    {
        $session = static::resolveScopedSession($request);

        if (! $session) {
            return [];
        }

        return [
            (new SessionContextMetric())
                ->withMeta(['workout_session_id' => $session->id]),
            (new SessionWeatherMetric())
                ->withMeta(['workout_session_id' => $session->id]),
            (new SessionAttendanceMetric())
                ->withMeta(['workout_session_id' => $session->id])
                ->refreshWhenActionsRun(),
        ];
    }

    public function filters(NovaRequest $request)
    {
        return [
            new Filters\WorkoutSessionFilter,
            new Filters\WorkoutSignupDateRangeFilter,
        ];
    }
    public static function actionsInIndex(): bool
    {
        return true;
    }

    public function actions(NovaRequest $request)
    {
        $session = static::resolveScopedSession($request);

        if (! $session || ! static::isSessionWithinCheckInWindow($session) || ! static::hasActiveAttendanceContext($request, $session)) {
            return [];
        }

        return [
            (new RefreshSessionWeather())
                ->standalone()
                ->withoutConfirmation(),

            (new EndCheckInStaffSession())
                ->standalone()
                ->withoutConfirmation(),

            (new CheckInAction())
                ->onlyOnIndex()
                ->canSee(fn () => is_null($this->checked_in_at)),

            (new CheckOutAction())
                ->onlyOnIndex()
                ->canSee(fn () => !is_null($this->checked_in_at) && is_null($this->checked_out_at)),
        ];
    }

    public static function uriKey()
    {
        return 'workout-signups';
    }
    public static function getResourceId(NovaRequest $request): ?string
    {
        $resourceId = $request->query('resourceId') ?? $request->input('resourceId');

        if ($resourceId !== null && $resourceId !== '') {
            return (string) $resourceId;
        }

        $parts = parse_url($request->server('HTTP_REFERER'));
        if ($parts && isset($parts['query'])) {
            parse_str($parts['query'], $params);
            return $params['resourceId'] ?? null;
        }
        return null;
    }

    protected static function resolvedSessionId(NovaRequest $request): ?string
    {
        $resourceId = static::getResourceId($request);

        if ($resourceId !== null) {
            return $resourceId;
        }

        $viaResource = $request->query('viaResource') ?? $request->input('viaResource');
        $viaResourceId = $request->query('viaResourceId') ?? $request->input('viaResourceId');

        if ($viaResource === 'workout-sessions' && $viaResourceId) {
            return (string) $viaResourceId;
        }

        return null;
    }

    protected static function hasExplicitIndexScope(NovaRequest $request): bool
    {
        $search = trim((string) ($request->query('search') ?? $request->input('search') ?? ''));
        if ($search !== '') {
            return true;
        }

        $filters = $request->query('filters') ?? $request->input('filters') ?? [];

        if (is_string($filters) && $filters !== '') {
            $decoded = json_decode($filters, true);
            $filters = is_array($decoded) ? $decoded : [];
        }

        if (is_array($filters)) {
            foreach ($filters as $value) {
                if (is_string($value) && trim($value) !== '' && $value !== 'null') {
                    return true;
                }

                if (is_array($value) && $value !== []) {
                    return true;
                }
            }
        }

        return false;
    }

    protected static function resolveScopedSession(NovaRequest $request): ?WorkoutSessionModel
    {
        $resourceId = static::resolvedSessionId($request);

        if (! $resourceId || ! ctype_digit((string) $resourceId)) {
            return null;
        }

        return WorkoutSessionModel::query()
            ->with(['workout.activityType', 'location'])
            ->find((int) $resourceId);
    }

    protected static function isSessionWithinCheckInWindow(WorkoutSessionModel $session): bool
    {
        if (! $session->session_date || ! $session->start_time) {
            return false;
        }

        $sessionDateTime = $session->session_date->copy()->setTimeFrom($session->start_time);
        $hoursUntilSession = now()->diffInHours($sessionDateTime, false);

        return abs($hoursUntilSession) <= 12;
    }

    protected static function getSessionDetails(int $sessionId): string
    {
        $session = \App\Models\WorkoutSession::with(['workout', 'location'])
            ->find($sessionId);

        if (!$session || !$session->workout || !$session->location) {
            return __('Unknown Session');
        }

        $activityLocation = $session->location->name ?? __('Unknown Location');
        $formattedDate = optional($session->session_date)->format('D, M jS Y');
        $startTime = optional($session->start_time)->format('H:i');
        $endTime = optional($session->end_time)->format('H:i');
        $sportName = $session->workout->activityType->name ?? __('Unknown Sport');

        return __(':sport in :location - :date :start-:end', [
            'sport' => $sportName,
            'location' => $activityLocation,
            'date' => $formattedDate,
            'start' => $startTime,
            'end' => $endTime,
        ]);
    }

    public static function label()
    {
        try {
            $request = app(NovaRequest::class);
            $session = static::resolveScopedSession($request);

            if ($session) {
                $sessionDetails = static::getSessionDetails($session->id);
                return __('Check-in/out for :session', ['session' => $sessionDetails]);
            }
        } catch (\Exception $e) {
            \Log::error('Error getting WorkoutSignup label:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return __('Session Signups (select a session to manage attendance)');
    }

// Helper method to check if session is within check-in window
    protected function isWithinCheckInWindow(): bool {
        if (!$this->workoutSession) {
            return false;
        }

        $sessionDateTime = $this->workoutSession->session_date->setTimeFrom($this->workoutSession->start_time);
        $now = now();

        // Get time differences in hours
        $hoursUntilSession = $now->diffInHours($sessionDateTime, false);

        // Return true if within ±12 hours
        return abs($hoursUntilSession) <= 12;
    }

// Helper to get check-in status message
    protected function getCheckInStatusMessage(): string {
        if (!$this->workoutSession) {
            return __('No session data available');
        }

        $sessionDateTime = $this->workoutSession->session_date->setTimeFrom($this->workoutSession->start_time);
        $now = now();
        $hoursUntilSession = $now->diffInHours($sessionDateTime, false);

        $difference = 12;

        if ($hoursUntilSession > $difference) {
            return "Check-in opens {$difference} hours before session starts!";
        } else if ($hoursUntilSession < -$difference) {
            return "Check-in closes {$difference} hours after session ends!";
        }

        return '';
    }

    protected static function sessionRoster(int $sessionId): Collection
    {
        if (! array_key_exists($sessionId, static::$sessionRosterCache)) {
            static::$sessionRosterCache[$sessionId] = \App\Models\WorkoutSignup::query()
                ->where('workout_session_id', $sessionId)
                ->with([
                    'user:id,name,is_athlete,is_guide',
                    'athleteUser:id,name',
                ])
                ->get([
                    'id',
                    'workout_session_id',
                    'user_id',
                    'athlete_id',
                ]);
        }

        return static::$sessionRosterCache[$sessionId];
    }

    protected static function sessionGuidesForAthlete(
        int $sessionId,
        int $athleteId,
        ?int $excludeGuideUserId = null
    ): Collection {
        return static::sessionRoster($sessionId)
            ->filter(fn ($signup) => (int) $signup->athlete_id === $athleteId)
            ->filter(fn ($signup) => $signup->user?->is_guide)
            ->reject(fn ($signup) => $excludeGuideUserId !== null && (int) $signup->user_id === $excludeGuideUserId)
            ->map(fn ($signup) => $signup->user)
            ->filter()
            ->unique('id')
            ->values();
    }

    protected static function athleteOptionsForSession(int|string|null $sessionId): array
    {
        if (! $sessionId || ! ctype_digit((string) $sessionId)) {
            return [];
        }

        return static::sessionRoster((int) $sessionId)
            ->filter(fn ($signup) => $signup->user?->is_athlete)
            ->mapWithKeys(fn ($signup) => [
                $signup->user_id => $signup->user?->name ?? __('Unknown Athlete'),
            ])
            ->all();
    }

    protected static function hasActiveAttendanceContext(NovaRequest $request, ?WorkoutSessionModel $session = null): bool
    {
        $session ??= static::resolveScopedSession($request);

        if (! $session) {
            return false;
        }

        return app(CheckInSessionContext::class)->isActiveForSession($session);
    }

    protected static function searchUrlFor(NovaRequest $request, string $search): string
    {
        $sessionId = static::resolvedSessionId($request);

        return '/resources/workout-signups?'.http_build_query(array_filter([
            'resourceId' => $sessionId,
            'search' => $search,
        ]));
    }

}
