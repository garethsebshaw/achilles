<?php

namespace App\Http\Controllers;

use App\Models\MeetingPoint;
use App\Models\SystemModule;
use App\Models\SystemCategory;
use App\Models\SystemChapter;
use Illuminate\Http\Request;
use Laravel\Nova\Nova;

class MeetingPointController extends Controller
{
    private function rules(): array
    {
        $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;

        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'type_id' => [
                'required',
                'exists:system_categories,id',
                function ($attribute, $value, $fail) use ($workoutsModuleId) {
                    $type = SystemCategory::find($value);
                    if (! $type || $type->system_module_id !== $workoutsModuleId) {
                        $fail('Invalid meeting point type.');
                    }
                },
            ],
            'chapter_id' => 'nullable|exists:system_chapters,id',
            'metadata' => 'nullable|array',
        ];
    }

    private function novaPath(string $suffix = ''): string
    {
        $novaBasePath = trim(Nova::path(), '/');
        $prefix = $novaBasePath === '' ? '' : '/'.$novaBasePath;

        return $prefix.'/resources/meeting-points'.$suffix;
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
        $validated = $request->validate($this->rules());

        $validated['created_by'] = auth()->id();

        $meetingPoint = MeetingPoint::create($validated);

        return redirect()->route('meeting-points.show', $meetingPoint)
            ->with('success', __('Meeting Point created successfully'));
    }

    public function show(MeetingPoint $meeting_point)
    {
        return redirect($this->novaPath('/'.$meeting_point->getKey()));
    }

    public function edit(MeetingPoint $meeting_point)
    {
        return redirect($this->novaPath('/'.$meeting_point->getKey().'/edit'));
    }

    public function update(Request $request, MeetingPoint $meeting_point)
    {
        $validated = $request->validate($this->rules());

        $meeting_point->update($validated);

        return redirect()->route('meeting-points.show', $meeting_point)
            ->with('success', __('Meeting Point updated successfully'));
    }

    public function destroy(MeetingPoint $meeting_point)
    {
        $meeting_point->delete();

        return redirect()->route('meeting-points.index')
            ->with('success', __('Meeting Point deleted successfully'));
    }
}
