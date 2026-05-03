<?php

namespace Tests\Feature;

use App\Models\MeetingPoint;
use App\Models\SystemLocation;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSession;
use App\Models\WorkoutSignup;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Nova;
use Tests\TestCase;

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
            'email' => 'thisisg@gmail.com',
            'password' => 'rcp@UHE-nmq_kmw0qzk',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticatedAs(
            User::query()->where('email', 'thisisg@gmail.com')->firstOrFail()
        );
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
