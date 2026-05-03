<?php

namespace App\Nova\Metrics;

use App\Models\User;
use Laravel\Nova\Metrics\Partition;
use Laravel\Nova\Http\Requests\NovaRequest;

class AthleteGuideDistribution extends Partition
{
    public $width = '1/2';

    public function calculate(NovaRequest $request)
    {
        return $this->count($request, User::class, 'is_guide')
            ->colors([
                1 => '#68D391', // Green for guides
                0 => '#4299E1'  // Blue for athletes
            ])
            ->label(function($value) {
                return $value ? 'Guides' : 'Athletes';
            });
    }

    public function name()
    {
        return 'Guide vs Athlete Distribution';
    }

    /**
     * Determine the amount of time the results should be cached.
     *
     * @return \DateTimeInterface|\DateInterval|float|int|null
     */
    public function cacheFor()
    {
        return now()->addMinutes(5);
    }
}
