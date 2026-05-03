<?php
namespace App\Traits;

trait HasEquipmentTracking
{
    public function getTotalUsageTime(): int
    {
        return $this->checkouts()
            ->whereNotNull('returned_at')
            ->sum(DB::raw('TIMESTAMPDIFF(MINUTE, checked_out_at, returned_at)'));
    }

    public function getTotalDistanceTraveled(): int
    {
        return $this->checkouts()
            ->whereNotNull('distance_traveled')
            ->sum('distance_traveled');
    }

    public function getMaintenanceCostTotal(): float
    {
        return $this->maintenanceLogs()
            ->whereNotNull('cost')
            ->sum('cost');
    }

    public function getAverageUsagePerMonth(): float
    {
        $totalMonths = Carbon::parse($this->purchase_date)->diffInMonths(Carbon::now());
        if ($totalMonths === 0) {
            return 0;
        }
        return $this->getTotalUsageTime() / $totalMonths;
    }

    public function getLifetimeValue(): float
    {
        $totalCost = $this->purchase_price + $this->getMaintenanceCostTotal();
        $totalUse = $this->getTotalUsageTime();
        if ($totalUse === 0) {
            return 0;
        }
        return $totalCost / $totalUse;
    }
}
