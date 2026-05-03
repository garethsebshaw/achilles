<?php
namespace App\Traits;

use Carbon\Carbon;

trait HasMaintenance
{
    public function isMaintenanceOverdue(): bool
    {
        if (!$this->next_maintenance_date) {
            return false;
        }
        return Carbon::now()->greaterThan($this->next_maintenance_date);
    }

    public function getDaysSinceLastMaintenance(): ?int
    {
        if (!$this->last_maintenance_date) {
            return null;
        }
        return Carbon::parse($this->last_maintenance_date)->diffInDays(Carbon::now());
    }

    public function getDaysUntilNextMaintenance(): ?int
    {
        if (!$this->next_maintenance_date) {
            return null;
        }
        return Carbon::now()->diffInDays(Carbon::parse($this->next_maintenance_date), false);
    }

    public function calculateNextMaintenanceDate(): Carbon
    {
        if ($this->maintenance_interval_months) {
            return Carbon::now()->addMonths($this->maintenance_interval_months);
        }
        // Default to 6 months if no interval is set
        return Carbon::now()->addMonths(6);
    }

    public function needsImmediateMaintenance(): bool
    {
        return $this->isMaintenanceOverdue() ||
            $this->status === 'needs_repair' ||
            $this->condition->rating <= 2;
    }
}
