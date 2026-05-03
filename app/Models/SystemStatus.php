<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemStatus extends Model
{
    protected $fillable = [
        'system_module_id',
        'parent_id',
        'name',
        'code',
        'color',
        'sort_order',
        'is_default',
        'is_system',
        'metadata'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_system' => 'boolean',
        'metadata' => 'json',
        'sort_order' => 'integer'
    ];
    public function module(): BelongsTo
    {
        return $this->belongsTo(SystemModule::class, 'system_module_id');
    }

    public function systemModule(): BelongsTo
    {
        return $this->belongsTo(SystemModule::class, 'system_module_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(SystemStatus::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(SystemStatus::class, 'parent_id');
    }

    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $status = $this;

        while ($status->parent) {
            $category = $status->parent;
            array_unshift($path, $status->name);
        }

        return implode(' > ', $path);
    }

}
