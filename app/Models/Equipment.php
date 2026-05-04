<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'system_category_id',
        'manufacturer_id',
        'name',
        'model',
        'serial_number',
        'qr_code',
        'location_id',
        'storage_location_id',
        'status_id',
        'condition_id',
        'owner_type',
        'owner_id',
        'assigned_user_id',
        'purchase_date',
        'purchase_price',
        'warranty_expiry',
        'last_maintenance_date',
        'next_maintenance_date',
        'notes',
        'attributes',
        'is_active'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
        'last_maintenance_date' => 'datetime',
        'next_maintenance_date' => 'datetime',
        'attributes' => 'array',
        'is_active' => 'boolean',
        'purchase_price' => 'decimal:2'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(SystemCategory::class, 'system_category_id');
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class, 'manufacturer_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(SystemLocation::class, 'location_id');
    }

    public function storageLocation(): BelongsTo
    {
        return $this->belongsTo(StorageLocation::class, 'storage_location_id');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(SystemStatus::class, 'status_id')
            ->whereHas('module', function ($query) {
                $query->where('model_type', self::class);
            });
    }

    public function equipmentCondition(): BelongsTo  // Changed this relationship name
    {
        return $this->belongsTo(EquipmentCondition::class, 'condition_id');
    }

    public function owner()
    {
        return $this->morphTo();
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function components(): HasMany
    {
        return $this->hasMany(EquipmentComponent::class, 'equipment_id');
    }

    public function maintenanceRequests(): HasMany
    {
        return $this->hasMany(MaintenanceRequest::class, 'equipment_id');
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(MaintenanceLog::class, 'equipment_id');
    }

    public function checkouts(): HasMany
    {
        return $this->hasMany(EquipmentCheckout::class, 'equipment_id');
    }

    public function isUnderWarranty(): bool
    {
        if (!$this->warranty_expiry) {
            return false;
        }
        return $this->warranty_expiry > now();
    }

    public function needsMaintenance(): bool
    {
        if (!$this->next_maintenance_date) {
            return false;
        }
        return $this->next_maintenance_date <= now();
    }

    public function isServiceable(): bool
    {
        return $this->equipmentCondition->serviceable;  // Updated to use new relationship name
    }

    public function isCheckedOut(): bool
    {
        return $this->checkouts()
            ->whereNull('returned_at')
            ->exists();
    }

    public function currentCheckout()
    {
        return $this->checkouts()
            ->whereNull('returned_at')
            ->latest('checked_out_at')
            ->first();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeAvailable($query)
    {
        return $query->whereHas('equipmentCondition', function($q) {  // Updated to use new relationship name
            $q->where('serviceable', true);
        })->whereDoesntHave('checkouts', function($q) {
            $q->whereNull('returned_at');
        });
    }

    public function scopeNeedsMaintenance($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('next_maintenance_date')
                ->where('next_maintenance_date', '<=', now());
        });
    }
}
