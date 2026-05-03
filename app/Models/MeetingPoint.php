<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingPoint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'type_id',
        'created_by',
        'chapter_id',
        'metadata'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'metadata' => 'json'
    ];

    // Relationships
    public function type()
    {
        return $this->belongsTo(SystemCategory::class, 'type_id')
            ->where('system_module_id', function($query) {
                $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;
                $query->select('id')->from('system_modules')->where('id', $workoutsModuleId);
            });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function chapter()
    {
        return $this->belongsTo(SystemChapter::class);
    }

    public function workoutTemplates()
    {
        return $this->belongsToMany(Workout::class, 'workout_template_meeting_points');
    }

    public function workoutSessions()
    {
        return $this->belongsToMany(WorkoutSession::class, 'workout_session_meeting_points');
    }

    // Scopes
    public function scopeByChapter($query, $chapterId)
    {
        return $query->where('chapter_id', $chapterId);
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('type_id', $typeId);
    }

    // Accessor for full address
    public function getFullAddressAttribute()
    {
        return $this->address;
    }

    // Mutator for coordinates
    public function setCoordinatesAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['latitude'] = $value['latitude'] ?? null;
            $this->attributes['longitude'] = $value['longitude'] ?? null;
        }
    }
}
