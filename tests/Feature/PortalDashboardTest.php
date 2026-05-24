<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\Logging\DefaultTenantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(DefaultTenantSeeder::class);
    }

    public function test_guest_is_redirected_from_portal_dashboard(): void
    {
        $this->get('/portal/dashboard')
            ->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_portal_dashboard(): void
    {
        $user = User::factory()->create([
            'is_athlete' => true,
        ]);

        $this->actingAs($user)
            ->get('/portal/dashboard')
            ->assertOk()
            ->assertSee('portal-dashboard')
            ->assertSee('Portal Dashboard');
    }

    public function test_authenticated_user_can_fetch_portal_dashboard_data(): void
    {
        $user = User::factory()->create([
            'is_guide' => true,
        ]);

        $this->actingAs($user)
            ->getJson('/portal/dashboard/data?days=30')
            ->assertOk()
            ->assertJsonStructure([
                'generated_at',
                'days',
                'summary' => ['users', 'chapters', 'sessions', 'signups'],
                'series' => [
                    'user_registrations' => ['labels', 'values'],
                    'workout_signups' => ['labels', 'values'],
                ],
            ])
            ->assertJson([
                'days' => 30,
            ]);
    }
}
