<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Models\SystemModule;
use App\Models\SystemCategory;
use App\Models\SystemLocation;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;

class WorkoutController extends Controller
{
    private function novaPath(string $suffix = ''): string
    {
        $novaBasePath = trim(Nova::path(), '/');
        $prefix = $novaBasePath === '' ? '' : '/'.$novaBasePath;

        return $prefix.'/resources/workouts'.$suffix;
    }

    public function index(Request $request)
    {
        return redirect($this->novaPath());
    }

    public function create()
    {
        return redirect($this->novaPath('/new'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'location_id' => 'required|exists:system_locations,id',
            'activity_type_id' => 'required|exists:system_categories,id',
            'default_start_time' => 'required|date_format:H:i',
            'default_end_time' => 'required|date_format:H:i|after:default_start_time',
            'is_recurring' => 'boolean',
            'recurrence_pattern' => 'nullable|string',
            'advance_create_weeks' => 'integer|min:1|max:52',
            'default_max_athletes' => 'nullable|integer|min:0',
            'default_max_guides' => 'nullable|integer|min:0',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_template'] = false;

        $workout = Workout::create($validated);

        return redirect()->route('workouts.show', $workout)
            ->with('success', 'Workout created successfully');
    }

    public function show(Workout $workout)
    {
        return redirect($this->novaPath('/'.$workout->getKey()));
    }

    public function edit(Workout $workout)
    {
        return redirect($this->novaPath('/'.$workout->getKey().'/edit'));
    }

    public function update(Request $request, Workout $workout)
    {
        $validated = $request->validate([
            // Same validation as store method
        ]);

        // Create a new version if significant changes
        if ($this->workoutNeedsNewVersion($workout, $validated)) {
            $workout->update(['is_current_version' => false]);
            $validated['version'] = $workout->version + 1;
            $validated['is_current_version'] = true;
            $workout = Workout::create($validated);
        } else {
            $workout->update($validated);
        }

        return redirect()->route('workouts.show', $workout)
            ->with('success', 'Workout updated successfully');
    }

    protected function workoutNeedsNewVersion(Workout $oldWorkout, array $newData)
    {
        // Define logic for determining if a new version is needed
        $criticalFields = [
            'activity_type_id',
            'default_start_time',
            'default_end_time',
            'is_recurring',
            'recurrence_pattern'
        ];

        foreach ($criticalFields as $field) {
            if (isset($newData[$field]) && $oldWorkout->{$field} != $newData[$field]) {
                return true;
            }
        }

        return false;
    }

    public function destroy(Workout $workout)
    {
        // Soft delete or mark as inactive
        $workout->delete();

        return redirect()->route('workouts.index')
            ->with('success', 'Workout archived successfully');
    }
}
