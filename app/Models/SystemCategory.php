<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemCategory extends Model
{
    protected $fillable = [
        'system_module_id',
        'name',
        'description',
        'parent_id',
        'active',
        'metadata'
    ];

    protected $casts = [
        'active' => 'boolean',
        'metadata' => 'array'
    ];

    public function systemModule(): BelongsTo
    {
        return $this->belongsTo(SystemModule::class, 'system_module_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SystemCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(SystemCategory::class, 'parent_id');
    }

    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $category = $this;

        while ($category->parent) {
            $category = $category->parent;
            array_unshift($path, $category->name);
        }

        return implode(' > ', $path);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForModule($query, $moduleId)
    {
        return $query->where('system_module_id', $moduleId);
    }

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey() :string
    {
        return 'system-categories';
    }
    public static function authorizedToViewAny() :string
    {
        return true;
    }
    public static function label() :string
    {
        return __('Categories');
    }
}
