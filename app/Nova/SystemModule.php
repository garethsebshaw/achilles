<?php

namespace App\Nova;

use App\Models\SystemModule as SystemModuleModel;
use App\Nova\Filters\ModuleImplementationStateFilter;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Badge;
use Laravel\Nova\Fields\Code;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class SystemModule extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\SystemModule>
     */
    public static $model = \App\Models\SystemModule::class;

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
        'name',
        'model_type',
        'description',
        'active'
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return __('System Modules');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return __('System Module');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function fields(NovaRequest $request)
    {
        return [
            ID::make()->sortable(),

            Text::make(__('Name'))
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make(__('Model Type'))
                ->sortable()
                ->rules('required'),

            Textarea::make(__('Description'))
                ->nullable()
                ->hideFromIndex(),

            Boolean::make(__('Active'))
                ->default(true)
                ->sortable(),

            Badge::make(__('Implementation Status'), fn () => $this->implementation_state)
                ->map([
                    SystemModuleModel::STATE_IMPLEMENTED => 'success',
                    SystemModuleModel::STATE_PARTIAL_SHELL => 'warning',
                    SystemModuleModel::STATE_MAPPED_ALIAS => 'info',
                    SystemModuleModel::STATE_MISSING_SPEC_ONLY => 'danger',
                ])
                ->labels([
                    SystemModuleModel::STATE_IMPLEMENTED => __('Implemented'),
                    SystemModuleModel::STATE_PARTIAL_SHELL => __('Partial Shell'),
                    SystemModuleModel::STATE_MAPPED_ALIAS => __('Mapped Alias'),
                    SystemModuleModel::STATE_MISSING_SPEC_ONLY => __('Spec Only'),
                ])
                ->sortable()
                ->exceptOnForms(),

            Text::make(__('Canonical Model'), fn () => $this->canonical_model_type)
                ->readonly()
                ->hideFromIndex()
                ->nullable(),

            Textarea::make(__('Implementation Notes'), fn () => $this->implementation_notes)
                ->readonly()
                ->hideFromIndex()
                ->alwaysShow()
                ->nullable(),

            Code::make(__('Metadata'))
                ->json()
                ->nullable()
                ->hideFromIndex(),

            HasMany::make(__('Categories'), 'systemCategories', SystemCategory::class),
            HasMany::make(__('Statuses'), 'systemStatuses', SystemStatus::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function cards(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function filters(NovaRequest $request)
    {
        return [
            new ModuleImplementationStateFilter(),
        ];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function lenses(NovaRequest $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return array
     */
    public function actions(NovaRequest $request)
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
        return 'system-modules';
    }
}
