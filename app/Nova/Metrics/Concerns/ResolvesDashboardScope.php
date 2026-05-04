<?php

namespace App\Nova\Metrics\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;

trait ResolvesDashboardScope
{
    protected function dashboardUser(?NovaRequest $request = null): ?User
    {
        $user = $request?->user() ?? auth()->user();

        return $user instanceof User ? $user : null;
    }

    protected function scopedLocationIds(?User $user = null): ?array
    {
        $user ??= $this->dashboardUser();

        if (! $user instanceof User) {
            return null;
        }

        if ($user->isSysAdmin()) {
            return null;
        }

        if (! $user->hasPrivilegedRole()) {
            return null;
        }

        $locationIds = $user->accessibleLocationIds();

        return $locationIds !== [] ? $locationIds : null;
    }

    protected function scopedChapterIds(?User $user = null): ?array
    {
        $user ??= $this->dashboardUser();

        if (! $user instanceof User) {
            return null;
        }

        if ($user->isSysAdmin()) {
            return null;
        }

        if (! $user->hasPrivilegedRole()) {
            return null;
        }

        $chapterIds = $user->accessibleChapterIds();

        return $chapterIds !== [] ? $chapterIds : null;
    }

    protected function applyScopedLocationFilter($query, string $column = 'location_id', ?User $user = null)
    {
        $locationIds = $this->scopedLocationIds($user);

        if ($locationIds !== null) {
            $query->whereIn($column, $locationIds);
        }

        return $query;
    }

    protected function scopeLabel(?User $user = null): string
    {
        $user ??= $this->dashboardUser();

        if (! $user instanceof User || $user->isSysAdmin()) {
            return __('All Chapters');
        }

        return $this->scopedLocationIds($user) !== null
            ? __('Your Accessible Chapters')
            : __('All Chapters');
    }

    protected function activeSignupCodes(): array
    {
        return [
            'signup_pending',
            'signup_confirmed',
            'signup_checked_in',
            'signup_checked_out',
            'signup_attended',
            'signup_waitlisted',
        ];
    }

    protected function attendedSignupCodes(): array
    {
        return [
            'signup_checked_out',
            'signup_attended',
        ];
    }

    protected function missedSignupCodes(): array
    {
        return [
            'signup_no_show',
            'signup_late_cancel',
        ];
    }

    protected function scopedSessionQuery(?User $user = null)
    {
        $query = DB::table('workout_sessions');

        return $this->applyScopedLocationFilter($query, 'workout_sessions.location_id', $user);
    }

    protected function scopedSignupQuery(?User $user = null)
    {
        $query = DB::table('workout_signups')
            ->join('workout_sessions', 'workout_signups.workout_session_id', '=', 'workout_sessions.id')
            ->leftJoin('system_statuses', 'workout_signups.status_id', '=', 'system_statuses.id');

        return $this->applyScopedLocationFilter($query, 'workout_sessions.location_id', $user);
    }

    protected function scopedEventQuery(?User $user = null)
    {
        $query = DB::table('events');

        return $this->applyScopedLocationFilter($query, 'events.location_id', $user);
    }

    protected function scopedMaintenanceQuery(?User $user = null)
    {
        $query = DB::table('maintenance_requests')
            ->join('equipment', 'maintenance_requests.equipment_id', '=', 'equipment.id')
            ->leftJoin('system_statuses', 'maintenance_requests.status_id', '=', 'system_statuses.id');

        return $this->applyScopedLocationFilter($query, 'equipment.location_id', $user);
    }

    protected function personalSignupQuery(User $user)
    {
        return DB::table('workout_signups')
            ->join('workout_sessions', 'workout_signups.workout_session_id', '=', 'workout_sessions.id')
            ->leftJoin('workouts', 'workout_sessions.workout_id', '=', 'workouts.id')
            ->leftJoin('system_categories as sports', 'workouts.activity_type_id', '=', 'sports.id')
            ->leftJoin('system_statuses', 'workout_signups.status_id', '=', 'system_statuses.id')
            ->where('workout_signups.user_id', $user->id);
    }
}
