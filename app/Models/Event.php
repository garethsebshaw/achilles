<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model //implements HasMedia
{
    //use SoftDeletes, InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'location_id',
        'start_date',
        'end_date',
        'status_id',
        'created_by_id',
        'is_active'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function location()
    {
        return $this->belongsTo(SystemLocation::class, 'location_id');
    }

    public function status()
    {
        return $this->belongsTo(SystemStatus::class, 'status_id')
            ->whereHas('module', function ($query) {
                $query->where('model_type', self::class);
            });
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function equipmentCheckouts()
    {
        return $this->hasMany(EquipmentCheckout::class, 'event_id');
    }
}
