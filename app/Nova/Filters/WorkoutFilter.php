<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\Workout;
use App\Models\SystemLocation;
use App\Models\SystemStatus;
use Illuminate\Support\Carbon;

class WorkoutFilter extends Filter
{
    public function apply(NovaRequest $request, $query, $value)
    {
        return $query->where('workout_id', $value);
    }

    public function options(NovaRequest $request)
    {
        return Workout::pluck('name', 'id');
    }
}

class LocationFilter extends Filter
{
    /**
     * The filter's component.
     */
    public $component = 'select-filter';

    /**
     * Apply the filter to the given query.
     */
    public function apply(Request $request, $query, $value)
    {
        return $query->where('location_id', $value);
    }

    /**
     * Get the filter's options.
     */
    public function options(Request $request)
    {
        // Get the current applied Chapter filter value
        $chapterFilterValue = $request->get('filters', [])['chapter_filter'] ?? null;

        // Fetch locations, filtering by Chapter if one is selected
        $locationsQuery = SystemLocation::orderBy('name', 'asc');

        if ($chapterFilterValue) {
            $locationsQuery->where('chapter_id', $chapterFilterValue);
        }

        return $locationsQuery->pluck('id', 'name')->toArray();
    }

    /**
     * Get the name for the filter.
     */
    public function name()
    {
//        return 'Location (Filtered by Chapter)';
        return 'Location';
    }
}



class SessionDateFilter extends Filter
{
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
