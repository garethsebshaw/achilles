<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSessionMeetingPoint extends Model
{
    protected $fillable = [
        'workout_session_id',
        'meeting_point_id',
        'is_primary'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function workoutSession()
    {
        return $this->belongsTo(WorkoutSession::class);
    }

    public function meetingPoint()
    {
        return $this->belongsTo(MeetingPoint::class);
    }
}
