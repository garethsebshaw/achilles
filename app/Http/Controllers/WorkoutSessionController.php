<?php

namespace App\Http\Controllers;

use App\Models\WorkoutSession;
use App\Models\SystemModule;
use App\Models\SystemStatus;
use App\Models\SystemLocation;
use App\Models\Workout;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;

class WorkoutSessionController extends Controller
{
    private function sessionStatusId(string $code, array $fallbackCodes = []): int
    {
        $statusId = SystemStatus::idForModel(WorkoutSession::class, $code, $fallbackCodes);

        abort_if($statusId === null, 500, __('Workout session status is not configured.'));

        return $statusId;
    }

    private function novaPath(string $suffix = ''): string
    {
        $novaBasePath = trim(Nova::path(), '/');
        $prefix = $novaBasePath === '' ? '' : '/'.$novaBasePath;

        return $prefix.'/resources/workout-sessions'.$suffix;
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
            'workout_id' => 'nullable|exists:workouts,id',
            'location_id' => 'required|exists:system_locations,id',
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'max_athletes' => 'nullable|integer|min:0',
            'max_guides' => 'nullable|integer|min:0',
            'status_id' => 'required|exists:system_statuses,id',
            'notes' => 'nullable|string',
        ]);

        $session = WorkoutSession::create($validated);

        return redirect()->route('workout-sessions.show', $session)
            ->with('success', __('Workout Session created successfully'));
    }

    public function show(WorkoutSession $workout_session)
    {
        return redirect($this->novaPath('/'.$workout_session->getKey()));
    }

    public function edit(WorkoutSession $workout_session)
    {
        return redirect($this->novaPath('/'.$workout_session->getKey().'/edit'));
    }

    public function update(Request $request, WorkoutSession $workout_session)
    {
        $workout_session->update($request->validate([
            'workout_id' => 'nullable|exists:workouts,id',
            'location_id' => 'required|exists:system_locations,id',
            'session_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'max_athletes' => 'nullable|integer|min:0',
            'max_guides' => 'nullable|integer|min:0',
            'status_id' => 'required|exists:system_statuses,id',
            'notes' => 'nullable|string',
        ]));

        return redirect()->route('workout-sessions.show', $workout_session)
            ->with('success', __('Workout Session updated successfully'));
    }

    public function destroy(WorkoutSession $workout_session)
    {
        $workout_session->delete();

        return redirect()->route('workout-sessions.index')
            ->with('success', __('Workout Session deleted successfully'));
    }

    public function cancel(WorkoutSession $workout_session)
    {
        $this->authorize('cancel', $workout_session);

        $workout_session->update([
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
            'status_id' => $this->sessionStatusId('session_cancelled'),
        ]);

        return redirect()->route('workout-sessions.show', $workout_session)
            ->with('success', __('Workout Session cancelled'));
    }
}
