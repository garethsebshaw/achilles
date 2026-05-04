<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceRequest extends Model //implements HasMedia
{
    //use SoftDeletes, InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'equipment_id',
        'component_id',
        'reported_by_id',
        'assigned_to_id',
        'status_id',
        'priority_id',
        'description',
        'reported_at',
        'assigned_at',
        'estimated_time',
        'actual_time',
        'estimated_cost',
        'actual_cost',
        'completed_at',
        'parent_request_id', // For tracking duplicate/related requests
        'notes',
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function component()
    {
        return $this->belongsTo(EquipmentComponent::class);
    }

    public function reportedBy()
    {
        return $this->belongsTo(User::class, 'reported_by_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function status()
    {
        return $this->belongsTo(SystemStatus::class, 'status_id')
            ->whereHas('module', function ($query) {
                $query->where('model_type', self::class);
            });
    }

    public function priority()
    {
        return $this->belongsTo(EquipmentMaintenancePriority::class, 'priority_id');
    }

    public function parentRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class, 'parent_request_id');
    }

    public function childRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'parent_request_id');
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class);
    }
}
