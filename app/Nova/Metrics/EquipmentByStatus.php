<?php

namespace App\Nova\Metrics;

use App\Models\Equipment;
use DateTimeInterface;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Metrics\PartitionResult;

class EquipmentByStatus extends Partition
{
    /**
     * Calculate the value of the metric.
     */
    public function calculate(NovaRequest $request): PartitionResult
    {
        $data = Equipment::query()
            ->leftJoin('system_statuses', 'equipment.status_id', '=', 'system_statuses.id')
            ->selectRaw("COALESCE(system_statuses.name, 'Unknown') as status_label, COUNT(*) as aggregate")
            ->groupBy('status_label')
            ->pluck('aggregate', 'status_label')
            ->toArray();

        return $this->result($data);
    }

    /**
     * Determine the amount of time the results of the metric should be cached.
     */
    public function cacheFor(): DateTimeInterface|null
    {
        return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     */
    public function uriKey(): string
    {
        return 'equipment-by-status';
    }
}
