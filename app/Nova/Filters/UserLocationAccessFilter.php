<?php

namespace App\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class UserLocationAccessFilter extends Filter
{
    public $component = 'select-filter';

    public function apply(Request $request, $query, $value)
    {
        return match ($value) {
            'active' => $query->whereHas('activeLocationAccessRecords'),
            'inactive_or_expired' => $query->whereHas('locationAccessRecords')
                ->whereDoesntHave('activeLocationAccessRecords'),
            'none' => $query->whereDoesntHave('locationAccessRecords'),
            default => $query,
        };
    }

    public function options(Request $request): array
    {
        return [
            __('Has Active Location Access') => 'active',
            __('Only Inactive or Expired Access') => 'inactive_or_expired',
            __('No Location Access Records') => 'none',
        ];
    }

    public function name()
    {
        return __('Location Access');
    }
}
