<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;

class Certification extends Resource
{
    public static $moduleName = 'Certification System';
    //public static $moduleType = static::class;
    public static $moduleDescription = 'Workout management system';

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\Certification>
     */
    public static $model = \App\Models\Certification::class;

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

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Text::make('Name')
                ->sortable()
                ->filterable()
                ->rules('required', 'max:255')
                ->creationRules('unique:certifications,name,NULL,id,certification_type_id,{{certification_type_id}}')
                ->updateRules('unique:certifications,name,{{resourceId}},id,certification_type_id,{{certification_type_id}}'),

            BelongsTo::make('Certification Type', 'certificationType', CertificationType::class)
                ->rules('required')
                ->filterable()
                ->showCreateRelationButton(),

            Textarea::make('Description')
                ->nullable()
                ->alwaysShow(),

            Number::make('Validity Period (Months)', 'validity_period')
                ->nullable()
                ->min(1)
                ->max(120)
                ->step(1),

            Boolean::make('Requires Document')
                ->sortable()
                ->default(false),

            BelongsTo::make('Status', 'systemStatus', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                    $query->whereHas('systemModule', function($q) {
                        $q->where('model_type', \App\Models\Certification::class);
                    });
                })
                ->required()
                ->filterable()
                ->showCreateRelationButton(),

            HasMany::make('User Certifications', 'userCertifications', UserCertification::class),

        ];
    }

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
