<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Actions\EnableTwoFactorAuthentication;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class AuthSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', database_path('database.sqlite'));
        DB::purge('sqlite');
        DB::reconnect('sqlite');
    }

    public function test_password_reset_views_load(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSeeText('Forgot your password?');

        $this->get(route('password.reset', ['token' => 'test-token', 'email' => 'thisisg@gmail.com']))
            ->assertOk()
            ->assertSeeText('Reset password');

        $user = $this->createPrivilegedUser([
            'email' => 'two-factor-view@example.com',
            'password' => Hash::make('ViewPass123!'),
        ]);

        app(EnableTwoFactorAuthentication::class)($user);

        $secret = decrypt($user->fresh()->two_factor_secret);
        $code = app(Google2FA::class)->getCurrentOtp($secret);
        app(ConfirmTwoFactorAuthentication::class)($user->fresh(), $code);

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'ViewPass123!',
        ])->assertRedirect(route('two-factor.login'));

        $this->get(route('two-factor.login'))
            ->assertOk()
            ->assertSeeText('Two-factor challenge');
    }

    public function test_password_reset_link_can_be_requested(): void
    {
        Notification::fake();

        $user = $this->createPrivilegedUser([
            'email' => 'password-reset-admin@example.com',
        ]);

        $response = $this->from(route('password.request'))->post(route('password.email'), [
            'email' => $user->email,
        ]);

        $response->assertRedirect(route('password.request'));
        $response->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_can_be_reset_from_custom_reset_flow(): void
    {
        $user = $this->createPrivilegedUser([
            'email' => 'reset-target@example.com',
            'password' => Hash::make('OldPass123!'),
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewSecurePass123!',
            'password_confirmation' => 'NewSecurePass123!',
        ]);

        $response->assertRedirect(route('login'));
        $this->assertTrue(Hash::check('NewSecurePass123!', $user->fresh()->password));
    }

    public function test_authenticated_user_can_view_account_security_page(): void
    {
        $user = $this->createPrivilegedUser([
            'email' => 'security-page@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('account.security'))
            ->assertOk()
            ->assertSeeText('Account security')
            ->assertSeeText('Enable two-factor authentication');
    }

    public function test_authenticated_user_can_enable_confirm_regenerate_and_disable_two_factor_authentication(): void
    {
        $user = $this->createPrivilegedUser([
            'email' => 'two-factor-admin@example.com',
            'password' => Hash::make('CurrentPass123!'),
        ]);

        $this->actingAs($user);

        $this->post(route('account.security.two-factor.enable'))
            ->assertRedirect();

        $user = $user->fresh();

        $this->assertNotNull($user->two_factor_secret);
        $this->assertNotNull($user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);

        $secret = decrypt($user->two_factor_secret);
        $code = app(Google2FA::class)->getCurrentOtp($secret);

        $this->post(route('account.security.two-factor.confirm'), [
            'code' => $code,
        ])->assertRedirect();

        $user = $user->fresh();

        $this->assertNotNull($user->two_factor_confirmed_at);

        $originalRecoveryCodes = $user->recoveryCodes();

        $this->post(route('account.security.two-factor.recovery-codes'))
            ->assertRedirect();

        $user = $user->fresh();

        $this->assertNotSame($originalRecoveryCodes, $user->recoveryCodes());

        $this->delete(route('account.security.two-factor.disable'))
            ->assertRedirect();

        $user = $user->fresh();

        $this->assertNull($user->two_factor_secret);
        $this->assertNull($user->two_factor_recovery_codes);
        $this->assertNull($user->two_factor_confirmed_at);
    }

    public function test_authenticated_user_can_update_password_from_account_security_page(): void
    {
        $user = $this->createPrivilegedUser([
            'email' => 'password-update-admin@example.com',
            'password' => Hash::make('CurrentPass123!'),
        ]);

        $this->actingAs($user)
            ->put(route('account.security.password.update'), [
                'current_password' => 'CurrentPass123!',
                'password' => 'ReplacementPass123!',
                'password_confirmation' => 'ReplacementPass123!',
            ])
            ->assertRedirect();

        $this->assertTrue(Hash::check('ReplacementPass123!', $user->fresh()->password));
    }

    protected function createPrivilegedUser(array $overrides = []): User
    {
        if (isset($overrides['email']) && is_string($overrides['email']) && str_contains($overrides['email'], '@')) {
            [$local, $domain] = explode('@', $overrides['email'], 2);
            $overrides['email'] = $local.'+'.Str::uuid()->toString().'@'.$domain;
        }

        return User::factory()->create(array_merge([
            'first_name' => 'Test',
            'last_name' => 'User',
            'preferred_name' => 'Test User',
            'phone' => '555-0100',
            'is_sys_admin' => true,
        ], $overrides));
    }
}
