<?php

namespace App\Nova\Filters;

use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\User;
use App\Models\SystemStatus;
use App\Models\WorkoutSession;

class WorkoutSignupUserFilter extends Filter
{
    public $name = 'User';

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('user_id', $value);
    }

    public function options(NovaRequest $request)
    {
        return User::pluck('name', 'id');
    }
}

class WorkoutSignupStatusFilter extends Filter
{
    public $name = 'Status';

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('status_id', $value);
    }

    public function options(NovaRequest $request)
    {
        $workoutsModuleId = \App\Models\SystemModule::where('model_type', 'App\Models\WorkoutSignup')->first()->id;
        return SystemStatus::where('system_module_id', $workoutsModuleId)
            ->pluck('name', 'id');
    }
}

//class WorkoutSessionFilter extends Filter
//{
//    public $name = 'Workout Session';
//
//    public function apply(NovaRequest $request, $query, $value)
//    {
//        return $query->where('workout_session_id', $value);
//    }
//
//    public function options(NovaRequest $request)
//    {
//        return WorkoutSession::with('workout')
//            ->get()
//            ->mapWithKeys(function ($session) {
//                $workoutName = $session->workout ? $session->workout->name : 'No Workout';
//                $displayName = "{$workoutName} - {$session->session_date}";
//                return [$session->id => $displayName];
//            });
//    }
//}

class WorkoutSignupDateFilter extends Filter
{
    public $name = 'Session Date';

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->whereHas('workoutSession', function($q) use ($value) {
            $q->whereDate('session_date', $value);
        });
    }

    public function options(NovaRequest $request)
    {
        return [];
    }
}
