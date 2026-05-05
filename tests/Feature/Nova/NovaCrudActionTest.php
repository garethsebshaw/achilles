<?php

namespace Tests\Feature\Nova;

use App\Models\MeetingPoint;
use App\Models\Equipment;
use App\Models\Event;
use App\Models\MaintenanceRequest;
use App\Models\SystemCategory;
use App\Models\SystemChapter;
use App\Models\SystemLocation;
use App\Models\SystemModule;
use App\Models\SystemStatus;
use App\Models\User;
use App\Models\Workout;
use App\Models\WorkoutSession;
use App\Models\WorkoutSignup;
use App\Models\WorkoutSpecificDetails;
use App\Nova\Actions\CheckInAction;
use App\Nova\Actions\CheckOutAction;
use App\Nova\Actions\GenerateDemoSessionSignups;
use App\Nova\Actions\ManageWorkoutAttendance;
use App\Nova\Actions\ViewSessionUsers;
use App\Nova\Actions\ViewWeatherData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\ActionFields;
use Tests\TestCase;

class NovaCrudActionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite.database', database_path('database.sqlite'));
        DB::purge('sqlite');
        DB::reconnect('sqlite');
    }

    public function test_workout_crud_flow_supports_versioned_updates(): void
    {
        $this->actingAs($this->admin());

        $createResponse = $this->post('/workouts', [
            'name' => 'Codex Workout '.uniqid(),
            'location_id' => $this->locationId(),
            'activity_type_id' => $this->workoutCategoryId(),
            'default_start_time' => '08:00',
            'default_end_time' => '09:00',
            'is_recurring' => true,
            'recurrence_pattern' => 'weekly',
            'advance_create_weeks' => 12,
            'default_max_athletes' => 8,
            'default_max_guides' => 6,
            'description' => 'Initial test workout',
        ]);

        $createResponse->assertRedirect();

        $workout = Workout::query()->latest('id')->firstOrFail();

        $this->assertSame('Initial test workout', $workout->description);
        $this->assertSame($this->admin()->id, $workout->created_by);

        $updateResponse = $this->put('/workouts/'.$workout->id, [
            'name' => $workout->name.' Updated',
            'location_id' => $workout->location_id,
            'activity_type_id' => $workout->activity_type_id,
            'default_start_time' => '08:30',
            'default_end_time' => '09:30',
            'is_recurring' => false,
            'recurrence_pattern' => null,
            'advance_create_weeks' => 16,
            'default_max_athletes' => 10,
            'default_max_guides' => 7,
            'description' => 'Updated test workout',
        ]);

        $updateResponse->assertRedirect();

        $updatedWorkout = Workout::query()->latest('id')->firstOrFail();

        $this->assertNotSame($workout->id, $updatedWorkout->id);
        $this->assertSame(2, $updatedWorkout->version);
        $this->assertTrue((bool) $updatedWorkout->is_current_version);
        $this->assertFalse((bool) $workout->fresh()->is_current_version);

        $deleteResponse = $this->delete('/workouts/'.$updatedWorkout->id);

        $deleteResponse->assertRedirect('/workouts');
        $this->assertSoftDeleted('workouts', ['id' => $updatedWorkout->id]);
    }

    public function test_workout_session_crud_flow_soft_deletes_records(): void
    {
        $this->actingAs($this->admin());

        $workout = Workout::query()->where('is_current_version', true)->firstOrFail();

        $createResponse = $this->post('/workout-sessions', [
            'workout_id' => $workout->id,
            'location_id' => $this->locationId(),
            'session_date' => now()->addDays(7)->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:30',
            'max_athletes' => 12,
            'max_guides' => 8,
            'status_id' => $this->sessionStatusId(),
            'notes' => 'Initial session notes',
        ]);

        $createResponse->assertRedirect();

        $session = WorkoutSession::query()->latest('id')->firstOrFail();
        $this->assertSame('Initial session notes', $session->notes);

        $updateResponse = $this->put('/workout-sessions/'.$session->id, [
            'workout_id' => $workout->id,
            'location_id' => $this->locationId(),
            'session_date' => now()->addDays(8)->toDateString(),
            'start_time' => '10:30',
            'end_time' => '12:00',
            'max_athletes' => 14,
            'max_guides' => 9,
            'status_id' => $this->sessionStatusId('session_progress'),
            'notes' => 'Updated session notes',
        ]);

        $updateResponse->assertRedirect('/workout-sessions/'.$session->id);
        $this->assertSame('Updated session notes', $session->fresh()->notes);

        $deleteResponse = $this->delete('/workout-sessions/'.$session->id);

        $deleteResponse->assertRedirect('/workout-sessions');
        $this->assertSoftDeleted('workout_sessions', ['id' => $session->id]);
    }

    public function test_meeting_point_crud_flow_validates_and_soft_deletes_records(): void
    {
        $this->actingAs($this->admin());

        $createResponse = $this->post('/meeting-points', [
            'name' => 'Codex Meeting Point '.uniqid(),
            'address' => '123 Test Lane',
            'latitude' => 40.7128,
            'longitude' => -74.0060,
            'type_id' => $this->workoutCategoryId(),
            'chapter_id' => $this->chapterId(),
            'metadata' => ['parking' => true],
        ]);

        $createResponse->assertRedirect();

        $meetingPoint = MeetingPoint::query()->latest('id')->firstOrFail();
        $this->assertSame('123 Test Lane', $meetingPoint->address);

        $updateResponse = $this->put('/meeting-points/'.$meetingPoint->id, [
            'name' => $meetingPoint->name.' Updated',
            'address' => '456 Updated Avenue',
            'latitude' => 41.0001,
            'longitude' => -73.9999,
            'type_id' => $meetingPoint->type_id,
            'chapter_id' => $meetingPoint->chapter_id,
            'metadata' => ['parking' => false],
        ]);

        $updateResponse->assertRedirect('/meeting-points/'.$meetingPoint->id);
        $this->assertSame('456 Updated Avenue', $meetingPoint->fresh()->address);

        $deleteResponse = $this->delete('/meeting-points/'.$meetingPoint->id);

        $deleteResponse->assertRedirect('/meeting-points');
        $this->assertSoftDeleted('meeting_points', ['id' => $meetingPoint->id]);
    }

    public function test_workout_signup_crud_flow_updates_specific_details(): void
    {
        $this->actingAs($this->admin());

        $templateDetails = WorkoutSpecificDetails::query()->firstOrFail();
        $session = WorkoutSession::query()->firstOrFail();
        $guide = User::query()->whereNull('deleted_at')->where('is_guide', true)->firstOrFail();
        $athlete = User::query()->whereNull('deleted_at')->where('is_athlete', true)->whereKeyNot($guide->id)->firstOrFail();
        $reassignedAthlete = User::query()->whereNull('deleted_at')->where('is_athlete', true)->whereKeyNot($athlete->id)->whereKeyNot($guide->id)->firstOrFail();

        $createResponse = $this->post('/workout-signups', [
            'workout_session_id' => $session->id,
            'user_id' => $guide->id,
            'athlete_id' => $athlete->id,
            'status_id' => $this->signupStatusId(),
            'preferences' => ['transport' => 'needs ride'],
            'equipment_requirements' => ['helmet' => true],
            'specific_details' => [
                'sport_category_id' => $templateDetails->sport_category_id,
                'distance_unit_id' => $templateDetails->distance_unit_id,
                'pace_unit_id' => $templateDetails->pace_unit_id,
                'speed_unit_id' => $templateDetails->speed_unit_id,
                'distance' => 5.25,
                'time' => 42.5,
                'pace_min' => 8.25,
                'pace_max' => 9.75,
                'speed_min' => 10.1,
                'speed_max' => 12.2,
                'additional_details' => ['note' => 'Created by test'],
            ],
        ]);

        $createResponse->assertRedirect();

        $signup = WorkoutSignup::query()->latest('id')->firstOrFail();

        $this->assertSame($session->id, $signup->workout_session_id);
        $this->assertSame($guide->id, $signup->user_id);
        $this->assertSame($athlete->id, $signup->athlete_id);
        $this->assertSame('needs ride', $signup->preferences['transport']);
        $this->assertNotNull($signup->specificDetails);
        $this->assertSame(5.25, (float) $signup->specificDetails->distance);

        $updateResponse = $this->put('/workout-signups/'.$signup->id, [
            'workout_session_id' => $session->id,
            'user_id' => $guide->id,
            'athlete_id' => $reassignedAthlete->id,
            'status_id' => $this->signupStatusId('signup_checked_in'),
            'preferences' => ['transport' => 'self'],
            'equipment_requirements' => ['helmet' => false],
            'specific_details' => [
                'sport_category_id' => $templateDetails->sport_category_id,
                'distance_unit_id' => $templateDetails->distance_unit_id,
                'pace_unit_id' => $templateDetails->pace_unit_id,
                'speed_unit_id' => $templateDetails->speed_unit_id,
                'distance' => 10.5,
                'time' => 75.0,
                'pace_min' => 7.5,
                'pace_max' => 8.5,
                'speed_min' => 13.0,
                'speed_max' => 14.0,
                'additional_details' => ['note' => 'Updated by test'],
            ],
        ]);

        $updateResponse->assertRedirect('/workout-signups/'.$signup->id);

        $signup = $signup->fresh(['specificDetails']);
        $this->assertSame('self', $signup->preferences['transport']);
        $this->assertSame($reassignedAthlete->id, $signup->athlete_id);
        $this->assertSame($this->signupStatusId('signup_checked_in'), $signup->status_id);
        $this->assertSame(10.5, (float) $signup->specificDetails->distance);

        $deleteResponse = $this->delete('/workout-signups/'.$signup->id);

        $deleteResponse->assertRedirect('/workout-signups');
        $this->assertSoftDeleted('workout_signups', ['id' => $signup->id]);
    }

    public function test_nova_attendance_actions_mutate_signup_state_and_navigation_actions_return_expected_urls(): void
    {
        $signup = WorkoutSignup::query()->whereNull('checked_in_at')->firstOrFail();
        $signup->forceFill([
            'checked_in_at' => null,
            'checked_out_at' => null,
        ])->save();

        $emptyFields = new ActionFields(collect(), collect());

        $checkInResponse = (new CheckInAction())->handle($emptyFields, new Collection([$signup]));
        $this->assertArrayHasKey('message', $checkInResponse->jsonSerialize());
        $checkedInSignup = $signup->fresh();
        $this->assertNotNull($checkedInSignup->checked_in_at);
        $this->assertSame($this->signupStatusId('signup_checked_in'), $checkedInSignup->status_id);

        $checkOutResponse = (new CheckOutAction())->handle($emptyFields, new Collection([$checkedInSignup]));
        $this->assertArrayHasKey('message', $checkOutResponse->jsonSerialize());
        $checkedOutSignup = $checkedInSignup->fresh();
        $this->assertNotNull($checkedOutSignup->checked_out_at);
        $this->assertSame($this->signupStatusId('signup_checked_out'), $checkedOutSignup->status_id);

        $managedSignup = $checkedOutSignup->fresh();
        (new ManageWorkoutAttendance())->handle(
            new ActionFields(collect(['action' => 'cancel-check-in']), collect()),
            new Collection([$managedSignup])
        );

        $managedSignup = $managedSignup->fresh();
        $this->assertNull($managedSignup->checked_in_at);
        $this->assertNull($managedSignup->checked_out_at);
        $this->assertSame($this->signupStatusId('signup_confirmed'), $managedSignup->status_id);

        $visitResponse = (new ViewSessionUsers())->handle($emptyFields, new Collection([$managedSignup->workoutSession]));
        $visitPayload = json_decode(json_encode($visitResponse, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(
            '/attendance/sessions/'.$managedSignup->workout_session_id.'/activate',
            $visitPayload['visit']['path']
        );

        $location = SystemLocation::query()->firstOrFail();
        $weatherResponse = (new ViewWeatherData())->handle($emptyFields, new Collection([$location]));
        $weatherPayload = json_decode(json_encode($weatherResponse, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        $this->assertSame(
            route('weather.view', ['location' => $location->id]),
            $weatherPayload['redirect']['url']
        );
        $this->assertTrue($weatherPayload['redirect']['openInNewTab']);
    }

    public function test_status_relations_are_scoped_to_their_modules(): void
    {
        $workoutStatusId = $this->statusIdForModule(Workout::class, 'workout_active');
        $session = WorkoutSession::query()->firstOrFail();
        $session->forceFill(['status_id' => $workoutStatusId])->save();
        $this->assertNull($session->fresh()->status);

        $eventStatusId = $this->statusIdForModule(Event::class, 'event_draft');
        $equipment = Equipment::query()->firstOrFail();
        $equipment->forceFill(['status_id' => $eventStatusId])->save();
        $this->assertNull($equipment->fresh()->status);

        $maintenanceStatusId = $this->statusIdForModule(MaintenanceRequest::class, 'maintreq_reported');
        $event = Event::query()->firstOrFail();
        $event->forceFill(['status_id' => $maintenanceStatusId])->save();
        $this->assertNull($event->fresh()->status);
    }

    public function test_demo_signup_generation_action_creates_multi_guide_demo_roster(): void
    {
        $session = WorkoutSession::query()->create([
            'workout_id' => Workout::query()->where('is_current_version', true)->value('id'),
            'location_id' => $this->locationId(),
            'session_date' => now()->addDays(10)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'status_id' => $this->sessionStatusId(),
            'notes' => 'Demo signup generation test session',
        ]);

        $response = (new GenerateDemoSessionSignups())->handle(
            new ActionFields(collect([
                'minimum_athletes' => 6,
                'maximum_athletes' => 8,
                'maximum_guides_per_athlete' => 3,
                'heavy_session_mode' => false,
            ]), collect()),
            new Collection([$session])
        );

        $payload = json_decode(json_encode($response, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
        $this->assertStringContainsString('Generated', $payload['message']);

        $signups = WorkoutSignup::query()
            ->where('workout_session_id', $session->id)
            ->get();

        $this->assertGreaterThanOrEqual(6, $signups->whereNull('athlete_id')->count());
        $this->assertGreaterThan(0, $signups->whereNotNull('athlete_id')->count());
        $this->assertTrue(
            $signups->whereNotNull('athlete_id')
                ->groupBy('athlete_id')
                ->contains(fn (Collection $group) => $group->count() > 1)
        );
    }

    public function test_active_check_in_staff_can_check_user_in_via_attendance_route(): void
    {
        $admin = $this->admin();
        $user = User::query()->whereNull('deleted_at')->where('is_athlete', true)->firstOrFail();
        $session = WorkoutSession::query()->firstOrFail();
        $windowStart = now()->copy()->addHour();

        $session->forceFill([
            'session_date' => $windowStart->toDateString(),
            'start_time' => $windowStart->format('H:i:s'),
            'end_time' => $windowStart->copy()->addHours(2)->format('H:i:s'),
        ])->save();

        $this->actingAs($admin)
            ->get('/attendance/sessions/'.$session->id.'/activate')
            ->assertRedirect('/resources/workout-signups?resourceId='.$session->id);

        $this->actingAs($admin)
            ->from('/resources/users')
            ->get('/attendance/users/'.$user->id.'/check-in')
            ->assertRedirect('/resources/users');

        $signup = WorkoutSignup::query()
            ->where('workout_session_id', $session->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $this->assertNotNull($signup->checked_in_at);
        $this->assertSame($this->signupStatusId('signup_checked_in'), $signup->status_id);
    }

    private function admin(): User
    {
        return User::query()->where('email', 'thisisg@gmail.com')->firstOrFail();
    }

    private function workoutCategoryId(): int
    {
        return SystemCategory::query()
            ->where('system_module_id', $this->workoutModuleId())
            ->value('id');
    }

    private function workoutModuleId(): int
    {
        return SystemModule::query()
            ->where('model_type', Workout::class)
            ->value('id');
    }

    private function sessionStatusId(?string $code = 'session_scheduled'): int
    {
        return $this->statusIdForModule(WorkoutSession::class, $code);
    }

    private function signupStatusId(?string $code = 'signup_pending'): int
    {
        return $this->statusIdForModule(WorkoutSignup::class, $code);
    }

    private function statusIdForModule(string $modelClass, ?string $preferredCode = null): int
    {
        $moduleId = SystemModule::query()->where('model_type', $modelClass)->value('id');

        return SystemStatus::query()
            ->where('system_module_id', $moduleId)
            ->when($preferredCode, fn ($query) => $query->where('code', $preferredCode))
            ->value('id')
            ?? SystemStatus::query()->where('system_module_id', $moduleId)->value('id');
    }

    private function locationId(): int
    {
        return SystemLocation::query()->value('id');
    }

    private function chapterId(): int
    {
        return SystemChapter::query()->value('id');
    }
}
