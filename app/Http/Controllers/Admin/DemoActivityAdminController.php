<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\DemoData\DemoUserRegistrationRedistributor;
use App\Support\DemoData\DemoWorkoutSignupWindowMaintainer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

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
        $shouldRedistributeUsers = $request->boolean('redistribute_users', true);
        $shouldRedistributeSignups = $request->boolean('redistribute_signups', true);
        $shouldBackfillHistory = $request->boolean('backfill_history', true);
        $shouldMaintainFuture = $request->boolean('maintain_future', true);
        $shouldAddTodayUsers = $request->boolean('add_today_users', true);
        $runInBackground = $request->boolean('background', false);

        if ($runInBackground) {
            $this->startBackgroundRun(
                $historyDays,
                $futureDays,
                $newUsersMin,
                $newUsersMax,
                $shouldRedistributeUsers,
                $shouldRedistributeSignups,
                $shouldBackfillHistory,
                $shouldMaintainFuture,
                $shouldAddTodayUsers,
            );

            return response()->json([
                'message' => 'Demo activity background job started.',
                'status' => $this->statusPayload(),
            ], 202);
        }

        $usersRedistributed = $shouldRedistributeUsers
            ? $userRedistributor->redistributeHistory($historyDays)
            : 0;
        $todayUsersCreated = $shouldAddTodayUsers
            ? $userRedistributor->createTodayUsers($newUsersMin, $newUsersMax)
            : 0;

        if ($shouldRedistributeSignups) {
            $signupMaintainer->redistributeExistingHistory($historyDays, $futureDays);
        }

        $historicSessions = $shouldBackfillHistory
            ? $signupMaintainer->backfillHistoricSessions($historyDays)
            : 0;
        $futureSessions = $shouldMaintainFuture
            ? $signupMaintainer->maintainUpcomingSessions($futureDays)
            : 0;

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

    public function status(Request $request): JsonResponse
    {
        abort_unless($request->user()?->canAccessNova(), 403);

        return response()->json($this->statusPayload());
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

    private function statusPayload(): array
    {
        $logPath = storage_path('logs/demo-activity-admin.log');
        $donePath = storage_path('logs/demo-activity-admin.done');
        $exitPath = storage_path('logs/demo-activity-admin.exit');

        return [
            'log_exists' => File::exists($logPath),
            'completed' => File::exists($donePath),
            'exit_code' => File::exists($exitPath) ? trim((string) File::get($exitPath)) : null,
            'log_tail' => File::exists($logPath)
                ? collect(preg_split('/\r\n|\r|\n/', (string) File::get($logPath)))
                    ->filter()
                    ->take(-20)
                    ->values()
                    ->all()
                : [],
        ];
    }

    private function startBackgroundRun(
        int $historyDays,
        int $futureDays,
        int $newUsersMin,
        int $newUsersMax,
        bool $shouldRedistributeUsers,
        bool $shouldRedistributeSignups,
        bool $shouldBackfillHistory,
        bool $shouldMaintainFuture,
        bool $shouldAddTodayUsers
    ): void {
        $logPath = storage_path('logs/demo-activity-admin.log');
        $donePath = storage_path('logs/demo-activity-admin.done');
        $exitPath = storage_path('logs/demo-activity-admin.exit');

        File::ensureDirectoryExists(dirname($logPath));
        File::delete([$logPath, $donePath, $exitPath]);

        $arguments = ['php', 'artisan', 'demo:simulate-activity'];

        if ($shouldRedistributeUsers) {
            $arguments[] = '--redistribute-users';
        }

        if ($shouldRedistributeSignups) {
            $arguments[] = '--redistribute-signups';
        }

        if ($shouldBackfillHistory) {
            $arguments[] = '--backfill-history';
        }

        if ($shouldMaintainFuture) {
            $arguments[] = '--maintain-future';
        }

        $arguments[] = '--history-days='.$historyDays;
        $arguments[] = '--future-days='.$futureDays;
        $arguments[] = '--new-users-min='.$newUsersMin;
        $arguments[] = '--new-users-max='.$newUsersMax;

        if ($shouldAddTodayUsers) {
            $arguments[] = '--add-today-users';
        }

        $artisanCommand = implode(' ', array_map('escapeshellarg', $arguments));
        $innerCommand = sprintf(
            '%s; code=$?; echo $code > %s; touch %s; exit $code',
            $artisanCommand,
            escapeshellarg($exitPath),
            escapeshellarg($donePath),
        );

        $shellCommand = sprintf(
            'mkdir -p %s && rm -f %s %s %s && nohup sh -lc %s > %s 2>&1 < /dev/null & echo STARTED',
            escapeshellarg(dirname($logPath)),
            escapeshellarg($logPath),
            escapeshellarg($donePath),
            escapeshellarg($exitPath),
            escapeshellarg($innerCommand),
            escapeshellarg($logPath),
        );

        Process::path(base_path())->run(['sh', '-lc', $shellCommand]);
    }
}
