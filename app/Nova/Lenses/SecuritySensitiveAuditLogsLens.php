<?php

namespace App\Nova\Lenses;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

class SecuritySensitiveAuditLogsLens extends Lens
{
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->where(function ($auditQuery) {
                $auditQuery
                    ->where('action', 'like', '%secret%')
                    ->orWhere('action', 'like', '%role%')
                    ->orWhere('action', 'like', '%setting%')
                    ->orWhere('action', 'like', '%key%');
            })
        ));
    }

    public function fields(Request $request)
    {
        return [ID::make()->sortable()];
    }
}
