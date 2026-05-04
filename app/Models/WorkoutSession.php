<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkoutSession extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'workout_id',
        'workout_version',
        'location_id',
        'session_date',
        'start_time',
        'end_time',
        'max_athletes',
        'max_guides',
        'status_id',
        'weather_conditions',
        'cancellation_reason',
        'cancelled_by',
        'cancelled_at',
        'notes',
        'metadata'
    ];

    protected $casts = [
        'session_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'weather_conditions' => 'json',
        'cancelled_at' => 'datetime',
        'metadata' => 'json'
    ];

    // Relationships
    public function workout()
    {
        return $this->belongsTo(Workout::class, 'workout_id');
    }

    public function location()
    {
        return $this->belongsTo(SystemLocation::class, 'location_id');
    }


    public function status()
    {
        return $this->belongsTo(SystemStatus::class)
            ->whereHas('module', function ($query) {
                $query->where('model_type', self::class);
            });
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function signups()
    {
        return $this->hasMany(WorkoutSignup::class, 'workout_session_id');
    }

    public function meetingPoints()
    {
        return $this->belongsToMany(MeetingPoint::class, 'workout_session_meeting_points');
    }

    // Scopes
    public function scopeUpcoming($query)
    {
        return $query->where('session_date', '>=', now()->toDateString());
    }

    public function scopePast($query)
    {
        return $query->where('session_date', '<', now()->toDateString());
    }

    public function scopeCancelled($query)
    {
        return $query->whereNotNull('cancelled_at');
    }
}
