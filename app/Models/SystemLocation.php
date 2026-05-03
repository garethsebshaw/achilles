<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SystemLocation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country_id',
        'region_id',
        'chapter_id',
        'phone',
        'email',
        'contact_name',
        'timezone',
        'latitude',
        'longitude',
        'is_active',
        'notes',
        'metadata',
        'access_code',
        'access_instructions'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'metadata' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    /**
     * Get the country that owns the location.
     */
    public function country(): BelongsTo
    {
        return $this->belongsTo(SystemCountry::class, 'country_id');
    }

    /**
     * Get the region that owns the location.
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(SystemRegion::class, 'region_id');
    }

    /**
     * Get the chapter that owns the location.
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(SystemChapter::class, 'chapter_id');
    }

    /**
     * Get the equipment for the location.
     */
    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'location_id');
    }

    /**
     * Get the storage locations for the location.
     */
    public function storageLocations(): HasMany
    {
        return $this->hasMany(StorageLocation::class, 'location_id');
    }

    /**
     * Get the storage locations for the location.
     */
    public function location(): HasMany
    {
        return $this->hasMany(StorageLocation::class, 'location_id');
    }

    /**
     * Get the events for the location.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'location_id');
    }

    /**
     * Get the users who have access to this location.
     */
    public function userAccess(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'system_location_access')
            ->withPivot([
                'access_type',
                'access_identifier',
                'access_granted_date',
                'access_expiry_date',
                'granted_by_id',
                'is_active',
                'notes'
            ])
            ->withTimestamps();
    }

    /**
     * Get the full address as a string.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->postal_code,
            optional($this->country)->name
        ]);

        return implode(', ', $parts);
    }

    /**
     * Get the coordinates as an array.
     */
    public function getCoordinatesAttribute(): array
    {
        return [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude
        ];
    }

    /**
     * Scope a query to only include active locations.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include locations in a specific chapter.
     */
    public function scopeInChapter($query, $chapterId)
    {
        return $query->where('chapter_id', $chapterId);
    }

    /**
     * Scope a query to only include locations in a specific region.
     */
    public function scopeInRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }

    /**
     * Scope a query to only include locations in a specific country.
     */
    public function scopeInCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }

    /**
     * Check if a user has access to this location.
     */
    public function hasUserAccess($userId): bool
    {
        return $this->userAccess()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('access_expiry_date')
                    ->orWhere('access_expiry_date', '>=', now());
            })
            ->exists();
    }

    /**
     * Get the active access records for this location.
     */
    public function activeAccess(): BelongsToMany
    {
        return $this->userAccess()
            ->where('system_location_access.is_active', true)
            ->where(function ($query) {
                $query->whereNull('system_location_access.access_expiry_date')
                    ->orWhere('system_location_access.access_expiry_date', '>=', now());
            });
    }
}
