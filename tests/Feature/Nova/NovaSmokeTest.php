<?php

namespace Tests\Feature\Nova;

use App\Models\MeetingPoint;
use App\Models\SystemCategory;
use App\Models\SystemLocation;
use App\Models\User;
use App\Nova\Filters\WorkoutSessionFilter;
use App\Nova\WorkoutSignup as WorkoutSignupResource;
use App\Nova\Dashboards\Main;
use App\Nova\Metrics\MyUpcomingSessions;
use App\Nova\Metrics\ScopedSessionWindowValue;
use App\Models\Workout;
use App\Models\WorkoutSession;
use App\Models\WorkoutSignup;
use App\Support\Attendance\CheckInSessionContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Nova\Nova;
use Laravel\Nova\Http\Requests\NovaRequest;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class NovaSmokeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', database_path('database.sqlite'));
        DB::purge('sqlite');
        DB::reconnect('sqlite');
    }

    public function test_public_pages_load_without_server_errors(): void
    {
        $failures = [];

        $publicPages = [
            '/' => [302],
            '/login' => [200],
            '/workouts' => [302],
            '/workouts/create' => [302],
            '/meeting-points' => [302],
            '/meeting-points/create' => [302],
            '/workout-sessions' => [302],
            '/workout-sessions/create' => [302],
            '/workout-signups' => [302],
            '/workout-signups/create' => [302],
        ];

        foreach ($publicPages as $uri => $allowedStatuses) {
            $response = $this->get($uri);

            if (! in_array($response->status(), $allowedStatuses, true)) {
                $failures[] = [
                    'uri' => $uri,
                    'status' => $response->status(),
                ];
            }
        }

        $detailPages = array_filter([
            Workout::query()->value('id') ? '/workouts/'.Workout::query()->value('id') : null,
            Workout::query()->value('id') ? '/workouts/'.Workout::query()->value('id').'/edit' : null,
            WorkoutSession::query()->value('id') ? '/workout-sessions/'.WorkoutSession::query()->value('id') : null,
            WorkoutSession::query()->value('id') ? '/workout-sessions/'.WorkoutSession::query()->value('id').'/edit' : null,
            WorkoutSignup::query()->value('id') ? '/workout-signups/'.WorkoutSignup::query()->value('id') : null,
            WorkoutSignup::query()->value('id') ? '/workout-signups/'.WorkoutSignup::query()->value('id').'/edit' : null,
            MeetingPoint::query()->value('id') ? '/meeting-points/'.MeetingPoint::query()->value('id') : null,
            MeetingPoint::query()->value('id') ? '/meeting-points/'.MeetingPoint::query()->value('id').'/edit' : null,
            SystemLocation::query()->value('id') ? '/weather/'.SystemLocation::query()->value('id') : null,
        ]);

        $detailAllowedStatuses = [
            '/weather/' => [200],
        ];

        foreach ($detailPages as $uri) {
            $response = $this->get($uri);
            $allowedStatuses = [302];

            foreach ($detailAllowedStatuses as $prefix => $statuses) {
                if (str_starts_with($uri, $prefix)) {
                    $allowedStatuses = $statuses;
                    break;
                }
            }

            if (! in_array($response->status(), $allowedStatuses, true)) {
                $failures[] = [
                    'uri' => $uri,
                    'status' => $response->status(),
                ];
            }
        }

        $this->assertSame([], $failures, json_encode($failures, JSON_PRETTY_PRINT));
    }

    public function test_seeded_sys_admin_can_log_in_via_web_guard(): void
    {
        $response = $this->post('/login', [
            'email' => 'THISISG@GMAIL.COM',
            'password' => 'rcp@UHE-nmq_kmw0qzk',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs(
            User::query()->where('email', 'thisisg@gmail.com')->firstOrFail()
        );
    }

    public function test_non_privileged_user_cannot_log_in_via_web_guard(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'sgibbs8885@gmail.com',
            'password' => 'default_pa55word!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_soft_deleted_privileged_user_cannot_log_in_via_web_guard(): void
    {
        $email = 'deleted-admin-'.uniqid().'@example.com';

        $user = User::query()->create([
            'name' => 'Soft Deleted Admin',
            'email' => $email,
            'first_name' => 'Soft',
            'last_name' => 'Deleted',
            'password' => Hash::make('TempPass123!'),
            'email_verified_at' => now(),
        ]);

        $user->forceFill(['is_sys_admin' => true])->save();
        $user->delete();

        $response = $this->from('/login')->post('/login', [
            'email' => $email,
            'password' => 'TempPass123!',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_successful_login_rehashes_legacy_password_hashes(): void
    {
        config()->set('hashing.bcrypt.rounds', 12);

        $email = 'legacy-admin-'.uniqid().'@example.com';
        $legacyPassword = 'LegacyPass123!';
        $legacyHash = password_hash($legacyPassword, PASSWORD_BCRYPT, ['cost' => 4]);

        $userId = DB::table('users')->insertGetId([
            'name' => 'Legacy Hash Admin',
            'email' => $email,
            'first_name' => 'Legacy',
            'last_name' => 'Admin',
            'password' => $legacyHash,
            'is_admin' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::query()->findOrFail($userId);

        $response = $this->post('/login', [
            'email' => $email,
            'password' => $legacyPassword,
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs($user);

        $freshUser = $user->fresh();

        $this->assertNotSame($legacyHash, $freshUser->password);
        $this->assertTrue(Hash::check($legacyPassword, $freshUser->password));
        $this->assertFalse(Hash::needsRehash($freshUser->password));
    }

    public function test_nova_pages_load_for_seeded_sys_admin(): void
    {
        $admin = User::query()->where('email', 'thisisg@gmail.com')->firstOrFail();
        $this->actingAs($admin);

        $failures = [];

        foreach ($this->novaUris() as $uri) {
            $response = $this->get($uri);

            if ($response->status() >= 500) {
                $failures[] = [
                    'uri' => $uri,
                    'status' => $response->status(),
                ];
            }
        }

        $this->assertSame([], $failures, json_encode($failures, JSON_PRETTY_PRINT));
    }

    public function test_authenticated_bridge_pages_resolve_to_working_nova_pages(): void
    {
        $admin = User::query()->where('email', 'thisisg@gmail.com')->firstOrFail();
        $this->actingAs($admin);

        $pages = [
            '/workouts',
            '/workouts/create',
            '/workout-sessions',
            '/workout-sessions/create',
            '/workout-signups',
            '/workout-signups/create',
            '/meeting-points',
            '/meeting-points/create',
            '/home',
        ];

        if ($workoutId = Workout::query()->value('id')) {
            $pages[] = '/workouts/'.$workoutId;
            $pages[] = '/workouts/'.$workoutId.'/edit';
        }

        if ($sessionId = WorkoutSession::query()->value('id')) {
            $pages[] = '/workout-sessions/'.$sessionId;
            $pages[] = '/workout-sessions/'.$sessionId.'/edit';
        }

        if ($signupId = WorkoutSignup::query()->value('id')) {
            $pages[] = '/workout-signups/'.$signupId;
            $pages[] = '/workout-signups/'.$signupId.'/edit';
        }

        if ($meetingPointId = MeetingPoint::query()->value('id')) {
            $pages[] = '/meeting-points/'.$meetingPointId;
            $pages[] = '/meeting-points/'.$meetingPointId.'/edit';
        }

        $failures = [];

        foreach ($pages as $uri) {
            $response = $this->followingRedirects()->get($uri);

            if ($response->status() >= 400) {
                $failures[] = [
                    'uri' => $uri,
                    'status' => $response->status(),
                ];
            }
        }

        $this->assertSame([], $failures, json_encode($failures, JSON_PRETTY_PRINT));
    }

    public function test_non_privileged_authenticated_user_cannot_access_nova_dashboard(): void
    {
        $user = User::query()->where('email', 'sgibbs8885@gmail.com')->firstOrFail();
        $this->actingAs($user);

        $this->get('/dashboards/main')->assertForbidden();
    }

    public function test_main_dashboard_cards_switch_between_personal_and_operational_views(): void
    {
        $athlete = User::query()
            ->whereNull('deleted_at')
            ->where('is_athlete', true)
            ->where('is_guide', false)
            ->where('is_sys_admin', false)
            ->where('is_admin', false)
            ->where('is_team_leader', false)
            ->firstOrFail();

        $this->actingAs($athlete);
        $athleteCardClasses = collect((new Main())->cards())->map(fn ($card) => $card::class)->all();
        $this->assertContains(MyUpcomingSessions::class, $athleteCardClasses);
        $this->assertNotContains(ScopedSessionWindowValue::class, $athleteCardClasses);

        $staff = User::query()
            ->whereNull('deleted_at')
            ->where(function ($query) {
                $query->where('is_sys_admin', true)
                    ->orWhere('is_admin', true)
                    ->orWhere('is_team_leader', true);
            })
            ->firstOrFail();

        $this->actingAs($staff);
        $staffCardClasses = collect((new Main())->cards())->map(fn ($card) => $card::class)->all();
        $this->assertContains(ScopedSessionWindowValue::class, $staffCardClasses);
    }

    public function test_workout_signup_index_requires_explicit_scope(): void
    {
        $request = NovaRequest::create('/nova-api/workout-signups', 'GET');

        $query = WorkoutSignupResource::indexQuery($request, WorkoutSignup::query());

        $this->assertSame(0, $query->count());
    }

    public function test_workout_signup_index_scopes_to_selected_session(): void
    {
        $session = WorkoutSession::query()->has('signups')->firstOrFail();
        $request = NovaRequest::create('/nova-api/workout-signups', 'GET', [
            'resourceId' => $session->id,
        ]);

        $query = WorkoutSignupResource::indexQuery($request, WorkoutSignup::query());

        $this->assertSame($session->signups()->count(), $query->count());
    }

    public function test_workout_signup_index_scopes_from_workout_session_relation_context(): void
    {
        $session = WorkoutSession::query()->has('signups')->firstOrFail();
        $request = NovaRequest::create('/nova-api/workout-signups', 'GET', [
            'viaResource' => 'workout-sessions',
            'viaResourceId' => $session->id,
            'viaRelationship' => 'signups',
        ]);

        $query = WorkoutSignupResource::indexQuery($request, WorkoutSignup::query());

        $this->assertSame($session->signups()->count(), $query->count());
    }

    public function test_workout_signup_scoped_index_does_not_eager_load_entire_session_roster_per_row(): void
    {
        $session = WorkoutSession::query()->has('signups')->firstOrFail();
        $request = NovaRequest::create('/nova-api/workout-signups', 'GET', [
            'resourceId' => $session->id,
        ]);

        $query = WorkoutSignupResource::indexQuery($request, WorkoutSignup::query());
        $eagerLoads = $query->getEagerLoads();

        $this->assertArrayNotHasKey('workoutSession.signups.user', $eagerLoads);
    }

    public function test_workout_signup_attendance_actions_require_selected_session_context(): void
    {
        $signup = WorkoutSignup::query()->with('workoutSession')->firstOrFail();
        $resource = new WorkoutSignupResource($signup);
        $windowStart = now()->copy()->addHour()->startOfMinute();

        $signup->workoutSession->forceFill([
            'session_date' => $windowStart->toDateString(),
            'start_time' => $windowStart->format('H:i:s'),
            'end_time' => $windowStart->copy()->addHours(2)->format('H:i:s'),
        ])->save();

        $unscopedRequest = NovaRequest::create('/resources/workout-signups', 'GET');
        $scopedRequest = NovaRequest::create('/resources/workout-signups', 'GET', [
            'resourceId' => $signup->workout_session_id,
        ]);
        $sessionStore = app('session.store');
        $sessionStore->start();
        $unscopedRequest->setLaravelSession($sessionStore);
        $scopedRequest->setLaravelSession($sessionStore);
        app(CheckInSessionContext::class)->activate(User::query()->where('email', 'thisisg@gmail.com')->firstOrFail(), $signup->workoutSession);

        $this->assertCount(0, $resource->actions($unscopedRequest));
        $this->assertGreaterThanOrEqual(2, count($resource->actions($scopedRequest)));
    }

    public function test_stale_workout_session_cannot_activate_check_in_context(): void
    {
        $admin = User::query()->where('email', 'thisisg@gmail.com')->firstOrFail();
        $session = WorkoutSession::query()->firstOrFail();

        $session->forceFill([
            'session_date' => now()->subDays(3)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
        ])->save();

        $this->actingAs($admin)
            ->get('/attendance/sessions/'.$session->id.'/activate')
            ->assertForbidden();
    }

    public function test_check_in_context_activation_persists_across_follow_up_requests(): void
    {
        $admin = User::query()->where('email', 'thisisg@gmail.com')->firstOrFail();
        $session = WorkoutSession::query()->has('signups')->firstOrFail();
        $windowStart = now()->copy()->addHour();

        $session->forceFill([
            'session_date' => $windowStart->toDateString(),
            'start_time' => $windowStart->format('H:i:s'),
            'end_time' => $windowStart->copy()->addHours(2)->format('H:i:s'),
        ])->save();

        $this->actingAs($admin)
            ->get('/attendance/sessions/'.$session->id.'/activate')
            ->assertRedirect('/resources/workout-signups?resourceId='.$session->id);

        $this->assertSame($session->id, session('attendance.active_check_in_session.session_id'));

        $this->actingAs($admin)
            ->get('/attendance/sessions/weather/refresh')
            ->assertRedirect();
    }

    public function test_workout_signup_scoped_label_is_plain_text(): void
    {
        $session = WorkoutSession::query()->with(['workout.activityType', 'location'])->firstOrFail();
        $request = NovaRequest::create('/resources/workout-signups', 'GET', [
            'resourceId' => $session->id,
        ]);

        app()->instance(NovaRequest::class, $request);

        $label = WorkoutSignupResource::label();

        $this->assertStringNotContainsString('<b>', $label);
        $this->assertStringContainsString((string) ($session->location->name ?? ''), $label);
    }

    public function test_workout_signup_relationships_include_soft_deleted_users(): void
    {
        $signup = WorkoutSignup::query()->with(['user', 'athleteUser'])->firstOrFail();
        $user = $signup->user;

        $this->assertNotNull($user);

        $user->delete();
        $signup->refresh()->load(['user', 'athleteUser']);

        $this->assertNotNull($signup->user);
        $this->assertSame($user->id, $signup->user->id);
    }

    public function test_workout_session_filter_options_tolerate_missing_activity_type_relation(): void
    {
        $session = WorkoutSession::query()->has('signups')->with('workout.activityType')->firstOrFail();
        $currentModuleId = $session->workout->activityType?->system_module_id ?? 0;

        $fallbackCategory = SystemCategory::query()
            ->where('system_module_id', '!=', $currentModuleId)
            ->value('id');

        $this->assertNotNull($fallbackCategory);

        $session->workout->update([
            'activity_type_id' => $fallbackCategory,
        ]);
        $session->update([
            'session_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
        ]);

        $filter = new WorkoutSessionFilter();
        $options = $filter->options(NovaRequest::create('/nova-api/workout-signups/filters', 'GET'));

        $this->assertIsArray($options);
        $this->assertNotEmpty($options);
    }

    public function test_nova_api_endpoints_load_for_seeded_sys_admin(): void
    {
        $admin = User::query()->where('email', 'thisisg@gmail.com')->firstOrFail();
        $this->actingAs($admin);

        $failures = [];

        foreach (Nova::resourceCollection()->all() as $resourceClass) {
            $uriKey = $resourceClass::uriKey();

            foreach ([
                $this->novaBasePath().'/nova-api/'.$uriKey,
                $this->novaBasePath().'/nova-api/'.$uriKey.'/creation-fields',
                $this->novaBasePath().'/nova-api/'.$uriKey.'/filters',
            ] as $uri) {
                $response = $this->getJson($uri);

                if ($response->status() >= 500) {
                    $failures[] = [
                        'uri' => $uri,
                        'status' => $response->status(),
                    ];
                }
            }

            $modelClass = $resourceClass::$model ?? null;

            if (! is_string($modelClass) || ! class_exists($modelClass)) {
                continue;
            }

            $model = new $modelClass();
            $key = $model->newQuery()->value($model->getKeyName());

            if ($key === null) {
                continue;
            }

            foreach ([
                $this->novaBasePath().'/nova-api/'.$uriKey.'/'.$key,
                $this->novaBasePath().'/nova-api/'.$uriKey.'/'.$key.'/update-fields',
            ] as $uri) {
                $response = $this->getJson($uri);

                if ($response->status() >= 500) {
                    $failures[] = [
                        'uri' => $uri,
                        'status' => $response->status(),
                    ];
                }
            }
        }

        $this->assertSame([], $failures, json_encode($failures, JSON_PRETTY_PRINT));
    }

    /**
     * @return array<int, string>
     */
    private function novaUris(): array
    {
        $uris = [
            '/',
            $this->novaBasePath().'/dashboards/main',
        ];

        foreach (Nova::resourceCollection()->all() as $resourceClass) {
            $uriKey = $resourceClass::uriKey();
            $uris[] = $this->novaBasePath().'/resources/'.$uriKey;
            $uris[] = $this->novaBasePath().'/resources/'.$uriKey.'/new';

            $modelClass = $resourceClass::$model ?? null;

            if (! is_string($modelClass) || ! class_exists($modelClass)) {
                continue;
            }

            $model = new $modelClass();
            $key = $model->newQuery()->value($model->getKeyName());

            if ($key === null) {
                continue;
            }

            $uris[] = $this->novaBasePath().'/resources/'.$uriKey.'/'.$key;
            $uris[] = $this->novaBasePath().'/resources/'.$uriKey.'/'.$key.'/edit';
        }

        return array_values(array_unique($uris));
    }

    private function novaBasePath(): string
    {
        $novaBasePath = trim(Nova::path(), '/');

        return $novaBasePath === '' ? '' : '/'.$novaBasePath;
    }
}
