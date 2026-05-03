<?php

namespace App\Nova;

use App\Nova\Metrics\SignupDistribution;
use App\Nova\Metrics\SignupTrend;
use App\Nova\Metrics\TotalSignups;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\HasOne;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Database\Eloquent\Builder;
//use App\Nova\Filters\WorkoutSignupUserFilter;
//use App\Nova\Filters\WorkoutSignupStatusFilter;
//use App\Nova\Filters\WorkoutSessionFilter;
//use App\Nova\Filters\WorkoutSignupDateRangeFilter;
use Sietse85\NovaButton\Button;
use Carbon\Carbon;
use Laravel\Nova\Panel;

use App\Nova\Actions\CheckInAction;
use App\Nova\Actions\CheckOutAction;
use App\Events\CheckInUser;
use App\Events\CheckOutUser;

use Illuminate\Support\Facades\DB;
use App\Nova\Traits\DynamicPollingInterval;


class WorkoutSignup extends Resource
{
    public static $model = \App\Models\WorkoutSignup::class;

    public static $title = 'id';

    public static $search = [
        'id',
        'status_id',
        'preferences'
    ];

    public static $group = 'Workout Management';

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

        // Parse the request URL to get parameters
        $parts = parse_url($request->server('HTTP_REFERER'));
        if ($parts && isset($parts['query'])) {
            parse_str($parts['query'], $params);

            \Log::info('Parsed URL params', [
                'params' => $params,
                'has_session_id' => isset($params['resourceId'])
            ]);

            if (isset($params['resourceId'])) {
                $query->where('workout_session_id', $params['resourceId']);
            }
        }

        return $query;
    }

    public function fields(NovaRequest $request)
    {
        $resourceId = static::getResourceId($request);
        $withinWindow = $this->isWithinCheckInWindow();

        return [
            ID::make()->sortable(),

            BelongsTo::make('User', 'user', 'App\Nova\User')
                ->rules('required'),

// 🏆 Sport Name (Sortable & Filterable)
            Text::make('Sport', function () {
                return optional($this->workoutSession->workout->activityType)->name ?? 'Unknown Sport';
            })
                ->sortable()
                ->filterable()
                ->showOnIndex(is_null($resourceId)),


            // Check-in status message (only shown outside check-in window)
            Text::make('Check-in Status', function() {
                return $this->getCheckInStatusMessage();
            })
                ->asHtml()
                ->onlyOnIndex()
                ->showOnIndex(!is_null($resourceId) && !$this->isWithinCheckInWindow()),

            // Check In button
            Button::make($this->checked_in_at ? 'Cancel Check In' : 'Check In')
                ->event(CheckInUser::class)
                ->style($this->checked_in_at ? 'warning' : 'success')
                ->loadingText($this->checked_in_at ? 'Cancelling...' : 'Checking In...')
                ->successText($this->checked_in_at ? 'Check In Cancelled' : 'Checked In')
                ->errorText('Error updating check-in status')
                ->showOnIndex(!is_null($resourceId) && $this->isWithinCheckInWindow()),

            // Check Out button
            Button::make('Check Out')
                ->event(CheckOutUser::class)
                ->style('danger')
                ->loadingText('Checking Out...')
                ->successText('Checked Out')
                ->errorText('Error updating check-out status')
                ->visible(!is_null($this->checked_in_at) && is_null($this->checked_out_at))
                ->showOnIndex(!is_null($resourceId) && $this->isWithinCheckInWindow()),

// 📍 Location Name (Sortable & Filterable)
            Text::make('Location', function () {
                return optional($this->workoutSession->location)->name ?? 'Unknown Location';
            })
                ->sortable()
                ->filterable()
                ->showOnIndex(is_null($resourceId)),

// 🗓️ Session Date (Sortable & Filterable)
            DateTime::make('Session Date', function () {
                return optional($this->workoutSession)->session_date;
            })
                ->displayUsing(function ($value) {
                    return optional($value)->format('D, M jS Y'); // Example: Tue, Mar 14th 2025
                })
                ->sortable()
                ->filterable()
                ->showOnIndex(is_null($resourceId)),

// ⏰ Start Time (Sortable & Filterable)
            Text::make('Start Time', function () {
                return optional($this->workoutSession)->start_time
                    ? $this->workoutSession->start_time->format('H:i')
                    : 'No Start Time';
            })
                ->sortable()
                ->filterable()
                ->showOnIndex(is_null($resourceId)),

// ⏳ End Time (Sortable & Filterable)
            Text::make('End Time', function () {
                return optional($this->workoutSession)->end_time
                    ? $this->workoutSession->end_time->format('H:i')
                    : 'No End Time';
            })
                ->sortable()
                ->filterable()
                ->showOnIndex(is_null($resourceId)),

            Text::make('Athlete', function () {
                if ($this->user->is_athlete) {
                    return sprintf(
                        '<a href="#" onclick="return updateSearchBox(\'%s\')"><i>%s</i></a>',
                        e($this->user->name),
                        e($this->user->name)
                    );
                }

                if ($this->athlete_id) {
                    $athlete = DB::table('users')
                        ->where('id', $this->athlete_id)
                        ->first();

                    return $athlete
                        ? sprintf(
                            '<a href="#" onclick="return updateSearchBox(\'%s\')"><b>%s</b></a>',
                            e($athlete->name),
                            e($athlete->name)
                        )
                        : "Unassigned Guide";
                }

                return "Unassigned";
            })->asHtml()->sortable(),

            Text::make('Guides', function () {
                if ($this->user->is_athlete) {
                    // For athletes - show all guides for this signup
                    $guides = DB::table('workout_signups')
                        ->join('users', 'workout_signups.user_id', '=', 'users.id')
                        ->where('workout_signups.workout_session_id', $this->workout_session_id)
                        ->where('workout_signups.athlete_id', $this->user->id)
                        ->where('users.is_guide', true)
                        ->select('users.id', 'users.name')
                        ->get();

                    if ($guides->isEmpty()) {
                        return '<i class="danger">No Guides</i>';
                    }

                    return $guides->map(function ($guide) {
                        return sprintf(
                            '<div class="flex justify-between items-center">
                    <a href="#" onclick="return updateSearchBox(\'%s\')">%s</a>
                </div>',
                            e($guide->name),
                            e($guide->name)
                        );
                    })->implode('');
                } else {
                    // For guides - lookup the athlete first to get all guides for that athlete
                    $athleteId = DB::table('workout_signups')
                        ->where('workout_signups.workout_session_id', $this->workout_session_id)
                        ->where('workout_signups.user_id', $this->user->id)
                        ->value('athlete_id');

                    if (!$athleteId) {
                        return '<i class="danger">No Athlete Assigned</i>';
                    }

                    // Get all guides for this athlete except the current user
                    $guides = DB::table('workout_signups')
                        ->join('users', 'workout_signups.user_id', '=', 'users.id')
                        ->where('workout_signups.workout_session_id', $this->workout_session_id)
                        ->where('workout_signups.athlete_id', $athleteId)
                        ->where('users.is_guide', true)
                        ->where('users.id', '!=', $this->user->id)  // Exclude current guide
                        ->select('users.id', 'users.name')
                        ->get();

                    if ($guides->isEmpty()) {
                        return '<i class="danger">No Additional Guides</i>';
                    }

                    return $guides->map(function ($guide) {
                        return sprintf(
                            '<div class="flex justify-between items-center">
                    <a href="#" onclick="return updateSearchBox(\'%s\')">%s</a>
                </div>',
                            e($guide->name),
                            e($guide->name)
                        );
                    })->implode('');
                }
            })->asHtml(),

            Button::make('Check In Users')
                ->link('/resources/workout-signups?resourceId=' . $this->workout_session_id, '_self')
                ->style('primary')
                ->visible($this->isWithinCheckInWindow())
                ->showOnIndex(is_null($resourceId)),

            BelongsTo::make('Status', 'status', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    return $query->whereHas('module', function ($q) {
                        $q->where('model_type', 'App\Models\WorkoutSignup');
                    });
                })
                ->filterable(),

            Code::make('Preferences')
                ->json()
                ->nullable(),

            HasOne::make('Specific Details', 'specificDetails', WorkoutSpecificDetails::class),

            HasMany::make('Equipment Assignments', 'equipmentAssignments', WorkoutEquipmentAssignment::class),

            /*Panel::make('Check-in Details', [
                DateTime::make('Check-in Date', 'checkin_date')
                    ->onlyOnDetail()
                    ->sortable(),

                DateTime::make('Check-out Date', 'checkout_date')
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
        // Add some debugging
        $resourceId = static::getResourceId($request);
        \Log::info('WorkoutSignup Cards', [
            'resourceId' => $resourceId,
            'request' => $request->all()
        ]);

        if (!$resourceId) {
            return [];
        }

        return [
            (new Metrics\SessionAttendanceMetric())
                ->withMeta(['workout_session_id' => $resourceId])
                ->refreshWhenActionsRun(),
        ];
    }

    public function filters(NovaRequest $request)
    {
        return [
//            new WorkoutSignupUserFilter,
//            new WorkoutSignupStatusFilter,
            new Filters\WorkoutSessionFilter,
//            new WorkoutSignupDateRangeFilter,
        ];
    }
    public static function actionsInIndex(): bool
    {
        return true;
    }

    public function actions(NovaRequest $request)
    {
        return [
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
        $parts = parse_url($request->server('HTTP_REFERER'));
        if ($parts && isset($parts['query'])) {
            parse_str($parts['query'], $params);
            return $params['resourceId'] ?? null;
        }
        return null;
    }

    protected static function getSessionDetails(int $sessionId): string
    {
        $session = \App\Models\WorkoutSession::with(['workout', 'location'])
            ->find($sessionId);

        if (!$session || !$session->workout || !$session->location) {
            return 'Unknown Session';
        }

        $activityLocation = $session->location->name ?? 'Unknown Location';
        $formattedDate = optional($session->session_date)->format('D, M jS Y');
        $startTime = optional($session->start_time)->format('H:i');
        $endTime = optional($session->end_time)->format('H:i');
        $sportName = $session->workout->activityType->name ?? 'Unknown Sport';

        return "<b>{$sportName}</b> in {$activityLocation} - <b>{$formattedDate}</b> {$startTime}-{$endTime}";
    }

    public static function label()
    {
        try {
            $request = app(NovaRequest::class);
            $resourceId = static::getResourceId($request);

            if ($resourceId) {
                $sessionDetails = static::getSessionDetails((int)$resourceId);
                return "Check-in/out for {$sessionDetails}";
            }
        } catch (\Exception $e) {
            \Log::error('Error getting WorkoutSignup label:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return 'Session Signups';
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
            return 'No session data available';
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

}

