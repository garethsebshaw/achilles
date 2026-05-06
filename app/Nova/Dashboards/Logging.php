<?php

namespace App\Nova\Dashboards;

use Laravel\Nova\Dashboards\Main as Dashboard;

class Logging extends Dashboard
{
    public function cards(): array
    {
        return [
            new \App\Nova\Metrics\Logging\ErrorsLast24Hours(),
            new \App\Nova\Metrics\Logging\CriticalEventsLast7Days(),
            new \App\Nova\Metrics\Logging\FailedJobsLast24Hours(),
            new \App\Nova\Metrics\Logging\UnhealthyModulesCount(),
            new \App\Nova\Metrics\Logging\OpenOperatorEventsCount(),
            new \App\Nova\Metrics\Logging\LogsByLevel(),
            new \App\Nova\Metrics\Logging\LogsByModule(),
        ];
    }

    public function uriKey()
    {
        return 'logging';
    }

    public function label()
    {
        return __('Logging');
    }
}
