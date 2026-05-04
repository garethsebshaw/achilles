<?php

namespace App\Nova\Lenses;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Laravel\Nova\Http\Requests\LensRequest;

class SysAdminUsers extends UserLens
{
    public static function query(LensRequest $request, Builder $query): Builder|Paginator
    {
        return static::baseLensQuery($request, $query->where('is_sys_admin', true));
    }

    public function uriKey(): string
    {
        return 'sys-admin-users';
    }
}
