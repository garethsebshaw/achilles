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
use App\Nova\Filters\SessionTimeRangeFilter;


class SessionTimeLens extends Lens
{
    /**
     * Apply the query logic for the Lens.
     */
    public static function query(LensRequest $request, Builder $query): \Illuminate\Contracts\Database\Eloquent\Builder|Paginator
    {
        // Default to last 12 hours if no filter is selected
        $now = Carbon::now();

        $pastDate = $now->copy()->subHours(6);
        $futureDate = $now->copy()->addHours(6);

        // Apply ordering and filters
        return $request->withOrdering(
            $request->withFilters(
                $query
                    ->select('*')
                    //->where('location_id', 1)
                    ->whereBetween('session_date', [$pastDate, $futureDate])
            )
        );
    }

    /**
     * Get the available filters for the Lens.
     */
    public function filters(NovaRequest $request)
    {
        return [
            new SessionTimeRangeFilter(),
        ];
    }

    /**
     * Define the fields that will be displayed.
     */
    /**
     * Get the fields available to the lens.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            BelongsTo::make(__('Workout'))
                ->nullable()
                ->sortable()
                ->filterable(),

            BelongsTo::make(__('Location'), 'location', SystemLocation::class)
                ->rules('required')
                ->sortable()
                ->filterable(),

            Text::make(__('Session Date'))
                ->rules('required')
                ->displayUsing(fn ($value) => $value ? $value->format('D d/m/Y') : '')
                ->showOnIndex()
                ->sortable(),

            Text::make(__('Start Time'))
                ->rules('required')
                ->displayUsing(fn ($value) => $value ? $value->format('g:ia') : '')
                ->sortable(),

            Text::make(__('End Time'))
                ->rules('required')
                ->displayUsing(fn ($value) => $value ? $value->format('g:ia') : '')
                ->sortable(),


            BelongsTo::make(__('Status'), 'status', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    return $query->forModelType(\App\Models\WorkoutSession::class);
                }),
        ];
    }
}
