<?php

namespace App\Nova\Cards;

use Laravel\Nova\Card;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TotalAthletes extends Card
{
    public function calculate(NovaRequest $request)
    {
        $athleteCount = User::where('is_athlete', 1)->count();
        $nonAthleteCount = User::where('is_athlete', 0)->count();

        Log::info('Athletes Card Data', [
            'total_athletes' => $athleteCount,
            'total_non_athletes' => $nonAthleteCount,
            'total_users' => User::count()
        ]);

        return $this->withTotal($athleteCount)
            ->title('Total Athletes')
            ->content("{$athleteCount} Athletes")
            ->labels(['Athletes', 'Non-Athletes'])
            ->values([$athleteCount, $nonAthleteCount]);
    }

    public function uriKey()
    {
        return 'total-athletes';
    }
}
