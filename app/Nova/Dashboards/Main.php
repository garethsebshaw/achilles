<?php

namespace App\Nova\Dashboards;

use App\Nova\Cards\LanguageBreakdown;
use App\Nova\Cards\TotalAthletes;
use App\Nova\Cards\TotalGuides;
use App\Nova\Cards\TotalTeamLeaders;
use App\Nova\Metrics\GuidesGrowth;
use App\Nova\Metrics\TotalLanguageProficiencies;
use App\Nova\Metrics\UserGrowth;
use Laravel\Nova\Cards\Help;
use Laravel\Nova\Dashboards\Main as Dashboard;
use App\Nova\Metrics\UserTypeDistribution;

class Main extends Dashboard
{
    /**
     * Get the cards for the dashboard.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(): array
    {
        return [
            //new \App\Nova\Cards\CategoryBreakdown(),
            //new LanguageBreakdown(),
            new \App\Nova\Metrics\AthleteGuideDistribution(),
            new \App\Nova\Metrics\UserTypeDistribution(),
            //new UserGrowth(),
            new \App\Nova\Metrics\GuideGrowth(),
            new \App\Nova\Metrics\AthleteGrowth(),
            //new \App\Nova\Metrics\TotalTeamLeaders(),
            //new \App\Nova\Metrics\TotalTeamLeaders(),
            //new TotalLanguageProficiencies(),
            //new \App\Nova\Metrics\TotalTeamLeaders(),
            //new \App\Nova\Metrics\WorkoutAttendanceTrend(),
            //new \App\Nova\Metrics\ExpiringCertifications(),
            //new \App\Nova\Metrics\EquipmentStatusDistribution(),
            new \App\Nova\Metrics\TotalGuides(),
            new \App\Nova\Metrics\TotalAthletes(),
            new \App\Nova\Metrics\TotalTeamLeaders(),
        ];
    }
}
