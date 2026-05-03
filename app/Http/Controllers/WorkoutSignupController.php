<?php

namespace App\Http\Controllers;

use App\Models\WorkoutSignup;
use App\Models\WorkoutSession;
use App\Models\SystemModule;
use App\Models\SystemStatus;
use App\Models\SystemCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkoutSignupController extends Controller
{
    public function index(Request $request)
    {
        $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;

        $query = WorkoutSignup::with([
            'workoutSession',
            'user',
            'status',
            'specificDetails',
            'equipmentAssignments'
        ]);

        // Filter by session
        if ($request->has('workout_session_id')) {
            $query->where('workout_session_id', $request->workout_session_id);
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->forUser($request->user_id);
        }

        // Filter by status
        if ($request->has('status_id')) {
            $query->byStatus($request->status_id);
        }

        $signups = $query->paginate(15);

        $statuses = SystemStatus::where('system_module_id', $workoutsModuleId)->get();
        $sessions = WorkoutSession::all();

        return view('workout-signups.index', compact('signups', 'statuses', 'sessions'));
    }

    public function create(WorkoutSession $workoutSession)
    {
        $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;

        $sportCategories = SystemCategory::where('system_module_id', $workoutsModuleId)->get();
        $statuses = SystemStatus::where('system_module_id', $workoutsModuleId)->get();

        return view('workout-signups.create', compact('workoutSession', 'sportCategories', 'statuses'));
    }

    public function store(Request $request, WorkoutSession $workoutSession)
    {
        $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;

        DB::beginTransaction();
        try {
            // Create signup
            $signup = WorkoutSignup::create([
                'workout_session_id' => $workoutSession->id,
                'user_id' => auth()->id(),
                'preferences' => $request->input('preferences'),
                'status_id' => SystemStatus::where('code', 'pending')->first()->id
            ]);

            // Handle specific details
            if ($request->has('specific_details')) {
                $signup->assignSpecificDetails($request->input('specific_details'));
            }

            // Handle equipment assignments
            if ($request->has('equipment')) {
                $signup->assignEquipment($request->input('equipment'));
            }

            DB::commit();

            return redirect()->route('workout-signups.show', $signup)
                ->with('success', 'Signup created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Failed to create signup: ' . $e->getMessage()]);
        }
    }

    public function show(WorkoutSignup $signup)
    {
        $signup->load([
            'workoutSession',
            'user',
            'status',
            'specificDetails',
            'equipmentAssignments'
        ]);

        return view('workout-signups.show', compact('signup'));
    }

    public function update(Request $request, WorkoutSignup $signup)
    {
        DB::beginTransaction();
        try {
            // Update signup status or preferences
            $signup->update($request->only(['preferences', 'status_id']));

            // Update specific details if provided
            if ($request->has('specific_details')) {
                $signup->specificDetails()->updateOrCreate(
                    ['workout_signup_id' => $signup->id],
                    $request->input('specific_details')
                );
            }

            // Update equipment assignments
            if ($request->has('equipment')) {
                // Remove existing assignments
                $signup->equipmentAssignments()->delete();

                // Create new assignments
                $signup->assignEquipment($request->input('equipment'));
            }

            DB::commit();

            return redirect()->route('workout-signups.show', $signup)
                ->with('success', 'Signup updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Failed to update signup: ' . $e->getMessage()]);
        }
    }

    public function destroy(WorkoutSignup $signup)
    {
        $signup->delete();

        return redirect()->route('workout-signups.index')
            ->with('success', 'Signup cancelled successfully');
    }
}
