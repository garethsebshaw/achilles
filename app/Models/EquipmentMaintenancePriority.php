<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentMaintenancePriority extends Model //implements HasMedia
{
    //use SoftDeletes, InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'level',
        'response_time_hours',
    ];

    protected $casts = [
        'level' => 'integer',
        'response_time_hours' => 'integer',
    ];

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'priority_id');
    }
}
