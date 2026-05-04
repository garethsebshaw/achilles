<?php

namespace App\Nova\Cards;

use Laravel\Nova\Card;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TotalTeamLeaders extends Card
{
    public function calculate(NovaRequest $request)
    {
        $teamLeaderCount = User::where('is_team_leader', 1)->count();
        $nonTeamLeaderCount = User::where('is_team_leader', 0)->count();

        Log::info('Team Leaders Card Data', [
            'total_team_leaders' => $teamLeaderCount,
            'total_non_team_leaders' => $nonTeamLeaderCount,
            'total_users' => User::count()
        ]);

        return $this->withTotal($teamLeaderCount)
            ->title(__('Total Team Leaders'))
            ->content(__(':count Team Leaders', ['count' => $teamLeaderCount]))
            ->labels([__('Team Leaders'), __('Non-Team Leaders')])
            ->values([$teamLeaderCount, $nonTeamLeaderCount]);
    }

    public function uriKey()
    {
        return 'total-team-leaders';
    }
}
