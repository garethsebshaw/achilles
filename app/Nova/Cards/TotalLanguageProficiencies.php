<?php

namespace App\Nova\Cards;

use Illuminate\Http\Request;
use Laravel\Nova\Card;
use App\Models\LanguageProficiency;

class TotalLanguageProficiencies extends Card
{
    public function __construct()
    {
        parent::__construct();
    }

    public function calculate(Request $request)
    {
        // Get category counts
        $categoryCounts = SystemCategory::withCount('languages')
            ->get()
            ->mapWithKeys(function ($category) {
                return [$category->name => $category->languages_count];
            });

        return $this->withTotal($categoryCounts->sum())
            ->chartData($categoryCounts->toArray());
    }

    public function uriKey()
    {
        return 'category-breakdown';
    }

    public function authorizedToSee(Request $request)
    {
        return true;
    }
}
