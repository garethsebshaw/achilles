<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponentType extends Model//implements HasMedia
{
    //use SoftDeletes, InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'system_category_id', // To group similar components
        'default_maintenance_interval_miles',
        'default_maintenance_interval_months',
        'attributes', // JSON field for component-specific attributes
    ];

    protected $casts = [
        'attributes' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(SystemCategory::class, 'system_category_id');
    }

    public function components()
    {
        return $this->hasMany(EquipmentComponent::class, 'component_type_id');
    }

    public function compatibleWith()
    {
        return $this->belongsToMany(ComponentType::class, 'component_compatibility',
            'component_type_id', 'compatible_with_id');
    }
}
