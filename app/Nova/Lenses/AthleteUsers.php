<?php

namespace App\Nova\Lenses;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Laravel\Nova\Http\Requests\LensRequest;

class AthleteUsers extends UserLens
{
    public static function query(LensRequest $request, Builder $query): Builder|Paginator
    {
        return static::baseLensQuery($request, $query->where('is_athlete', true));
    }

    public function uriKey(): string
    {
        return 'athlete-users';
    }
}
