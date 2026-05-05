<?php

namespace App\Nova;

use App\Nova\Filters\EquipmentLocationFilter;
use App\Nova\Filters\EquipmentOwnershipFilter;
use App\Nova\Filters\EquipmentStatusFilter;
use App\Nova\Lenses\CheckedOutEquipment;
use App\Nova\Lenses\EquipmentRequiringMaintenance;
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
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use App\Nova\Fields\HierarchicalCategoryField;

use App\Nova\Fields\SystemCategoryField;

class Equipment extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Equipment>
     */
    public static $model = \App\Models\Equipment::class;

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
        'name', 'model', 'serial_number', 'qr_code'
    ];

    public static $group = 'Equipment Management';

    public static $perPageOptions = [25, 50, 100];

    public static function indexQuery(NovaRequest $request, BuilderContract $query): BuilderContract
    {
        $query = $query
            ->with([
                'manufacturer',
                'location.chapter',
                'storageLocation',
                'status',
                'equipmentCondition',
                'assignedUser',
            ])
            ->withCount([
                'components',
                'maintenanceRequests',
                'checkouts',
            ]);

        if (! $request->filled('orderBy')) {
            $query->orderByDesc('is_active')
                ->orderBy('name');
        }

        return $query;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),
/*
            BelongsTo::make(__('Category'), 'category', SystemCategory::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    $workoutsModuleId = \App\Models\SystemModule::where('model_type', \App\Models\Equipment::class)->first()->id;
                    return $query->where('system_module_id', $workoutsModuleId);
                })
                ->rules('required')
                ->filterable(),*/

            HierarchicalCategoryField::make(__('Category'), 'category')
                ->rules('required'),

            BelongsTo::make(__('Manufacturer'))
                ->searchable()
                ->rules('required'),

            Text::make(__('Name'))
                ->rules('required', 'max:255')
                ->sortable(),

            Number::make(__('Components'), 'components_count')
                ->exceptOnForms()
                ->sortable(),

            Number::make(__('Maintenance Requests'), 'maintenance_requests_count')
                ->exceptOnForms()
                ->sortable(),

            Number::make(__('Checkouts'), 'checkouts_count')
                ->exceptOnForms()
                ->sortable(),

            Text::make(__('Model'))
                ->nullable(),

            Text::make(__('Serial Number'))
                ->nullable(),

            Text::make(__('QR Code'))
                ->rules('required', 'unique:equipment,qr_code,{{resourceId}}')
                ->creationRules('unique:equipment,qr_code')
                ->updateRules('unique:equipment,qr_code,{{resourceId}}'),

            BelongsTo::make(__('Location'), 'location', SystemLocation::class)
                ->searchable()
                ->rules('required'),

            BelongsTo::make(__('Storage Location'), 'storageLocation', StorageLocation::class)
                ->searchable()
                ->nullable(),

            BelongsTo::make(__('Status'), 'status', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, $query) {
                    return $query->forModelType(\App\Models\Equipment::class);
                })
                ->rules('required')
                ->filterable(),

            BelongsTo::make(__('Condition'), 'equipmentCondition', EquipmentCondition::class)
                ->rules('required'),

            Select::make(__('Owner Type'))
                ->options([
                    'athlete' => __('Athlete'),
                    'partner' => __('Partner'),
                ])
                ->nullable(),

            Number::make(__('Owner ID'))
                ->nullable()
                ->hideFromIndex(),

            BelongsTo::make(__('Assigned To'), 'assignedUser', User::class)
                ->searchable()
                ->nullable(),

            Date::make(__('Purchase Date'))
                ->nullable(),

            Currency::make(__('Purchase Price'))
                ->nullable(),

            Date::make(__('Warranty Expiry'))
                ->nullable(),

            DateTime::make(__('Last Maintenance Date'))
                ->nullable(),

            DateTime::make(__('Next Maintenance Date'))
                ->nullable(),

            Textarea::make(__('Notes'))
                ->nullable()
                ->rows(3),

            Code::make(__('Attributes'))
                ->json()
                ->nullable(),

            Boolean::make(__('Is Active'))
                ->default(true),

            Panel::make(__('Relationships'), [
                HasMany::make(__('Components'), 'components', EquipmentComponent::class),
                HasMany::make(__('Maintenance Requests'), 'maintenanceRequests', MaintenanceRequest::class),
                HasMany::make(__('Maintenance Logs'), 'maintenanceLogs', MaintenanceLog::class),
                HasMany::make(__('Checkouts'), 'checkouts', EquipmentCheckout::class),
            ]),

            DateTime::make(__('Created At'))->onlyOnDetail(),
            DateTime::make(__('Updated At'))->onlyOnDetail(),
        ];
    }

    /**
     * Get the cards available for the resource.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [
            new Metrics\EquipmentTotal(),
            new Metrics\ActiveMaintenanceRequests(),
            new Metrics\EquipmentByStatus(),
            new Metrics\EquipmentCheckouts(),
        ];
    }

    /**
     * Get the filters available for the resource.
     *
     * @return array<int, \Laravel\Nova\Filters\Filter>
     */
    public function filters(NovaRequest $request): array
    {
        return [
            new EquipmentStatusFilter(),
            new EquipmentLocationFilter(),
            new EquipmentOwnershipFilter(),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @return array<int, \Laravel\Nova\Lenses\Lens>
     */
    public function lenses(NovaRequest $request): array
    {
        return [
            new EquipmentRequiringMaintenance(),
            new CheckedOutEquipment(),
        ];
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

    /**
     * Get the URI key for the resource.
     *
     * @return string
     */
    public static function uriKey()
    {
        return 'equipment';
    }

}
