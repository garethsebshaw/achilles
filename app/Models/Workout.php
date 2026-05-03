<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workout extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'location_id',
        'activity_type_id',
        'version',
        'name',
        'description',
        'default_start_time',
        'default_end_time',
        'is_recurring',
        'recurrence_pattern',
        'advance_create_weeks',
        'default_max_athletes',
        'default_max_guides',
        'is_template',
        'is_current_version',
        'metadata',
        'created_by'
    ];

    protected $casts = [
        'is_recurring' => 'boolean',
        'is_template' => 'boolean',
        'is_current_version' => 'boolean',
        'metadata' => 'json',
        'default_start_time' => 'datetime:H:i',
        'default_end_time' => 'datetime:H:i'
    ];

    // Relationships
    public function location()
    {
        return $this->belongsTo(SystemLocation::class, 'location_id');
    }

    public function activityType()
    {
        return $this->belongsTo(SystemCategory::class, 'activity_type_id')
            ->where('system_module_id', function($query) {
                $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;
                $query->select('id')->from('system_modules')->where('id', $workoutsModuleId);
            });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sessions()
    {
        return $this->hasMany(WorkoutSession::class);
    }

    public function meetingPoints()
    {
        return $this->belongsToMany(MeetingPoint::class, 'workout_template_meeting_points');
    }

    // Scopes
    public function scopeCurrentVersion($query)
    {
        return $query->where('is_current_version', true);
    }

    public function scopeTemplates($query)
    {
        return $query->where('is_template', true);
    }

    public function categories()
    {
        return $this->hasMany(SystemCategory::class, 'system_module_id')
            ->whereHas('systemModule', function($query) {
                $query->where('model_type', self::class);
            });
    }

    public function statuses()
    {
        return $this->hasMany(SystemStatus::class, 'system_module_id')
            ->whereHas('systemModule', function($query) {
                $query->where('model_type', self::class);
            });
    }
}
