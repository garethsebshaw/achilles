<?php

namespace App\Nova\Cards;

use Laravel\Nova\Card;
use Laravel\Nova\Http\Requests\NovaRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class TotalGuides extends Card
{
    public function calculate(NovaRequest $request)
    {
        // Log the count details
        $guideCount = User::where('is_guide', 1)->count();
        $nonGuideCount = User::where('is_guide', 0)->count();

        Log::info('Guides Card Data', [
            'total_guides' => $guideCount,
            'total_non_guides' => $nonGuideCount,
            'total_users' => User::count()
        ]);

        // Manually set numeric values
        return $this->withTotal($guideCount)
            ->title(__('Total Guides'))
            ->content(__(':count Guides', ['count' => $guideCount]))
            ->labels([__('Guides'), __('Non-Guides')])
            ->values([$guideCount, $nonGuideCount]);
    }

    public function uriKey()
    {
        return 'total-guides';
    }
}
