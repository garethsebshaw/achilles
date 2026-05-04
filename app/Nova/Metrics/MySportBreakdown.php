<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class MySportBreakdown extends Partition
{
    use ResolvesDashboardScope;

    public $width = '1/2';

    public function calculate(NovaRequest $request)
    {
        $user = $this->dashboardUser($request);

        if (! $user) {
            return $this->result([]);
        }

        $data = $this->personalSignupQuery($user)
            ->selectRaw("COALESCE(sports.name, 'Unknown Sport') as sport_label, COUNT(*) as aggregate")
            ->groupBy('sport_label')
            ->pluck('aggregate', 'sport_label')
            ->toArray();

        return $this->result($data);
    }

    public function name()
    {
        return __('My Sports Mix');
    }
}
