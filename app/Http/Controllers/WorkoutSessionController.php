<?php

namespace App\Http\Controllers;

use App\Models\WorkoutSession;
use App\Models\SystemModule;
use App\Models\SystemStatus;
use Illuminate\Http\Request;

class WorkoutSessionController extends Controller
{
    public function index(Request $request)
    {
        $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;

        $query = WorkoutSession::with(['workout', 'location', 'status'])
            ->whereHas('status', function($q) use ($workoutsModuleId) {
                $q->where('system_module_id', $workoutsModuleId);
            });

        // Filter options
        if ($request->has('status')) {
            $query->whereHas('status', function($q) use ($request) {
                $q->where('id', $request->status);
            });
        }

        if ($request->has('date_range')) {
            // Handle date range filtering
            switch($request->date_range) {
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'past':
                    $query->past();
                    break;
            }
        }

        $sessions = $query->paginate(15);

        return view('workout-sessions.index', compact('sessions'));
    }

    public function create()
    {
        $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;

        $statuses = SystemStatus::where('system_module_id', $workoutsModuleId)->get();
        $workouts = Workout::all();
        $locations = SystemLocation::all();

        return view('workout-sessions.create', compact('statuses', 'workouts', 'locations'));
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
            ->with('success', 'Workout Session created successfully');
    }

    public function show(WorkoutSession $session)
    {
        $session->load(['workout', 'location', 'status', 'signups', 'meetingPoints']);
        return view('workout-sessions.show', compact('session'));
    }

    public function cancel(WorkoutSession $session)
    {
        $this->authorize('cancel', $session);

        $session->update([
            'cancelled_at' => now(),
            'cancelled_by' => auth()->id(),
            'status_id' => SystemStatus::where('code', 'cancelled')->first()->id
        ]);

        return redirect()->route('workout-sessions.show', $session)
            ->with('success', 'Workout Session cancelled');
    }
}
