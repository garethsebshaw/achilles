<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutSpecificDetails extends Model
{
    protected $fillable = [
        'workout_signup_id',
        'sport_category_id',
        'distance_unit_id',
        'pace_unit_id',
        'speed_unit_id',
        'distance',
        'time',
        'pace_min',
        'pace_max',
        'speed_min',
        'speed_max',
        'additional_details'
    ];

    protected $casts = [
        'additional_details' => 'json'
    ];

    public function workoutSignup()
    {
        return $this->belongsTo(WorkoutSignup::class);
    }

    public function sportCategory()
    {
        return $this->belongsTo(SystemCategory::class, 'sport_category_id')
            ->where('system_module_id', function($query) {
                $workoutsModuleId = SystemModule::where('name', 'Workouts')->first()->id;
                $query->select('id')->from('system_modules')->where('id', $workoutsModuleId);
            });
    }

    public function distanceUnit()
    {
        return $this->belongsTo(SystemStatus::class, 'distance_unit_id')
            ->whereHas('module', function ($query) {
                $query->where('model_type', self::class);
            });
    }

    public function paceUnit()
    {
        return $this->belongsTo(SystemStatus::class, 'pace_unit_id')
            ->whereHas('module', function ($query) {
                $query->where('model_type', self::class);
            });
    }

    public function speedUnit()
    {
        return $this->belongsTo(SystemStatus::class, 'speed_unit_id')
            ->whereHas('module', function ($query) {
                $query->where('model_type', self::class);
            });
    }
}
