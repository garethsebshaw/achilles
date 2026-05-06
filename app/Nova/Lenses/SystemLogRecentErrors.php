<?php

namespace App\Nova\Lenses;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

class SystemLogRecentErrors extends Lens
{
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->whereIn('level', ['error', 'critical', 'alert', 'emergency'])
                ->where('created_at', '>=', now()->subDays(7))
        ));
    }

    public function fields(Request $request)
    {
        return [
            ID::make()->sortable(),
        ];
    }
}
