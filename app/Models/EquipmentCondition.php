<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// First model: EquipmentCondition
class EquipmentCondition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'rating',
        'serviceable'
    ];

    protected $casts = [
        'rating' => 'integer',
        'serviceable' => 'boolean'
    ];

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'condition_id');
    }

    public function components(): HasMany
    {
        return $this->hasMany(EquipmentComponent::class, 'condition_id');
    }

    public function checkoutsOut(): HasMany
    {
        return $this->hasMany(EquipmentCheckout::class, 'condition_out_id');
    }

    public function checkoutsIn(): HasMany
    {
        return $this->hasMany(EquipmentCheckout::class, 'condition_in_id');
    }

    public function getIsServiceableAttribute(): bool
    {
        return $this->serviceable;
    }

    public function scopeServiceable($query)
    {
        return $query->where('serviceable', true);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }
}
