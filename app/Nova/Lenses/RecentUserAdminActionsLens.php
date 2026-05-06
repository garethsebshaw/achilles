<?php

namespace App\Nova\Lenses;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

class RecentUserAdminActionsLens extends Lens
{
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->where('action', 'like', '%user%')->where('created_at', '>=', now()->subDays(30))
        ));
    }

    public function fields(Request $request)
    {
        return [ID::make()->sortable()];
    }
}
