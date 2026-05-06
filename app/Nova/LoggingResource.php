<?php

namespace App\Nova;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

abstract class LoggingResource extends Resource
{
    public static function availableForNavigation(Request $request)
    {
        return $request->user()?->hasPrivilegedRole() || $request->user()?->isSysAdmin();
    }

    public static function authorizedToViewAny(Request $request)
    {
        return $request->user()?->hasPrivilegedRole() || $request->user()?->isSysAdmin();
    }

    public static function authorizedToCreate(Request $request)
    {
        return false;
    }

    public function authorizedToUpdate(Request $request)
    {
        return false;
    }

    public function authorizedToDelete(Request $request)
    {
        return false;
    }

    public static function indexQuery(NovaRequest $request, Builder $query): Builder
    {
        $user = $request->user();

        if (! $user) {
            return $query->whereRaw('1 = 0');
        }

        if ($user->isSysAdmin()) {
            return $query;
        }

        if (! $user->hasPrivilegedRole() || ! $user->tenant_id) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('tenant_id', $user->tenant_id);
    }
}
