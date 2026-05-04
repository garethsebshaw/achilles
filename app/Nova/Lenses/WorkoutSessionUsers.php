<?php

namespace App\Nova\Lenses;

use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;
use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Nova;

class WorkoutSessionUsers extends Lens
{
    /**
     * Get the query builder / paginator for the lens.
     */
    public static function query(LensRequest $request, $query): Builder
    {
        return $request->withOrdering($request->withFilters(
            $query->select(self::defaultColumns())
                ->join('users', 'workout_signups.user_id', '=', 'users.id')
                ->join('workout_sessions', 'workout_signups.workout_session_id', '=', 'workout_sessions.id')
                ->join('system_statuses', 'workout_signups.status_id', '=', 'system_statuses.id')
        ));
    }

    /**
     * Get the fields available to the lens.
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),

            Text::make(__('Name'), 'users.name')
                ->sortable(),

            Text::make(__('Email'), 'users.email')
                ->sortable(),

            Badge::make(__('Role'))->map([
                'athlete' => 'info',
                'guide' => 'success',
            ])->resolveUsing(function ($resource) {
                return $resource->user->is_athlete ? 'athlete' : 'guide';
            }),

            BelongsTo::make(__('Assigned To'), 'athlete', \App\Nova\User::class)
                ->nullable(),

            DateTime::make(__('Checked In At'))
                ->sortable(),

            DateTime::make(__('Checked Out At'))
                ->sortable(),

            Badge::make(__('Status'), 'system_statuses.name')
                ->map([
                    'signed_up' => 'info',
                    'checked_in' => 'success',
                    'attended' => 'success',
                    'cancelled' => 'danger',
                    'no_show' => 'warning',
                ]),
        ];
    }

    /**
     * Get the default columns that should be selected.
     */
    protected static function defaultColumns(): array
    {
        return [
            'workout_signups.id',
            'workout_signups.user_id',
            'workout_signups.athlete_id',
            'workout_signups.checked_in_at',
            'workout_signups.checked_out_at',
            'workout_signups.status_id',
            'users.name as user_name',
            'users.email as user_email',
            'users.is_athlete',
            'users.is_guide',
            'system_statuses.name as status_name',
        ];
    }

    /**
     * Get the cards available on the lens.
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available on the lens.
     */
    public function actions(NovaRequest $request): array
    {
        return parent::actions($request);
    }

    /**
     * Get the URI key for the lens.
     */
    public function uriKey(): string
    {
        return 'workout-session-users';
    }
}
