<?php

namespace App\Nova;

use App\Models\User as UserModel;

use App\Nova\Lenses\AthleteUsers;
use App\Nova\Lenses\GuideUsers;
use App\Nova\Lenses\TeamLeadUsers;
use App\Nova\Lenses\SysAdminUsers;
use App\Nova\Lenses\AdminUsers;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource;
use Laravel\Nova\Auth\PasswordValidationRules;
use Laravel\Nova\Fields\Avatar;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Gravatar;
use Laravel\Nova\Fields\UiAvatar;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Password;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\File;
use Laravel\Nova\Fields\Image;
use Laravel\Nova\Http\Requests\NovaRequest;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Nova\Fields\HasMany;

use App\Nova\Cards\TotalGuides;
use App\Nova\Cards\TotalAthletes;
use App\Nova\Cards\TotalTeamLeaders;
use App\Nova\Metrics\UserGrowth;
use Sietse85\NovaButton\Button;

class User extends Resource
{
    use PasswordValidationRules, SoftDeletes;

    /**
     * The model the resource corresponds to.
     *
     * @var class-string<\App\Models\User>
     */
    public static $model = \App\Models\User::class;

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
        'name', 'preferred_name', 'first_name', 'middle_name', 'last_name', 'email',
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        return 'Users';
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        return 'User';
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array<int, \Laravel\Nova\Fields\Field|\Laravel\Nova\Panel|\Laravel\Nova\ResourceTool|\Illuminate\Http\Resources\MergeValue>
     */
    public function fields(NovaRequest $request): array
    {
        return [
            ID::make()->sortable(),

            Button::make('Check IN'),
            Button::make('Check OUT'),

            Button::make('Re-Assign'),

            Avatar::make('Photo')
                ->thumbnail(function () {
                    return $this->picture ? $this->picture : $this->getGravatarUrl($this->email);
                })
                ->maxWidth(50)
                ->hideFromDetail()
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            // Image preview
            Image::make('Photo')
                ->preview(function () {
                    return $this->picture ? $this->picture : $this->getGravatarUrl($this->email);
                })
                ->disableDownload()
                ->hideFromIndex()
                ->hideWhenCreating()
                ->hideWhenUpdating(),

            Text::make('Name')
                ->sortable()
                ->hideFromIndex()
                ->rules('required', 'max:255'),
            /*
            Text::make('Preferred Name')
                ->sortable()
                ->rules('required', 'max:255'),
            */

            Text::make('First Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Middle Name')
                ->sortable()
                ->hideFromIndex()
                ->rules('required', 'max:255'),

            Text::make('Last Name')
                ->sortable()
                ->rules('required', 'max:255'),

            Text::make('Email')
                ->sortable()
                ->rules('required', 'email', 'max:254')
                ->creationRules('unique:users,email')
                ->updateRules('unique:users,email,{{resourceId}}'),

            Boolean::make('Sys Admin','is_sys_admin')
                ->filterable(),

            Boolean::make('Admin','is_admin')
                ->filterable(),

            Boolean::make('Team Lead','is_team_leader')
                ->filterable(),

            Boolean::make('Athlete','is_athlete')
                ->filterable(),

            Boolean::make('Guide','is_guide')
                ->filterable(),

            Password::make('Password')
                ->onlyOnForms()
                ->creationRules($this->passwordRules())
                ->updateRules($this->optionalPasswordRules()),

            Panel::make('Relationships', [
                HasMany::make('Certifications', 'userCertification', UserCertification::class),
                HasMany::make('Language Proficiencies', 'languageProficiency', LanguageProficiency::class),
            ]),

        ];
    }

    public static function uriKey()
    {
        return 'users';
    }

    protected function getGravatarUrl($email)
    {
        $hash = md5(strtolower(trim($email)));
        return "https://www.gravatar.com/avatar/{$hash}?s=250";
    }

    /**
     * Get the cards available for the request.
     *
     * @return array<int, \Laravel\Nova\Card>
     */
    public function cards(NovaRequest $request): array
    {
        return [
//            (new Metrics\TotalGuides())->width('1/3'),
//            (new Metrics\TotalAthletes())->width('1/3'),
//            (new Metrics\TotalTeamLeaders())->width('1/3'),
            //(new Metrics\UserGrowth())->width('1/3')
        ];
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
        return [
            new SysAdminUsers(),
            new AdminUsers(),
            new TeamLeadUsers(),
            new GuideUsers(),
            new AthleteUsers()
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
}
