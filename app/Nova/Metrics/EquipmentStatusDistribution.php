<?php

namespace App\Nova\Metrics;

use App\Models\Equipment;
use Laravel\Nova\Metrics\Partition;

class EquipmentStatusDistribution extends Partition
{
    public function calculate()
    {
        return $this->count(Equipment::class, 'system_status_id')
            ->label(function($value) {
                return \App\Models\SystemStatus::find($value)->name ?? __('Unknown');
            });
    }

    public function name()
    {
        return __('Equipment Status');
    }
}
