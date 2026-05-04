<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutEquipmentAssignment extends Model
{
    protected $fillable = [
        'workout_signup_id',
        'equipment_id',
        'assignment_type_id',
        'fitting_details'
    ];

    protected $casts = [
        'fitting_details' => 'json'
    ];

    public function workoutSignup()
    {
        return $this->belongsTo(WorkoutSignup::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function assignmentType()
    {
        return $this->belongsTo(SystemCategory::class, 'assignment_type_id')
            ->whereHas('systemModule', function ($query) {
                $query->where('model_type', self::class);
            });
    }
}
