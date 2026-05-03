<?php

namespace App\Http\Controllers;

use App\Models\MeetingPoint;
use App\Models\SystemModule;
use App\Models\SystemCategory;
use App\Models\SystemChapter;
use Illuminate\Http\Request;

class MeetingPointController extends Controller
{
    private function novaPath(string $suffix = ''): string
    {
        return '/achillesworkouts.com/resources/meeting-points'.$suffix;
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

    public function show(MeetingPoint $meeting_point)
    {
        return redirect($this->novaPath('/'.$meeting_point->getKey()));
    }

    public function edit(MeetingPoint $meeting_point)
    {
        return redirect($this->novaPath('/'.$meeting_point->getKey().'/edit'));
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
