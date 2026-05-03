<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WorkoutSession;
use App\Models\WorkoutSignup;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WorkoutSessionAttendanceController extends Controller
{
    public function getSessionUsers(WorkoutSession $session)
    {
        $users = $session->signups()
            ->with(['user', 'status'])
            ->get()
            ->map(function ($signup) {
                $user = $signup->user;
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'picture' => $user->picture,
                    'is_athlete' => $user->is_athlete,
                    'is_guide' => $user->is_guide,
                    'checked_in_at' => $signup->checked_in_at,
                    'checked_out_at' => $signup->checked_out_at,
                    'status' => $signup->status?->name,
                    'athlete_id' => $signup->athlete_id,
                ];
            });

        $metrics = [
            'athletesCheckedIn' => $users->where('is_athlete', true)->whereNotNull('checked_in_at')->count(),
            'guidesCheckedIn' => $users->where('is_guide', true)->whereNotNull('checked_in_at')->count(),
            'athletesCheckedOut' => $users->where('is_athlete', true)->whereNotNull('checked_out_at')->count(),
            'guidesCheckedOut' => $users->where('is_guide', true)->whereNotNull('checked_out_at')->count(),
        ];

        return response()->json([
            'users' => $users,
            'metrics' => $metrics,
        ]);
    }

    public function checkIn(Request $request, WorkoutSession $session)
    {
        $signup = $session->signups()->where('user_id', $request->userId)->firstOrFail();

        if ($signup->checked_in_at) {
            throw ValidationException::withMessages(['user' => 'User is already checked in']);
        }

        $signup->update([
            'checked_in_at' => now(),
            'status_id' => \App\Models\SystemStatus::where('code', 'checked_in')->first()->id
        ]);

        return response()->json(['message' => 'User checked in successfully']);
    }

    public function checkOut(Request $request, WorkoutSession $session)
    {
        $signup = $session->signups()->where('user_id', $request->userId)->firstOrFail();

        if (!$signup->checked_in_at) {
            throw ValidationException::withMessages(['user' => 'User must be checked in first']);
        }

        $signup->update([
            'checked_out_at' => now(),
            'status_id' => \App\Models\SystemStatus::where('code', 'attended')->first()->id
        ]);

        return response()->json(['message' => 'User checked out successfully']);
    }

    public function cancelCheckIn(Request $request, WorkoutSession $session)
    {
        $signup = $session->signups()->where('user_id', $request->userId)->firstOrFail();

        $signup->update([
            'checked_in_at' => null,
            'checked_out_at' => null,
            'status_id' => \App\Models\SystemStatus::where('code', 'signed_up')->first()->id
        ]);

        return response()->json(['message' => 'Check-in cancelled successfully']);
    }

    public function assignGuide(Request $request, WorkoutSession $session)
    {
        $request->validate([
            'athlete_id' => 'required|exists:users,id',
            'guide_id' => 'required|exists:users,id'
        ]);

        $guideSignup = $session->signups()
            ->where('user_id', $request->guide_id)
            ->firstOrFail();

        $athleteSignup = $session->signups()
            ->where('user_id', $request->athlete_id)
            ->firstOrFail();

        // Verify roles
        if (!$guideSignup->user->is_guide) {
            throw ValidationException::withMessages(['guide' => 'Selected user is not a guide']);
        }

        if (!$athleteSignup->user->is_athlete) {
            throw ValidationException::withMessages(['athlete' => 'Selected user is not an athlete']);
        }

        DB::transaction(function () use ($guideSignup, $request) {
            // Remove guide from any other athletes in this session
            WorkoutSignup::where('athlete_id', $guideSignup->user_id)
                ->where('workout_session_id', $guideSignup->workout_session_id)
                ->update(['athlete_id' => null]);

            // Assign guide to athlete
            $guideSignup->update(['athlete_id' => $request->athlete_id]);
        });

        return response()->json(['message' => 'Guide assigned successfully']);
    }

    public function searchUsers(Request $request, WorkoutSession $session)
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $users = User::query()
            ->where('name', 'like', "%{$request->query}%")
            ->orWhere('email', 'like', "%{$request->query}%")
            ->when($request->chapter_id, function ($query, $chapterId) {
                $query->whereHas('chapter', function ($q) use ($chapterId) {
                    $q->where('id', $chapterId);
                });
            })
            ->get();

        return response()->json(['users' => $users]);
    }

    public function addUserToSession(Request $request, WorkoutSession $session)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if user is already signed up
        if ($session->signups()->where('user_id', $request->user_id)->exists()) {
            throw ValidationException::withMessages(['user' => 'User is already signed up for this session']);
        }

        $signup = $session->signups()->create([
            'user_id' => $request->user_id,
            'status_id' => \App\Models\SystemStatus::where('code', 'signed_up')->first()->id,
        ]);

        return response()->json([
            'message' => 'User added successfully',
            'signup' => $signup->load('user', 'status')
        ]);
    }
}
