<?php

namespace App\Nova\Metrics;

use App\Models\UserCertification;
use Laravel\Nova\Metrics\Value;
use Laravel\Nova\Http\Requests\NovaRequest;

class ExpiringCertifications extends Value
{
    public function calculate(NovaRequest $request)
    {
        $expiringCount = UserCertification::query()
            ->whereHas('systemStatus', function($query) {
                $query->where('name', 'Renewal Required');
            })
            ->count();

        return $this->result($expiringCount)
            ->prefix('Certifications Expiring Soon:');
    }

    public function name()
    {
        return 'Expiring Certifications';
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            30 => __('30 Days'),
            60 => __('60 Days'),
            90 => __('90 Days'),
        ];
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'expiring-certifications';
    }
}
