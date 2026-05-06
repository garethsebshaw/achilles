<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\Workout;
use App\Models\SystemStatus;

class WorkoutFilter extends Filter
{
    public $component = 'select-filter';

    public function name()
    {
        return __('Workout');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('workout_id', $value);
    }

    public function options(NovaRequest $request)
    {
        return Workout::pluck('name', 'id');
    }
}



class SessionDateFilter extends Filter
{
    public $component = 'date-filter';

    public function name()
    {
        return __('Session Date');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->whereDate('session_date', $value);
    }

    public function options(NovaRequest $request)
    {
        return [];
    }
}

class StatusFilter extends Filter
{
    public $component = 'select-filter';

    public function name()
    {
        return __('Status');
    }

    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('status_id', $value);
    }

    public function options(NovaRequest $request)
    {
        $workoutsModuleId = \App\Models\SystemModule::where('model_type', 'App\Models\Workout')->first()->id;
        return SystemStatus::where('system_module_id', $workoutsModuleId)
            ->pluck('name', 'id');
    }
}
