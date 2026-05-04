<?php

namespace Tests\Feature\Nova;

use App\Models\SystemModule;
use App\Models\SystemNotification;
use App\Models\User;
use App\Notifications\SystemMessageNotification;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tests\TestCase;

#[RunTestsInSeparateProcesses]
#[PreserveGlobalState(false)]
class NotificationSystemTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', database_path('database.sqlite'));
        DB::purge('sqlite');
        DB::reconnect('sqlite');
    }

    public function test_notification_module_is_aligned_to_system_notification_model(): void
    {
        $module = SystemModule::query()
            ->where('model_type', SystemNotification::class)
            ->first();

        $this->assertNotNull($module);
        $this->assertSame('Notifications', $module->name);
        $this->assertTrue((bool) $module->active);
    }

    public function test_user_notification_relations_use_system_notifications_table(): void
    {
        $user = User::query()->where('email', 'thisisg@gmail.com')->firstOrFail();

        $expectedCount = SystemNotification::query()
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->count();

        $this->assertSame($expectedCount, $user->notifications()->count());
        $this->assertSame(
            SystemNotification::class,
            $user->notifications()->getModel()::class
        );
    }

    public function test_database_notifications_are_persisted_to_system_notifications(): void
    {
        $user = User::query()->where('email', 'thisisg@gmail.com')->firstOrFail();

        $beforeCount = $user->notifications()->count();

        $user->notify(new SystemMessageNotification(
            'Codex Test Notification',
            'Notification pipeline stored this message in system_notifications.',
            ['channel' => 'test']
        ));

        $notification = $user->notifications()->latest()->first();

        $this->assertNotNull($notification);
        $this->assertSame($beforeCount + 1, $user->notifications()->count());
        $this->assertSame(SystemNotification::class, $notification::class);
        $this->assertSame('Codex Test Notification', $notification->title);
        $this->assertSame(
            'Notification pipeline stored this message in system_notifications.',
            $notification->message
        );
        $this->assertSame(User::class, $notification->notifiable_type);
        $this->assertSame($user->id, $notification->notifiable_id);
    }

    public function test_notification_can_transition_between_unread_and_read_states(): void
    {
        $user = User::query()->where('email', 'thisisg@gmail.com')->firstOrFail();

        $user->notify(new SystemMessageNotification(
            'Read State Probe',
            'This notification verifies markAsRead and markAsUnread.'
        ));

        /** @var \App\Models\SystemNotification $notification */
        $notification = $user->notifications()->latest()->firstOrFail();

        $this->assertTrue($user->unreadNotifications()->whereKey($notification->id)->exists());

        $notification->markAsRead();
        $this->assertTrue($user->readNotifications()->whereKey($notification->id)->exists());

        $notification->markAsUnread();
        $this->assertTrue($user->unreadNotifications()->whereKey($notification->id)->exists());
    }

    public function test_system_notification_nova_resource_loads_for_seeded_sys_admin(): void
    {
        $admin = User::query()->where('email', 'thisisg@gmail.com')->firstOrFail();
        $notification = SystemNotification::query()->firstOrFail();

        $this->actingAs($admin);

        foreach ([
            '/resources/system-notifications',
            '/resources/system-notifications/new',
            '/resources/system-notifications/'.$notification->getKey(),
            '/resources/system-notifications/'.$notification->getKey().'/edit',
            '/nova-api/system-notifications',
            '/nova-api/system-notifications/creation-fields',
            '/nova-api/system-notifications/'.$notification->getKey(),
            '/nova-api/system-notifications/'.$notification->getKey().'/update-fields',
        ] as $uri) {
            $response = str_contains($uri, '/nova-api/')
                ? $this->getJson($uri)
                : $this->get($uri);

            $this->assertLessThan(
                500,
                $response->status(),
                sprintf('Notification Nova endpoint failed for [%s] with status [%d]', $uri, $response->status())
            );
        }
    }
}
