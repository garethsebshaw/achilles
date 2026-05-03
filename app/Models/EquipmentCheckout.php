<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentCheckout extends Model //implements HasMedia
{
    //use SoftDeletes, InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'equipment_id',
        'user_id',
        'event_id', // Optional - if checked out for an event
        'checked_out_at',
        'expected_return_at',
        'returned_at',
        'distance_traveled',
        'condition_out_id',
        'condition_in_id',
        'notes',
    ];

    protected $casts = [
        'checked_out_at' => 'datetime',
        'expected_return_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function conditionOut()
    {
        return $this->belongsTo(EquipmentCondition::class, 'condition_out_id');
    }

    public function conditionIn()
    {
        return $this->belongsTo(EquipmentCondition::class, 'condition_in_id');
    }
}
