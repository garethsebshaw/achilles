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

    public function test_nova_api_endpoints_load_for_seeded_sys_admin(): void
    {
        $admin = User::query()->where('email', 'thisisg@gmail.com')->firstOrFail();
        $this->actingAs($admin);

        $failures = [];

        foreach (Nova::resourceCollection()->all() as $resourceClass) {
            $uriKey = $resourceClass::uriKey();

            foreach ([
                '/achillesworkouts.com/nova-api/'.$uriKey,
                '/achillesworkouts.com/nova-api/'.$uriKey.'/creation-fields',
                '/achillesworkouts.com/nova-api/'.$uriKey.'/filters',
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
                '/achillesworkouts.com/nova-api/'.$uriKey.'/'.$key,
                '/achillesworkouts.com/nova-api/'.$uriKey.'/'.$key.'/update-fields',
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
            '/achillesworkouts.com',
            '/achillesworkouts.com/dashboard',
        ];

        foreach (Nova::resourceCollection()->all() as $resourceClass) {
            $uriKey = $resourceClass::uriKey();
            $uris[] = '/achillesworkouts.com/resources/'.$uriKey;
            $uris[] = '/achillesworkouts.com/resources/'.$uriKey.'/new';

            $modelClass = $resourceClass::$model ?? null;

            if (! is_string($modelClass) || ! class_exists($modelClass)) {
                continue;
            }

            $model = new $modelClass();
            $key = $model->newQuery()->value($model->getKeyName());

            if ($key === null) {
                continue;
            }

            $uris[] = '/achillesworkouts.com/resources/'.$uriKey.'/'.$key;
            $uris[] = '/achillesworkouts.com/resources/'.$uriKey.'/'.$key.'/edit';
        }

        return array_values(array_unique($uris));
    }
}
