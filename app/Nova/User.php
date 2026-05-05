<?php

namespace App\Nova;

use App\Models\WorkoutSignup as WorkoutSignupModel;
use App\Support\Attendance\CheckInSessionContext;
use App\Nova\Filters\UserChapterAccessFilter;
use App\Nova\Filters\UserCreatedPresetFilter;
use App\Nova\Filters\UserLocationAccessFilter;
use App\Nova\Filters\UserRoleFilter;
use App\Nova\Filters\UserSubscriptionFilter;
use App\Nova\Filters\UserVerificationFilter;
use App\Nova\Lenses\AdminUsers;
use App\Nova\Lenses\AthleteUsers;
use App\Nova\Lenses\GuideUsers;
use App\Nova\Lenses\PrivilegedUsers;
use App\Nova\Lenses\RecentUsers;
use App\Nova\Lenses\SysAdminUsers;
use App\Nova\Lenses\TeamLeadUsers;
use App\Nova\Lenses\UnverifiedUsers;
use App\Nova\Lenses\UsersWithActiveLocationAccess;
use App\Nova\Metrics\PrivilegedUsersMetric;
use App\Nova\Metrics\SubscribedUsers;
use App\Nova\Metrics\TotalAthletes;
use App\Nova\Metrics\TotalGuides;
use App\Nova\Metrics\TotalTeamLeaders;
use App\Nova\Metrics\TotalUsers;
use App\Nova\Metrics\UserGrowth;
use App\Nova\Metrics\UserSubscriptionDistribution;
use App\Nova\Metrics\UserTypeDistribution;
use App\Nova\Metrics\UserVerificationDistribution;
use App\Nova\Metrics\UsersWithActiveLocationAccessMetric;
use App\Nova\Metrics\VerifiedUsers;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Laravel\Nova\Auth\PasswordValidationRules;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Sietse85\NovaButton\Button;

class User extends Resource
{
    use PasswordValidationRules;
    use SoftDeletes;

    /**
     * @var class-string<\App\Models\User>
     */
    public static $model = \App\Models\User::class;

    public static $title = 'name';

    public static $search = [
        'name',
        'preferred_name',
        'first_name',
        'middle_name',
        'last_name',
        'email',
    ];

    public static $perPageOptions = [25, 50, 100, 250];

    public static function label()
    {
        return __('Users');
    }

    public static function singularLabel()
    {
        return __('User');
    }

    public static function indexQuery(NovaRequest $request, BuilderContract $query): BuilderContract
    {
        $query = parent::indexQuery($request, $query)
            ->withCount([
                'languageProficiency',
                'userCertification',
                'activeLocationAccessRecords as active_location_access_count',
                'workoutSignups',
            ]);

        if (! $request->filled('orderBy')) {
            $query->orderByDesc('created_at')
                ->orderByDesc('id');
        }

        return $query;
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Avatar::make(__('Photo'))
                ->thumbnail(function () {
                    return $this->picture ?: $this->getGravatarUrl($this->email);
                })
                ->maxWidth(50)
                ->hideFromDetail()
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            Image::make(__('Photo'))
                ->preview(function () {
                    return $this->picture ?: $this->getGravatarUrl($this->email);
                })
                ->disableDownload()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            Text::make(__('Name'))
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make(__('Preferred Name'))
                ->hideFromIndex()
                ->rules('nullable', 'max:255'),

            Text::make(__('First Name'))
                ->sortable()
                ->hideFromIndex()
                ->rules('required', 'max:255'),

            Text::make(__('Middle Name'))
                ->sortable()
                ->hideFromIndex()
                ->rules('nullable', 'max:255'),

            Text::make(__('Last Name'))
                ->sortable()
                ->hideFromIndex()
                ->rules('required', 'max:255'),

            Text::make(__('Email'))
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Button::make(__('Check In'))
                ->link($this->attendanceCheckInLink(), '_self')
                ->style('success')
                ->showOnIndex(static::shouldShowAttendanceButton($request, $this->resource) && ! static::isCheckedInToActiveSession($this->resource))
                ->showOnDetail(static::shouldShowAttendanceButton($request, $this->resource) && ! static::isCheckedInToActiveSession($this->resource)),

            Button::make(__('Check Out'))
                ->link($this->attendanceCheckOutLink(), '_self')
                ->style('danger')
                ->showOnIndex(static::shouldShowAttendanceButton($request, $this->resource) && static::isCheckedInToActiveSession($this->resource))
                ->showOnDetail(static::shouldShowAttendanceButton($request, $this->resource) && static::isCheckedInToActiveSession($this->resource)),

            Text::make(__('Phone'))
                ->hideFromIndex()
                ->rules('nullable', 'max:22'),

            Boolean::make(__('Verified'), fn () => $this->email_verified_at !== null)
                ->exceptOnForms(),

            Boolean::make(__('Subscribed'), 'is_subscribed')
                ->sortable(),

            Boolean::make(__('Sys Admin'), 'is_sys_admin')
                ->sortable(),

            Boolean::make(__('Admin'), 'is_admin')
                ->sortable(),

            Boolean::make(__('Team Lead'), 'is_team_leader')
                ->sortable(),

            Boolean::make(__('Athlete'), 'is_athlete')
                ->sortable(),

            Boolean::make(__('Guide'), 'is_guide')
                ->sortable(),

            Number::make(__('Active Location Access'), 'active_location_access_count')
                ->exceptOnForms()
                ->sortable(),

            Number::make(__('Languages'), 'language_proficiency_count')
                ->exceptOnForms()
                ->sortable(),

            Number::make(__('Certifications'), 'user_certification_count')
                ->exceptOnForms()
                ->sortable(),

            Number::make(__('Signups'), 'workout_signups_count')
                ->exceptOnForms()
                ->sortable(),

            DateTime::make(__('Created At'), 'created_at')
                ->exceptOnForms()
                ->sortable(),

            DateTime::make(__('Updated At'), 'updated_at')
                ->exceptOnForms()
                ->hideFromIndex()
                ->sortable(),

            Password::make(__('Password'))
                ->onlyOnForms()
                ->creationRules($this->passwordRules())
                ->updateRules($this->optionalPasswordRules()),

            HasMany::make(__('Location Access Records'), 'locationAccessRecords', SystemLocationAccess::class),

            HasMany::make(__('Workout Signups'), 'workoutSignups', WorkoutSignup::class),

            HasMany::make(__('Certifications'), 'userCertification', UserCertification::class),

            HasMany::make(__('Language Proficiencies'), 'languageProficiency', LanguageProficiency::class),
        ];
    }

    public static function uriKey()
    {
        return 'users';
    }

    protected function getGravatarUrl($email)
    {
        $hash = md5(strtolower(trim($email)));

        return "https://www.gravatar.com/avatar/{$hash}?s=250";
    }

    protected function attendanceCheckInLink(): string
    {
        return $this->resource?->getKey()
            ? route('attendance.users.check-in', ['user' => $this->resource->getKey()])
            : '#';
    }

    protected function attendanceCheckOutLink(): string
    {
        return $this->resource?->getKey()
            ? route('attendance.users.check-out', ['user' => $this->resource->getKey()])
            : '#';
    }

    public function cards(NovaRequest $request): array
    {
        $cards = [
            new TotalUsers(),
            new VerifiedUsers(),
            new SubscribedUsers(),
            new UsersWithActiveLocationAccessMetric(),
            new TotalAthletes(),
            new TotalGuides(),
            new TotalTeamLeaders(),
            new PrivilegedUsersMetric(),
            new UserTypeDistribution(),
            new UserVerificationDistribution(),
            new UserSubscriptionDistribution(),
            new UserGrowth(),
        ];

        if (app(CheckInSessionContext::class)->currentSession()) {
            array_unshift(
                $cards,
                new \App\Nova\Metrics\SessionWeatherMetric(),
                new \App\Nova\Metrics\SessionContextMetric()
            );
        }

        return $cards;
    }

    public function filters(NovaRequest $request): array
    {
        return [
            new UserRoleFilter(),
            new UserVerificationFilter(),
            new UserSubscriptionFilter(),
            new UserLocationAccessFilter(),
            new UserChapterAccessFilter(),
            new UserCreatedPresetFilter(),
        ];
    }

    public function lenses(NovaRequest $request): array
    {
        return [
            new RecentUsers(),
            new PrivilegedUsers(),
            new UsersWithActiveLocationAccess(),
            new UnverifiedUsers(),
            new SysAdminUsers(),
            new AdminUsers(),
            new TeamLeadUsers(),
            new GuideUsers(),
            new AthleteUsers(),
        ];
    }

    public function actions(NovaRequest $request): array
    {
        return [];
    }

    protected static function shouldShowAttendanceButton(NovaRequest $request, \App\Models\User $user): bool
    {
        $context = app(CheckInSessionContext::class);
        $session = $context->currentSession();

        if (! $session) {
            return false;
        }

        $viewer = $request->user();

        return $viewer && $context->canManageSession($viewer, $session);
    }

    protected static function isCheckedInToActiveSession(\App\Models\User $user): bool
    {
        $sessionId = app(CheckInSessionContext::class)->currentSessionId();

        if (! $sessionId) {
            return false;
        }

        return WorkoutSignupModel::query()
            ->where('workout_session_id', $sessionId)
            ->where('user_id', $user->id)
            ->whereNotNull('checked_in_at')
            ->whereNull('checked_out_at')
            ->exists();
    }
}
