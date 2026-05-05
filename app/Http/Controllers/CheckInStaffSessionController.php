<?php

namespace App\Http\Controllers;

use App\Models\SystemStatus;
use App\Models\User;
use App\Models\WorkoutSession;
use App\Models\WorkoutSignup;
use App\Support\Attendance\CheckInSessionContext;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CheckInStaffSessionController extends Controller
{
    public function activate(Request $request, WorkoutSession $session, CheckInSessionContext $context): RedirectResponse
    {
        abort_unless($request->user(), 403);
        abort_unless($context->canManageSession($request->user(), $session), 403);
        abort_unless($context->isSessionWithinCheckInWindow($session), 403, __('Check-in can only be activated within the attendance window for this session.'));

        $session->loadMissing(['location.chapter.country', 'workout.activityType']);
        $context->activate($request->user(), $session);

        return redirect('/resources/workout-signups?resourceId='.$session->id)
            ->with('success', __('Check-in staff session activated.'));
    }

    public function deactivate(CheckInSessionContext $context): RedirectResponse
    {
        $context->deactivate();

        return redirect('/resources/workout-sessions')
            ->with('success', __('Check-in staff session ended.'));
    }

    public function refreshWeather(Request $request, CheckInSessionContext $context): RedirectResponse
    {
        $session = $context->currentSession();

        abort_unless($request->user() && $session, 403);
        abort_unless($context->canManageSession($request->user(), $session), 403);

        $context->refreshWeatherIfStale($session, true);

        return back()->with('success', __('Weather refreshed for the active check-in session.'));
    }

    public function checkInUser(Request $request, User $user, CheckInSessionContext $context): RedirectResponse
    {
        $session = $context->currentSession();

        abort_unless($request->user() && $session, 403);
        abort_unless($context->canManageSession($request->user(), $session), 403);

        $signup = WorkoutSignup::withTrashed()->firstOrCreate(
            [
                'workout_session_id' => $session->id,
                'user_id' => $user->id,
            ],
            [
                'status_id' => $this->signupStatusId('signup_confirmed', ['signup_pending']),
                'preferences' => ['created_by_attendance_staff' => true],
            ]
        );

        if ($signup->trashed()) {
            $signup->restore();
        }

        $signup->forceFill([
            'checked_in_at' => now(),
            'checked_out_at' => null,
            'status_id' => $this->signupStatusId('signup_checked_in', ['signup_confirmed', 'signup_pending']),
        ])->save();

        return back()->with('success', __('User checked in to the active session.'));
    }

    public function checkOutUser(Request $request, User $user, CheckInSessionContext $context): RedirectResponse
    {
        $session = $context->currentSession();

        abort_unless($request->user() && $session, 403);
        abort_unless($context->canManageSession($request->user(), $session), 403);

        $signup = WorkoutSignup::query()
            ->where('workout_session_id', $session->id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $signup->forceFill([
            'checked_out_at' => now(),
            'status_id' => $this->signupStatusId('signup_checked_out', ['signup_attended', 'signup_checked_in']),
        ])->save();

        return back()->with('success', __('User checked out from the active session.'));
    }

    private function signupStatusId(string $code, array $fallbackCodes = []): int
    {
        $statusId = SystemStatus::idForModel(WorkoutSignup::class, $code, $fallbackCodes);

        abort_if($statusId === null, 500, __('Workout signup status is not configured.'));

        return $statusId;
    }
}
