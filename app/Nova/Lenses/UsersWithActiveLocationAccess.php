<?php

namespace App\Nova\Lenses;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Laravel\Nova\Http\Requests\LensRequest;

class UsersWithActiveLocationAccess extends UserLens
{
    public static function query(LensRequest $request, Builder $query): Builder|Paginator
    {
        return static::baseLensQuery($request, $query->whereHas('activeLocationAccessRecords'));
    }

    public function uriKey(): string
    {
        return 'users-with-active-location-access';
    }
}
