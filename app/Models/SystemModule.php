<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemModule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'model_type',
        'description',
        'active',
        'metadata'
    ];

    protected $casts = [
        'active' => 'boolean',
        'metadata' => 'json'
    ];

    public function statuses()
    {
        return $this->hasMany(SystemStatus::class, 'system_module_id');
    }

    public function taggables()
    {
        return $this->hasMany(SystemTaggable::class, 'system_module_id');
    }


    public function categories()
    {
        return $this->hasMany(SystemCategory::class, 'system_module_id');
    }

    public function systemCategories()
    {
        return $this->hasMany(SystemCategory::class, 'system_module_id');
    }

    public function systemStatuses()
    {
        return $this->hasMany(SystemStatus::class, 'system_module_id');
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey() :string
    {
        return 'system-modules';
    }
}
