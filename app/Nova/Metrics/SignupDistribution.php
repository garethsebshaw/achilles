<?php

namespace App\Nova\Metrics;

use App\Models\WorkoutSignup;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\DB;

class SignupDistribution extends Partition
{
    public function calculate(NovaRequest $request)
    {
        return $this->count($request, WorkoutSignup::class, 'role')
            ->groupBy('role', function($role) {
                return DB::table('workout_signups')
                    ->join('users', 'workout_signups.user_id', '=', 'users.id')
                    ->select(DB::raw('CASE WHEN users.is_athlete THEN "Athletes" ELSE "Guides" END as role'))
                    ->count();
            });
    }
}
