<?php

namespace App\Nova\Lenses;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Laravel\Nova\Http\Requests\LensRequest;

class UnverifiedUsers extends UserLens
{
    public static function query(LensRequest $request, Builder $query): Builder|Paginator
    {
        return static::baseLensQuery($request, $query->whereNull('email_verified_at'));
    }

    public function uriKey(): string
    {
        return 'unverified-users';
    }
}
