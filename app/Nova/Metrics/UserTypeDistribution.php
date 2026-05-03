<?php

namespace App\Nova\Metrics;

use App\Models\User;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\DB;

class UserTypeDistribution extends Partition
{
    public function calculate(NovaRequest $request)
    {
        $results = User::query()
            ->select(DB::raw("
                CASE
                    WHEN is_sys_admin = 1 THEN 'sys_admin'
                    WHEN is_admin = 1 THEN 'admin'
                    WHEN is_team_leader = 1 THEN 'team_leader'
                    WHEN is_guide = 1 THEN 'guide'
                    WHEN is_athlete = 1 THEN 'athlete'
                    ELSE 'other'
                END as user_type,
                COUNT(*) as count
            "))
            ->groupBy('user_type')
            ->orderByRaw("
                CASE user_type
                    WHEN 'sys_admin' THEN 1
                    WHEN 'admin' THEN 2
                    WHEN 'team_leader' THEN 3
                    WHEN 'guide' THEN 4
                    WHEN 'athlete' THEN 5
                    ELSE 6
                END
            ")
            ->get()
            ->pluck('count', 'user_type')
            ->toArray();

        return $this->result($results)
            ->colors([
                'sys_admin' => '#F56565',    // Red
                'admin' => '#ED8936',        // Orange
                'team_leader' => '#ECC94B',  // Yellow
                'guide' => '#68D391',        // Green
                'athlete' => '#4299E1',      // Blue
                'other' => '#A0AEC0'         // Gray
            ])
            ->label(function($value) {
                $labels = [
                    'sys_admin' => 'System Admins',
                    'admin' => 'Admins',
                    'team_leader' => 'Team Leaders',
                    'guide' => 'Guides',
                    'athlete' => 'Athletes',
                    'other' => 'Other'
                ];
                return $labels[$value] ?? $value;
            });
    }

    public function name()
    {
        return 'User Distribution';
    }

    public function uriKey()
    {
        return 'user-type-distribution';
    }

    public $width = '1/2';

    /**
     * Determine the amount of time the results should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        return now()->addMinutes(5);
    }
}
