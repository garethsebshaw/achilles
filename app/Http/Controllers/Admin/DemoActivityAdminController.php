<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData\DemoUserRegistrationRedistributor;
use App\Support\DemoData\DemoWorkoutSignupWindowMaintainer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemoActivityAdminController extends Controller
{
    public function run(
        Request $request,
        DemoUserRegistrationRedistributor $userRedistributor,
        DemoWorkoutSignupWindowMaintainer $signupMaintainer
    ): JsonResponse {
        abort_unless($request->user()?->canAccessNova(), 403);

        $historyDays = max(30, (int) $request->integer('history_days', config('demo_activity.history_days', 365)));
        $futureDays = max(7, (int) $request->integer('future_days', config('demo_activity.future_days', 28)));
        $newUsersMin = max(0, (int) $request->integer('new_users_min', config('demo_activity.user_registrations.daily_new_users_min', 2)));
        $newUsersMax = max($newUsersMin, (int) $request->integer('new_users_max', config('demo_activity.user_registrations.daily_new_users_max', 12)));
        $shouldAddTodayUsers = $request->boolean('add_today_users', true);

        $usersRedistributed = $userRedistributor->redistributeHistory($historyDays);
        $todayUsersCreated = $shouldAddTodayUsers
            ? $userRedistributor->createTodayUsers($newUsersMin, $newUsersMax)
            : 0;

        $signupMaintainer->redistributeExistingHistory($historyDays, $futureDays);
        $historicSessions = $signupMaintainer->backfillHistoricSessions($historyDays);
        $futureSessions = $signupMaintainer->maintainUpcomingSessions($futureDays);

        return response()->json([
            'message' => 'Demo activity updated.',
            'changes' => [
                'users_redistributed' => $usersRedistributed,
                'today_users_created' => $todayUsersCreated,
                'historic_sessions_updated' => $historicSessions,
                'future_sessions_updated' => $futureSessions,
            ],
            'summary' => $this->summaryPayload(),
        ]);
    }

    public function summary(Request $request): JsonResponse
    {
        abort_unless($request->user()?->canAccessNova(), 403);

        return response()->json($this->summaryPayload());
    }

    private function summaryPayload(): array
    {
        return [
            'user_registrations' => [
                'recent_days' => DB::table('users')
                    ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
                    ->where('created_at', '>=', now()->subDays(90)->startOfDay())
                    ->groupByRaw('DATE(created_at)')
                    ->orderBy('day')
                    ->limit(12)
                    ->get(),
                'peak_days' => DB::table('users')
                    ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
                    ->groupByRaw('DATE(created_at)')
                    ->orderByDesc('total')
                    ->limit(10)
                    ->get(),
            ],
            'workout_signups' => [
                'recent_days' => DB::table('workout_signups')
                    ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
                    ->where('created_at', '>=', now()->subDays(90)->startOfDay())
                    ->groupByRaw('DATE(created_at)')
                    ->orderBy('day')
                    ->limit(12)
                    ->get(),
                'peak_days' => DB::table('workout_signups')
                    ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
                    ->groupByRaw('DATE(created_at)')
                    ->orderByDesc('total')
                    ->limit(10)
                    ->get(),
                'upcoming_sessions' => DB::table('workout_sessions')
                    ->leftJoin('workout_signups', 'workout_signups.workout_session_id', '=', 'workout_sessions.id')
                    ->selectRaw('workout_sessions.id, workout_sessions.start_time, COUNT(workout_signups.id) as signup_total')
                    ->where('workout_sessions.start_time', '>=', now())
                    ->groupBy('workout_sessions.id', 'workout_sessions.start_time')
                    ->orderByDesc('signup_total')
                    ->limit(10)
                    ->get(),
            ],
        ];
    }
}
