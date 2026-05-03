<?php

namespace App\Http\Controllers;

use App\Models\WorkoutSignup;
use App\Models\WorkoutSession;
use App\Models\SystemModule;
use App\Models\SystemStatus;
use App\Models\SystemCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Nova;

class WorkoutSignupController extends Controller
{
    private function signupRules(): array
    {
        return [
            'workout_session_id' => 'required|exists:workout_sessions,id',
            'user_id' => 'nullable|exists:users,id',
            'preferences' => 'nullable|array',
            'equipment_requirements' => 'nullable|array',
            'status_id' => 'nullable|exists:system_statuses,id',
            'athlete_id' => 'nullable|exists:users,id',
            'specific_details' => 'nullable|array',
            'equipment' => 'nullable|array',
        ];
    }

    private function novaPath(string $suffix = ''): string
    {
        $novaBasePath = trim(Nova::path(), '/');
        $prefix = $novaBasePath === '' ? '' : '/'.$novaBasePath;

        return $prefix.'/resources/workout-signups'.$suffix;
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
        $validated = $request->validate($this->signupRules());
        $workoutSession = WorkoutSession::findOrFail($validated['workout_session_id']);

        DB::beginTransaction();
        try {
            // Create signup
            $signup = WorkoutSignup::create([
                'workout_session_id' => $workoutSession->id,
                'user_id' => $validated['user_id'] ?? auth()->id(),
                'athlete_id' => $validated['athlete_id'] ?? null,
                'preferences' => $validated['preferences'] ?? null,
                'equipment_requirements' => $validated['equipment_requirements'] ?? null,
                'status_id' => $validated['status_id'] ?? $this->defaultSignupStatusId(),
            ]);

            // Handle specific details
            if (array_key_exists('specific_details', $validated) && is_array($validated['specific_details'])) {
                $signup->assignSpecificDetails($validated['specific_details']);
            }

            // Handle equipment assignments
            if (array_key_exists('equipment', $validated) && is_array($validated['equipment'])) {
                $signup->assignEquipment($validated['equipment']);
            }

            DB::commit();

            return redirect()->route('workout-signups.show', $signup)
                ->with('success', 'Signup created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Failed to create signup: ' . $e->getMessage()]);
        }
    }

    public function show(WorkoutSignup $workout_signup)
    {
        return redirect($this->novaPath('/'.$workout_signup->getKey()));
    }

    public function edit(WorkoutSignup $workout_signup)
    {
        return redirect($this->novaPath('/'.$workout_signup->getKey().'/edit'));
    }

    public function update(Request $request, WorkoutSignup $workout_signup)
    {
        $validated = $request->validate($this->signupRules());

        DB::beginTransaction();
        try {
            // Update signup status or preferences
            $workout_signup->update([
                'workout_session_id' => $validated['workout_session_id'] ?? $workout_signup->workout_session_id,
                'user_id' => $validated['user_id'] ?? $workout_signup->user_id,
                'athlete_id' => $validated['athlete_id'] ?? $workout_signup->athlete_id,
                'preferences' => $validated['preferences'] ?? $workout_signup->preferences,
                'equipment_requirements' => $validated['equipment_requirements'] ?? $workout_signup->equipment_requirements,
                'status_id' => $validated['status_id'] ?? $workout_signup->status_id,
            ]);

            // Update specific details if provided
            if (array_key_exists('specific_details', $validated) && is_array($validated['specific_details'])) {
                $workout_signup->specificDetails()->updateOrCreate(
                    ['workout_signup_id' => $workout_signup->id],
                    $validated['specific_details']
                );
            }

            // Update equipment assignments
            if (array_key_exists('equipment', $validated) && is_array($validated['equipment'])) {
                // Remove existing assignments
                $workout_signup->equipmentAssignments()->delete();

                // Create new assignments
                $workout_signup->assignEquipment($validated['equipment']);
            }

            DB::commit();

            return redirect()->route('workout-signups.show', $workout_signup)
                ->with('success', 'Signup updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Failed to update signup: ' . $e->getMessage()]);
        }
    }

    public function destroy(WorkoutSignup $workout_signup)
    {
        $workout_signup->delete();

        return redirect()->route('workout-signups.index')
            ->with('success', 'Signup cancelled successfully');
    }

    private function defaultSignupStatusId(): ?int
    {
        $moduleId = SystemModule::where('model_type', WorkoutSignup::class)->value('id');

        return SystemStatus::query()
            ->where('system_module_id', $moduleId)
            ->where(function ($query) {
                $query->where('is_default', true)
                    ->orWhere('code', 'signup_pending')
                    ->orWhere('code', 'pending');
            })
            ->orderByDesc('is_default')
            ->value('id');
    }
}
