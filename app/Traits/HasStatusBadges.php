<?php

namespace App\Traits;

trait HasStatusBadges
{
    protected function getStatusBadgeClass($status)
    {
        return match ($status) {
            'active' => 'success',
            'pending' => 'warning',
            'inactive' => 'danger',
            'in-progress' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'info',
        };
    }

    protected function getPriorityBadgeClass($level)
    {
        return match ($level) {
            1 => 'info', // Low
            2 => 'success', // Normal
            3 => 'warning', // High
            4, 5 => 'danger', // Urgent/Critical
            default => 'info',
        };
    }

    protected function getConditionBadgeClass($rating)
    {
        return match ($rating) {
            5 => 'success', // Excellent
            4 => 'info',    // Good
            3 => 'warning', // Fair
            2, 1 => 'danger', // Poor/Critical
            default => 'info',
        };
    }
}
