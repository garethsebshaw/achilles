<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkoutSignup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'workout_session_id',
        'user_id',
        'preferences',
        'equipment_requirements',
        'status_id'
    ];

    protected $casts = [
        'preferences' => 'json',
        'equipment_requirements' => 'json'
    ];

    // Relationships
    public function workoutSession()
    {
        return $this->belongsTo(WorkoutSession::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function athleteUser()
    {
        return $this->belongsTo(User::class, 'athlete_id')->withTrashed();
    }

/*    public function status()
    {
        return $this->belongsTo(SystemStatus::class)
            ->where('system_module_id', function($query) {
                $workoutsModuleId = SystemModule::where('name', 'Workout Sessions')->first()->id;
                $query->select('id')->from('system_modules')->where('id', $workoutsModuleId)->first()->id;
            });
    }*/

    public function status()
    {
        return $this->belongsTo(SystemStatus::class)
            ->whereHas('module', function ($query) {
                $query->where('model_type', self::class);
            });
    }

    public function specificDetails()
    {
        return $this->hasOne(WorkoutSpecificDetails::class);
    }

    public function equipmentAssignments()
    {
        return $this->hasMany(WorkoutEquipmentAssignment::class);
    }

    // Scopes
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByStatus($query, $statusId)
    {
        return $query->where('status_id', $statusId);
    }

    // Methods
    public function assignSpecificDetails(array $details)
    {
        return $this->specificDetails()->updateOrCreate(
            ['workout_signup_id' => $this->id],
            $details
        );
    }

    public function assignEquipment(array $equipmentData)
    {
        $assignments = [];
        foreach ($equipmentData as $equipment) {
            $assignments[] = $this->equipmentAssignments()->create($equipment);
        }
        return $assignments;
    }
}
