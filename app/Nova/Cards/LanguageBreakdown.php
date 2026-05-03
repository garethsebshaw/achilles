<?php

namespace App\Nova\Cards;

use Laravel\Nova\Card;
use App\Models\Language;

class LanguageBreakdown extends Card
{
    public $width = '1/3';

    public function __construct()
    {
        parent::__construct();
    }

    public function calculate()
    {
        $languageStats = Language::groupBy('system_category_id')
            ->selectRaw('system_category_id, count(*) as count')
            ->get();

        return $this->result($languageStats);
    }

    public function uriKey()
    {
        return 'language-breakdown';
    }
}
