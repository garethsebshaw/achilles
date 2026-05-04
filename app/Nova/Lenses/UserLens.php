<?php

namespace App\Nova\Lenses;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Nova;

abstract class UserLens extends Lens
{
    public static $search = [
        'name',
        'preferred_name',
        'first_name',
        'last_name',
        'email',
    ];

    protected static function baseLensQuery(LensRequest $request, Builder $query): Builder|Paginator
    {
        return $request->withOrdering($request->withFilters(
            $query
                ->withCount([
                    'languageProficiency',
                    'userCertification',
                    'activeLocationAccessRecords as active_location_access_count',
                    'workoutSignups',
                ])
                ->orderByDesc('created_at')
                ->orderByDesc('id')
        ));
    }

    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(Nova::__('ID'), 'id')->sortable(),
            Text::make(__('Name'), 'name')->sortable(),
            Text::make(__('Email'), 'email')->sortable(),
            Boolean::make(__('Subscribed'), 'is_subscribed')->sortable(),
            Boolean::make(__('Verified'), fn () => $this->email_verified_at !== null),
            Boolean::make(__('Athlete'), 'is_athlete')->sortable(),
            Boolean::make(__('Guide'), 'is_guide')->sortable(),
            Boolean::make(__('Team Lead'), 'is_team_leader')->sortable(),
            Boolean::make(__('Admin'), 'is_admin')->sortable(),
            Boolean::make(__('Sys Admin'), 'is_sys_admin')->sortable(),
            Number::make(__('Active Location Access'), 'active_location_access_count')->sortable(),
            Number::make(__('Languages'), 'language_proficiency_count')->sortable(),
            Number::make(__('Certifications'), 'user_certification_count')->sortable(),
            Number::make(__('Signups'), 'workout_signups_count')->sortable(),
            DateTime::make(__('Created At'), 'created_at')->sortable(),
        ];
    }

    public function cards(NovaRequest $request): array
    {
        return [];
    }

    public function filters(NovaRequest $request): array
    {
        return [];
    }

    public function actions(NovaRequest $request): array
    {
        return parent::actions($request);
    }
}
