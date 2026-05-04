<?php

namespace App\Nova\Lenses;

use App\Nova\MeetingPoint;
use App\Nova\SystemLocation;
use App\Nova\SystemStatus;
use App\Nova\User;
use App\Nova\WorkoutSignup;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\Paginator;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Nova;
use Carbon\Carbon;

//use App\Nova\Lenses\SessionWithin3Hours;
use App\Nova\Lenses\SessionWithin6Hours;
use App\Nova\Lenses\SessionWithin12Hours;

class SessionWithin3Hours extends Lens
{
    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [];

    /**
     * Get the query builder / paginator for the lens.
     */

    public static function query(LensRequest $request, Builder $query): Builder|Paginator
    {
        // Get the current timestamp
        $now = Carbon::now();

        // Calculate past and future 12-hour windows
        $pastDate = $now->copy()->subHours(3);
        $futureDate = $now->copy()->addHours(3);

        return $request->withOrdering(
            $request->withFilters(
                $query
                    ->select('*')
//                    ->where('location_id', 1)
                    ->whereBetween('session_date', [$pastDate, $futureDate])
            )
        );
    }


    /**
     * Get the fields available to the lens.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make(__('Session Date'))
                ->rules('required')
                ->displayUsing(fn ($value) => $value ? $value->format('D d/m/Y') : '')
                ->showOnIndex()
                ->sortable(),

            Text::make(__('Start Time'))
                ->rules('required')
                ->displayUsing(fn ($value) => $value ? $value->format('g:ia') : '')
                ->sortable(),

            BelongsTo::make(__('Workout'))
                ->nullable()
                ->sortable(),

            BelongsTo::make(__('Location'), 'location', SystemLocation::class)
                ->rules('required')
                ->sortable(),

            BelongsTo::make(__('Status'), 'status', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    return $query->forModelType(\App\Models\WorkoutSession::class);
                }),
        ];
    }
    public function lenses(NovaRequest $request): array
    {
        return [
//            new Lenses\WorkoutSessionUsers,
//            new SessionWithin3Hours(),
            new SessionWithin6Hours(),
            new SessionWithin12Hours(),
        ];
    }

    /**
     * Get the cards available on the lens.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the lens.
     *
     * @return array<int, \Laravel\Nova\Filters\Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available on the lens.
     *
     * @return array<int, \Laravel\Nova\Actions\Action>
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
        return 'session-within-3-hours';
    }
}
