<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StorageLocation extends Model //implements HasMedia
{
    //use SoftDeletes, InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'location_id',
        'name',
        'type', // enum: unit, van, static_van, etc
        'capacity',
        'notes',
    ];

    public function location()
    {
        return $this->belongsTo(SystemLocation::class, 'location_id');
    }

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }
}
