<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\Logging\DefaultTenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoActivityAdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DefaultTenantSeeder::class);
    }

    public function test_non_privileged_user_cannot_run_demo_activity(): void
    {
        $user = User::factory()->create([
            'is_admin' => false,
            'is_sys_admin' => false,
            'is_team_leader' => false,
        ]);

        $this->actingAs($user)
            ->postJson('/admin/demo-activity/run')
            ->assertForbidden();
    }

    public function test_privileged_user_can_fetch_demo_activity_summary(): void
    {
        $user = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->actingAs($user)
            ->getJson('/admin/demo-activity/summary')
            ->assertOk()
            ->assertJsonStructure([
                'user_registrations' => ['recent_days', 'peak_days'],
                'workout_signups' => ['recent_days', 'peak_days', 'upcoming_sessions'],
            ]);
    }

    public function test_privileged_user_can_run_demo_activity(): void
    {
        User::factory()->count(12)->create([
            'is_sys_admin' => false,
            'is_admin' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = User::factory()->create([
            'is_sys_admin' => true,
        ]);

        $this->actingAs($user)
            ->postJson('/admin/demo-activity/run', [
                'history_days' => 45,
                'future_days' => 14,
                'add_today_users' => false,
            ])
            ->assertOk()
            ->assertJsonStructure([
                'message',
                'changes' => [
                    'users_redistributed',
                    'today_users_created',
                    'historic_sessions_updated',
                    'future_sessions_updated',
                ],
                'summary',
            ])
            ->assertJsonPath('changes.today_users_created', 0);
    }

    public function test_privileged_user_can_start_background_demo_activity(): void
    {
        $user = User::factory()->create([
            'is_sys_admin' => true,
        ]);

        $this->actingAs($user)
            ->postJson('/admin/demo-activity/run', [
                'background' => true,
                'history_days' => 30,
                'future_days' => 7,
            ])
            ->assertStatus(202)
            ->assertJsonStructure([
                'message',
                'status' => [
                    'log_exists',
                    'completed',
                    'exit_code',
                    'log_tail',
                ],
            ]);
    }
}
