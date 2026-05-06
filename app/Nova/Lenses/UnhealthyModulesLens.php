<?php

namespace App\Nova\Lenses;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

class UnhealthyModulesLens extends Lens
{
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->where('status', '!=', 'healthy')->whereNull('resolved_at')
        ));
    }

    public function fields(Request $request)
    {
        return [ID::make()->sortable()];
    }
}
