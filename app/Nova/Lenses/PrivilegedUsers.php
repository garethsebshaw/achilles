<?php

namespace App\Nova\Lenses;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Laravel\Nova\Http\Requests\LensRequest;

class PrivilegedUsers extends UserLens
{
    public static function query(LensRequest $request, Builder $query): Builder|Paginator
    {
        return static::baseLensQuery($request, $query->where(function ($query) {
            $query->where('is_sys_admin', true)
                ->orWhere('is_admin', true)
                ->orWhere('is_team_leader', true);
        }));
    }

    public function uriKey(): string
    {
        return 'privileged-users';
    }
}
