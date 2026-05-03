<?php

namespace App\Nova;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Http\Requests\NovaRequest;

class CertificationDocument extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\CertificationDocument>
     */
    public static $model = \App\Models\CertificationDocument::class;

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

            BelongsTo::make('Certification Type', 'certificationType', CertificationType::class)
                ->nullable(),

            Text::make('Name')->required(),
            Textarea::make('Description')->alwaysShow(),

            Number::make('Validity Period')
                ->min(1)
                ->max(255)
                ->required(),

            Boolean::make('Requires Document')
                ->default(false),

            BelongsTo::make('User Certification', 'userCertification', UserCertification::class)
                ->required(),

            File::make('Document', 'file_path')
                ->disk('public')
                ->path('certification-documents')
                ->prunable()
                ->deletable(),

            Text::make('File Type')->readonly(),
            DateTime::make('Uploaded At')->readonly(),
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
