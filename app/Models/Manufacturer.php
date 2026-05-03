<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manufacturer extends Model //implements HasMedia
{
    //use SoftDeletes, InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contact_info',
        'website',
        'notes',
    ];

    public function equipment()
    {
        return $this->hasMany(Equipment::class);
    }

    public function components()
    {
        return $this->hasMany(EquipmentComponent::class);
    }
}
