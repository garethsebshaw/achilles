<?php

namespace App\Support\Attendance;

use App\Models\User;
use App\Models\WeatherData;
use App\Models\WorkoutSession;
use App\Services\Weather\WeatherService;
use Carbon\Carbon;

class CheckInSessionContext
{
    private const SESSION_KEY = 'attendance.active_check_in_session';
    private const EXPIRY_BUFFER_HOURS = 3;
    private const WEATHER_STALE_MINUTES = 30;

    public function __construct(private readonly WeatherService $weatherService)
    {
    }

    public function currentContext(): ?array
    {
        $context = session()->get(self::SESSION_KEY);

        if (! is_array($context) || empty($context['session_id']) || empty($context['expires_at'])) {
            $this->deactivate();

            return null;
        }

        $expiresAt = Carbon::parse($context['expires_at']);

        if ($expiresAt->isPast()) {
            $this->deactivate();

            return null;
        }

        return $context;
    }

    public function currentSession(): ?WorkoutSession
    {
        $context = $this->currentContext();

        if (! $context) {
            return null;
        }

        return WorkoutSession::query()
            ->with(['location.chapter.country', 'workout.activityType'])
            ->find($context['session_id']);
    }

    public function currentSessionId(): ?int
    {
        return $this->currentContext()['session_id'] ?? null;
    }

    public function activate(User $user, WorkoutSession $session): void
    {
        session()->put(self::SESSION_KEY, [
            'session_id' => $session->id,
            'location_id' => $session->location_id,
            'chapter_id' => $session->location?->chapter_id,
            'expires_at' => $this->expiresAt($session)->toIso8601String(),
            'activated_by_user_id' => $user->id,
            'activated_at' => now()->toIso8601String(),
        ]);

        $this->refreshWeatherIfStale($session);
    }

    public function deactivate(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function isActiveForSession(?WorkoutSession $session): bool
    {
        if (! $session) {
            return false;
        }

        return $this->currentSessionId() === (int) $session->id;
    }

    public function canManageSession(User $user, WorkoutSession $session): bool
    {
        if ($user->isSysAdmin() || $user->isAdmin()) {
            return true;
        }

        $chapterId = $session->location?->chapter_id;

        if (! $chapterId) {
            return false;
        }

        return $user->hasPrivilegedRole() && $user->hasActiveChapterAccess((int) $chapterId);
    }

    public function refreshWeatherIfStale(WorkoutSession $session, bool $force = false): void
    {
        $location = $session->location;

        if (! $location || ! $location->latitude || ! $location->longitude) {
            return;
        }

        $latestGeneratedAt = WeatherData::query()
            ->where('location_id', $location->id)
            ->max('generated_at');

        if (! $force && $latestGeneratedAt) {
            $generatedAt = Carbon::parse($latestGeneratedAt);

            if ($generatedAt->greaterThan(now()->subMinutes(self::WEATHER_STALE_MINUTES))) {
                return;
            }
        }

        $this->weatherService->fetchWeatherDataForLocation($location);
    }

    public function isSessionWithinCheckInWindow(WorkoutSession $session): bool
    {
        if (! $session->session_date || ! $session->start_time) {
            return false;
        }

        $sessionDateTime = $session->session_date->copy()->setTimeFrom($session->start_time);
        $hoursUntilSession = now()->diffInHours($sessionDateTime, false);

        return abs($hoursUntilSession) <= 12;
    }

    public function expiresAt(WorkoutSession $session): Carbon
    {
        $baseDate = $session->session_date?->copy() ?? now();

        if ($session->end_time) {
            $baseDate = $baseDate->setTimeFrom($session->end_time);
        }

        return $baseDate->copy()->addHours(self::EXPIRY_BUFFER_HOURS);
    }
}
