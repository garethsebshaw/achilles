<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceLog extends Model //implements HasMedia
{
    //use SoftDeletes, InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'maintenance_request_id',
        'equipment_id',
        'component_id',
        'performed_by_id',
        'work_type', // service, repair, inspection
        'description',
        'performed_at',
        'time_spent',
        'cost',
        'parts_used', // JSON field for tracking parts
        'notes',
    ];

    protected $casts = [
        'performed_at' => 'datetime',
        'parts_used' => 'array',
    ];

    public function maintenanceRequest()
    {
        return $this->belongsTo(MaintenanceRequest::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function component()
    {
        return $this->belongsTo(EquipmentComponent::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by_id');
    }
}
