<?php

namespace App\Nova\Cards;

use Laravel\Nova\Card;
use Laravel\Nova\Http\Requests\NovaRequest;

class CategoryBreakdown extends Card
{
    public $width = '1/3';

    public function __construct()
    {
        parent::__construct();
    }

    public function calculate(NovaRequest $request)
    {
        // Implement your calculation logic here
        return $this->result([
            // Example data structure
            'labels' => ['Active', 'Inactive'],
            'values' => [100, 50]
        ]);
    }

    public function uriKey()
    {
        return 'category-breakdown';
    }

}
