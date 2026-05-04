<?php

namespace App\Nova;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\DateTime;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Support\Facades\Log;
use App\Traits\HandlesFileTypeIcons;

class UserCertification extends Resource
{
    use HandlesFileTypeIcons;

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\UserCertification>
     */
    public static $model = \App\Models\UserCertification::class;

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

            BelongsTo::make(__('User'))
                ->searchable()
                ->filterable(),

            Text::make(__('Name'))
                ->sortable(),

            BelongsTo::make(__('Certification'))
                ->showCreateRelationButton()
                ->filterable(),

            Date::make(__('Certified At'))->required(),
            Date::make(__('Expires At'))->nullable(),

            Number::make(__('Validity Period'))
                ->min(1)
                ->max(255)
                ->required(),

            Textarea::make(__('Description'))->alwaysShow(),

            BelongsTo::make(__('Status'), 'systemStatus', SystemStatus::class)
                ->relatableQueryUsing(function (NovaRequest $request, Builder $query) {
                    return $query->forModelType(\App\Models\UserCertification::class);
                })
                ->required()
                ->filterable(),

            Textarea::make(__('Notes'))->nullable()->alwaysShow(),

            File::make(__('Document'), 'file_path')
                ->store(function (Request $request, $model) {
                    // Log all files in the request
                    Log::info('Uploaded files:', [
                        'all_files' => $request->allFiles(),
                        'file_path' => $request->file('file_path')
                    ]);

                    $file = $request->file('file_path');

                    if ($file === null) {
                        Log::error('No file uploaded');
                        return null;
                    }

                    // Store file
                    $path = $file->store('user-documents', 'public');

                    return [
                        'file_path' => $path,
                        'file_type' => $file->getClientOriginalExtension() ?? 'unknown'
                    ];
                })
                ->disk('public')
                ->path('certification-documents')
                ->prunable()
                ->deletable(),

            Text::make(__('File Type'), function () {
                return $this->renderFileTypeIcon($this->file_type, $this);
            })->asHtml(),

            DateTime::make(__('Uploaded At'))
                ->readonly(),
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
