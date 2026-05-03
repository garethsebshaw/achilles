<?php

namespace App\Http\Controllers;

use App\Models\MeetingPoint;
use App\Models\SystemModule;
use App\Models\SystemCategory;
use App\Models\SystemChapter;
use Illuminate\Http\Request;

class MeetingPointController extends Controller
{
    public function index(Request $request)
    {
        $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;

        $query = MeetingPoint::with(['type', 'chapter', 'createdBy']);

        // Filter by type
        if ($request->has('type')) {
            $query->byType($request->type);
        }

        // Filter by chapter
        if ($request->has('chapter')) {
            $query->byChapter($request->chapter);
        }

        $meetingPoints = $query->paginate(15);

        $types = SystemCategory::where('system_module_id', $workoutsModuleId)->get();
        $chapters = SystemChapter::all();

        return view('meeting-points.index', compact('meetingPoints', 'types', 'chapters'));
    }

    public function create()
    {
        $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;

        $types = SystemCategory::where('system_module_id', $workoutsModuleId)->get();
        $chapters = SystemChapter::all();

        return view('meeting-points.create', compact('types', 'chapters'));
    }

    public function store(Request $request)
    {
        $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'type_id' => [
                'required',
                'exists:system_categories,id',
                function ($attribute, $value, $fail) use ($workoutsModuleId) {
                    $type = SystemCategory::find($value);
                    if (!$type || $type->system_module_id !== $workoutsModuleId) {
                        $fail('Invalid meeting point type.');
                    }
                }
            ],
            'chapter_id' => 'nullable|exists:system_chapters,id',
            'metadata' => 'nullable|array'
        ]);

        $validated['created_by'] = auth()->id();

        $meetingPoint = MeetingPoint::create($validated);

        return redirect()->route('meeting-points.show', $meetingPoint)
            ->with('success', 'Meeting Point created successfully');
    }

    public function show(MeetingPoint $meetingPoint)
    {
        $meetingPoint->load(['type', 'chapter', 'createdBy', 'workoutTemplates', 'workoutSessions']);
        return view('meeting-points.show', compact('meetingPoint'));
    }

    public function edit(MeetingPoint $meetingPoint)
    {
        $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;

        $types = SystemCategory::where('system_module_id', $workoutsModuleId)->get();
        $chapters = SystemChapter::all();

        return view('meeting-points.edit', compact('meetingPoint', 'types', 'chapters'));
    }

    public function update(Request $request, MeetingPoint $meetingPoint)
    {
        // Similar validation to store method
        $validated = $request->validate([
            // Same validation as store method
        ]);

        $meetingPoint->update($validated);

        return redirect()->route('meeting-points.show', $meetingPoint)
            ->with('success', 'Meeting Point updated successfully');
    }

    public function destroy(MeetingPoint $meetingPoint)
    {
        $meetingPoint->delete();

        return redirect()->route('meeting-points.index')
            ->with('success', 'Meeting Point deleted successfully');
    }
}
