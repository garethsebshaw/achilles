<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentComponent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'equipment_id',
        'component_type_id',
        'manufacturer_id',
        'model',
        'serial_number',
        'status_id',
        'condition_id',
        'installation_date',
        'warranty_expiry',
        'is_monitored',
        'maintenance_interval_miles',
        'maintenance_interval_months',
        'last_maintenance_date',
        'next_maintenance_date',
        'notes'
    ];

    protected $casts = [
        'installation_date' => 'date',
        'warranty_expiry' => 'date',
        'last_maintenance_date' => 'datetime',
        'next_maintenance_date' => 'datetime',
        'is_monitored' => 'boolean',
        'maintenance_interval_miles' => 'integer',
        'maintenance_interval_months' => 'integer'
    ];

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function componentType(): BelongsTo
    {
        return $this->belongsTo(ComponentType::class);
    }

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(SystemStatus::class, 'status_id');
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(EquipmentCondition::class);
    }

    public function maintenanceRequests()
    {
        return $this->hasMany(MaintenanceRequest::class, 'component_id');
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class, 'component_id');
    }

    public function needsMaintenance(): bool
    {
        if (!$this->next_maintenance_date) {
            return false;
        }
        return $this->next_maintenance_date <= now();
    }

    public function calculateNextMaintenanceDate()
    {
        if ($this->maintenance_interval_months) {
            return now()->addMonths($this->maintenance_interval_months);
        }
        return null;
    }

    public function getDaysSinceLastMaintenance(): ?int
    {
        if (!$this->last_maintenance_date) {
            return null;
        }
        return $this->last_maintenance_date->diffInDays(now());
    }

    public function getDaysUntilNextMaintenance(): ?int
    {
        if (!$this->next_maintenance_date) {
            return null;
        }
        return now()->diffInDays($this->next_maintenance_date, false);
    }

    public function isUnderWarranty(): bool
    {
        if (!$this->warranty_expiry) {
            return false;
        }
        return $this->warranty_expiry > now();
    }
}
