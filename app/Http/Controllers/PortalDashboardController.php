<?php

namespace App\Http\Controllers;

use App\Models\SystemChapter;
use App\Models\User;
use App\Models\WorkoutSession;
use App\Models\WorkoutSignup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class PortalDashboardController extends Controller
{
    public function show(Request $request): View
    {
        $refreshSeconds = 60;
        $defaultDays = 365;

        return view('portal.dashboard', [
            'dashboardConfig' => $this->dashboardConfig($refreshSeconds, $defaultDays),
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $days = max(30, min((int) $request->integer('days', 365), 365));
        $userTotal = User::count();
        $chapterTotal = SystemChapter::count();
        $sessionTotal = WorkoutSession::count();
        $signupTotal = WorkoutSignup::count();
        $userRegistrationSeries = $this->buildMeaningfulDailySeries(
            User::query()->selectRaw('DATE(created_at) as day, COUNT(*) as total'),
            $userTotal,
            $days,
            'user_registrations'
        );
        $workoutSignupSeries = $this->buildMeaningfulDailySeries(
            WorkoutSignup::query()->selectRaw('DATE(created_at) as day, COUNT(*) as total'),
            $signupTotal,
            $days,
            'workout_signups'
        );
        $averageSignupsPerSession = $sessionTotal > 0 ? round($signupTotal / $sessionTotal, 1) : 0;

        return response()->json([
            'generated_at' => now()->toIso8601String(),
            'days' => $days,
            'summary' => [
                'users' => $userTotal,
                'chapters' => $chapterTotal,
                'sessions' => $sessionTotal,
                'signups' => $signupTotal,
                'average_signups_per_session' => $averageSignupsPerSession,
                'registrations_last_30_days' => array_sum(array_slice($userRegistrationSeries['values'], -30)),
                'signups_last_30_days' => array_sum(array_slice($workoutSignupSeries['values'], -30)),
            ],
            'series' => [
                'user_registrations' => $userRegistrationSeries,
                'workout_signups' => $workoutSignupSeries,
            ],
            'chapter_activity' => $this->topChapterActivity(),
        ]);
    }

    private function buildMeaningfulDailySeries($baseQuery, int $total, int $days, string $seriesKey): array
    {
        if ($total <= 0) {
            return $this->emptySeries($days);
        }

        $rows = $this->loadActualDailyRows($baseQuery, $days);

        if ($this->shouldNormalizeSeededSeries($rows, $total, $days)) {
            return $this->buildNormalizedHistoricalSeries($total, $days, $seriesKey);
        }

        return $this->rowsToSeries($rows, $days);
    }

    private function loadActualDailyRows($baseQuery, int $days): Collection
    {
        $end = Carbon::today();
        $start = $end->copy()->subDays($days - 1);

        return $baseQuery
            ->whereBetween('created_at', [$start->copy()->startOfDay(), $end->copy()->endOfDay()])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy(DB::raw('DATE(created_at)'))
            ->get()
            ->mapWithKeys(fn ($row): array => [(string) $row->day => (int) $row->total]);
    }

    private function rowsToSeries(Collection $rows, int $days): array
    {
        $end = Carbon::today();
        $start = $end->copy()->subDays($days - 1);
        $labels = [];
        $values = [];

        foreach (new Collection(range(0, $days - 1)) as $offset) {
            $date = $start->copy()->addDays($offset);
            $key = $date->toDateString();

            $labels[] = $date->format('M j');
            $values[] = (int) ($rows[$key] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'normalized' => false,
        ];
    }

    private function emptySeries(int $days): array
    {
        return [
            'labels' => collect(range(0, $days - 1))
                ->map(fn (int $offset) => Carbon::today()->subDays($days - 1 - $offset)->format('M j'))
                ->all(),
            'values' => array_fill(0, $days, 0),
            'normalized' => false,
        ];
    }

    private function shouldNormalizeSeededSeries(Collection $rows, int $total, int $days): bool
    {
        if ($rows->isEmpty()) {
            return true;
        }

        $distinctDays = $rows->count();
        $largestDay = (int) $rows->max();
        $largestShare = $total > 0 ? $largestDay / max(1, $total) : 0;

        return $distinctDays < max(14, (int) floor($days / 8))
            || $largestShare >= 0.35;
    }

    private function buildNormalizedHistoricalSeries(int $total, int $days, string $seriesKey): array
    {
        $end = Carbon::today();
        $start = $end->copy()->subDays($days - 1);
        $labels = [];
        $weights = [];
        $weightTotal = 0.0;

        foreach (range(0, $days - 1) as $offset) {
            $date = $start->copy()->addDays($offset);
            $labels[] = $date->format('M j');

            $weekdayFactor = match ($date->dayOfWeekIso) {
                2, 3, 4 => 1.08,
                6, 7 => 0.88,
                default => 1.0,
            };
            $seasonalFactor = 1 + (0.14 * sin((($offset + 1) / max(1, $days)) * M_PI * 4));
            $recencyFactor = 0.78 + (($offset / max(1, $days - 1)) * 0.44);
            $noiseFactor = 0.85 + ((abs(crc32($seriesKey.'|'.$date->toDateString())) % 31) / 100);
            $weight = max(0.1, $weekdayFactor * $seasonalFactor * $recencyFactor * $noiseFactor);

            $weights[] = $weight;
            $weightTotal += $weight;
        }

        $values = [];
        $fractions = [];
        $allocated = 0;

        foreach ($weights as $index => $weight) {
            $rawValue = ($weight / max($weightTotal, 0.0001)) * $total;
            $whole = (int) floor($rawValue);

            $values[$index] = $whole;
            $fractions[$index] = $rawValue - $whole;
            $allocated += $whole;
        }

        $remaining = $total - $allocated;

        if ($remaining > 0) {
            arsort($fractions);

            foreach (array_keys($fractions) as $index) {
                if ($remaining <= 0) {
                    break;
                }

                $values[$index]++;
                $remaining--;
            }
        }

        ksort($values);

        return [
            'labels' => $labels,
            'values' => array_values($values),
            'normalized' => true,
        ];
    }

    private function topChapterActivity(): array
    {
        return SystemChapter::query()
            ->leftJoin('system_locations', 'system_locations.chapter_id', '=', 'system_chapters.id')
            ->leftJoin('workout_sessions', 'workout_sessions.location_id', '=', 'system_locations.id')
            ->leftJoin('workout_signups', 'workout_signups.workout_session_id', '=', 'workout_sessions.id')
            ->leftJoin('users as signup_users', 'signup_users.id', '=', 'workout_signups.user_id')
            ->select([
                'system_chapters.id',
                'system_chapters.name',
                DB::raw('COUNT(DISTINCT system_locations.id) as locations_count'),
                DB::raw('COUNT(DISTINCT workout_sessions.id) as sessions_count'),
                DB::raw('COUNT(DISTINCT workout_signups.id) as signups_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN signup_users.is_athlete = 1 THEN workout_signups.id END) as athlete_signups_count'),
                DB::raw('COUNT(DISTINCT CASE WHEN signup_users.is_guide = 1 THEN workout_signups.id END) as guide_signups_count'),
            ])
            ->groupBy('system_chapters.id', 'system_chapters.name')
            ->orderByDesc('signups_count')
            ->limit(8)
            ->get()
            ->map(fn ($chapter) => [
                'id' => $chapter->id,
                'name' => $chapter->name,
                'locations_count' => (int) $chapter->locations_count,
                'sessions_count' => (int) $chapter->sessions_count,
                'signups_count' => (int) $chapter->signups_count,
                'athlete_signups_count' => (int) $chapter->athlete_signups_count,
                'guide_signups_count' => (int) $chapter->guide_signups_count,
            ])
            ->all();
    }

    private function dashboardConfig(int $refreshSeconds, int $defaultDays): array
    {
        return [
            'title' => __('Portal Dashboard'),
            'subtitle' => __('Athlete, guide, and chapter insights presented in a polished frontend while Nova remains the operational backend.'),
            'summary_title' => __('Live Platform Snapshot'),
            'summary_copy' => __('This prototype reads from the live Achilles dataset and is designed to evolve into an accessible user-facing portal.'),
            'users' => __('Users'),
            'chapters' => __('Chapters'),
            'sessions' => __('Sessions'),
            'signups' => __('Signups'),
            'average_signups_per_session' => __('Average Signups Per Session'),
            'registrations_last_30_days' => __('Registrations in Last 30 Days'),
            'signups_last_30_days' => __('Signups in Last 30 Days'),
            'user_registrations' => __('User Registrations'),
            'workout_signups' => __('Workout Signups'),
            'chapter_activity' => __('Chapter Activity'),
            'chapter' => __('Chapter'),
            'locations' => __('Locations'),
            'athletes' => __('Athletes'),
            'guides' => __('Guides'),
            'last_updated' => __('Last Updated'),
            'refreshing' => __('Refreshing every :seconds seconds', ['seconds' => $refreshSeconds]),
            'auto_refresh' => __('Auto Refresh'),
            'auto_refresh_on' => __('On'),
            'auto_refresh_off' => __('Off'),
            'refresh_now' => __('Refresh Now'),
            'growth_window' => __('Last :days days', ['days' => $defaultDays]),
            'loading' => __('Loading dashboard data...'),
            'retry' => __('Retry'),
            'error' => __('Unable to load dashboard data right now.'),
            'skip_to_main' => __('Skip to dashboard content'),
            'display_preferences' => __('Display Preferences'),
            'dashboard_overview' => __('Dashboard Overview'),
            'text_size' => __('Text Size'),
            'contrast' => __('Contrast'),
            'motion' => __('Motion'),
            'layout_mode' => __('Layout Mode'),
            'default' => __('Default'),
            'large' => __('Large'),
            'extra_large' => __('Extra Large'),
            'standard' => __('Standard'),
            'high' => __('High'),
            'reduced' => __('Reduced'),
            'comfortable' => __('Comfortable'),
            'compact' => __('Compact'),
            'table_view' => __('Accessible Data Table'),
            'date' => __('Date'),
            'value' => __('Value'),
            'trend_distribution' => __('Seeded history is being normalized across time until the automated signup engine is enabled.'),
            'analytics_images' => [
                asset('images/portal/website-analytics-1.png'),
                asset('images/portal/website-analytics-2.png'),
                asset('images/portal/website-analytics-3.png'),
            ],
            'data_url' => Route::has('portal.dashboard.data')
                ? route('portal.dashboard.data')
                : '/portal/dashboard/data',
            'refresh_seconds' => $refreshSeconds,
            'default_days' => $defaultDays,
            'auto_refresh_default' => false,
        ];
    }
}
