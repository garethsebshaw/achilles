<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoActivityCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration_randomizer_spreads_demo_users_over_time(): void
    {
        User::factory()->count(24)->create([
            'is_sys_admin' => false,
            'is_admin' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->artisan('demo:simulate-activity', [
            '--redistribute-users' => true,
            '--history-days' => 30,
        ])->assertExitCode(0);

        $distinctDays = User::query()
            ->selectRaw('DATE(created_at) as day')
            ->groupByRaw('DATE(created_at)')
            ->pluck('day')
            ->count();

        $oldest = CarbonImmutable::parse(User::query()->min('created_at'));
        $newest = CarbonImmutable::parse(User::query()->max('created_at'));

        $this->assertGreaterThan(5, $distinctDays);
        $this->assertTrue($oldest->greaterThanOrEqualTo(now()->subDays(29)->startOfDay()));
        $this->assertTrue($newest->lessThanOrEqualTo(now()->endOfDay()));
    }
}
