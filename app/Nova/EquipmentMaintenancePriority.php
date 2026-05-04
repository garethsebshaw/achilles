<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Builder;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;

class EquipmentMaintenancePriority extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\EquipmentMaintenancePriority>
     */
    public static $model = \App\Models\EquipmentMaintenancePriority::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'name', 'description'
    ];

    public static $group = 'Equipment Management';

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make(__('Name'))
                ->rules('required', 'max:255')
                ->sortable(),

            Textarea::make(__('Description'))
                ->nullable()
                ->rows(3),

            Number::make(__('Level'))
                ->rules('required', 'integer', 'min:1', 'max:5')
                ->min(1)
                ->max(5)
                ->sortable(),

            Number::make(__('Response Time (Hours)'), 'response_time_hours')
                ->nullable()
                ->min(0)
                ->step(0.5)
                ->help(__('Target response time in hours')),

            Panel::make(__('Relationships'), [
                HasMany::make(__('Maintenance Requests'), 'maintenanceRequests', MaintenanceRequest::class),
            ]),

            DateTime::make(__('Created At'))->onlyOnDetail(),
            DateTime::make(__('Updated At'))->onlyOnDetail(),
        ];
    }

    /*
    public static function indexQuery(NovaRequest $request, $query) : Builder
    {
        return $query->orderBy('level', 'desc');
    }
    */


    /**
     * Get the cards available for the resource.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Laravel\Nova\Filters\Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array<int, \Laravel\Nova\Lenses\Lens>
     */
    public function lenses(NovaRequest $request): array
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @return array<int, \Laravel\Nova\Actions\Action>
     */
    public function actions(NovaRequest $request): array
    {
        return [];
    }
}
// Add computed field trait for displaying statuses with badges
trait HasStatusBadges
{
    protected function getStatusBadgeClass($status)
    {
        return match ($status) {
            'active' => 'success',
            'pending' => 'warning',
            'inactive' => 'danger',
            'in-progress' => 'info',
            'completed' => 'success',
            'cancelled' => 'danger',
            default => 'info',
        };
    }

    protected function getPriorityBadgeClass($level)
    {
        return match ($level) {
            1 => 'info', // Low
            2 => 'success', // Normal
            3 => 'warning', // High
            4, 5 => 'danger', // Urgent/Critical
            default => 'info',
        };
    }
}
