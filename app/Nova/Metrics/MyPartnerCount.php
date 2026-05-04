<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Concerns\ResolvesDashboardScope;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Value;

class MyPartnerCount extends Value
{
    use ResolvesDashboardScope;

    public $width = '1/3';

    public function calculate(NovaRequest $request)
    {
        $user = $this->dashboardUser($request);

        if (! $user) {
            return $this->result(0);
        }

        $partnerIds = collect();

        if ($user->is_guide) {
            $partnerIds = $partnerIds->merge(
                DB::table('workout_signups')
                    ->where('user_id', $user->id)
                    ->whereNotNull('athlete_id')
                    ->pluck('athlete_id')
                    ->all()
            );
        }

        if ($user->is_athlete) {
            $partnerIds = $partnerIds->merge(
                DB::table('workout_signups as guide_signups')
                    ->join('users as guides', 'guide_signups.user_id', '=', 'guides.id')
                    ->where('guide_signups.athlete_id', $user->id)
                    ->where('guides.is_guide', true)
                    ->pluck('guide_signups.user_id')
                    ->all()
            );
        }

        return $this->result($partnerIds->filter()->unique()->count())
            ->help(__('Distinct training partners you have worked with.'));
    }

    public function name()
    {
        $user = $this->dashboardUser();

        if ($user?->is_guide && ! $user->is_athlete) {
            return __('Athletes Supported');
        }

        if ($user?->is_athlete && ! $user->is_guide) {
            return __('Guides Worked With');
        }

        return __('Partners Worked With');
    }
}
