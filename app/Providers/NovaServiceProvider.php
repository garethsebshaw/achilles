<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Menu\MenuItem;

use App\Nova\WeatherLocation;
use App\Nova\Tools\WorkoutManagement\WorkoutManagement;

use Laravel\Nova\Providers\NovaServiceProvider as ServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Nova::serving(function () {
            Nova::script('session-attendance', ('resources/js/app.js'));
        });

        Nova::withBreadcrumbs();

        Nova::mainMenu(function () {
            return [
                MenuSection::dashboard(\App\Nova\Dashboards\Main::class)->icon('chart-bar'),

                MenuSection::make('Sessions', [
                    MenuItem::resource(\App\Nova\WorkoutSession::class),
                    MenuItem::resource(\App\Nova\WorkoutSignup::class),
                    MenuItem::resource(\App\Nova\WorkoutSpecificDetails::class),
                    MenuItem::resource(\App\Nova\WorkoutSessionMeetingPoint::class),
                ])->icon('clipboard-document-check')->collapsable(),

                MenuSection::make('Workout Templates', [
                    MenuItem::resource(\App\Nova\Workout::class),
                    MenuItem::resource(\App\Nova\WorkoutEquipmentAssignment::class),
                    MenuItem::resource(\App\Nova\MeetingPoint::class),
                ])->icon('document-duplicate')->collapsable(),

                MenuSection::make('Weather', [
                    MenuItem::resource(WeatherLocation::class),
                    MenuItem::resource(\App\Nova\WeatherData::class),
                    MenuItem::resource(\App\Nova\WeatherDailyData::class),
                    MenuItem::resource(\App\Nova\WeatherPreference::class),
                ])->icon('cloud')->collapsable(),

                // User Management
                MenuSection::make('Users', [
                    MenuItem::resource(\App\Nova\User::class),
                    MenuItem::resource(\App\Nova\UserCertification::class),
                    MenuItem::resource(\App\Nova\LanguageProficiency::class),
                ])->icon('users')->collapsable(),

                // Equipment Management
                MenuSection::make('Equipment', [
                    MenuItem::resource(\App\Nova\Equipment::class),
                    MenuItem::resource(\App\Nova\EquipmentComponent::class),
                    MenuItem::resource(\App\Nova\ComponentType::class),
                    MenuItem::resource(\App\Nova\Manufacturer::class),
                    MenuItem::resource(\App\Nova\StorageLocation::class),
                    MenuItem::resource(\App\Nova\EquipmentCondition::class),
                ])->icon('rocket-launch')->collapsable(),

                // Maintenance Management
                MenuSection::make('Maintenance', [
                    MenuItem::resource(\App\Nova\MaintenanceRequest::class),
                    MenuItem::resource(\App\Nova\MaintenanceLog::class),
                    MenuItem::resource(\App\Nova\EquipmentMaintenancePriority::class),
                    MenuItem::resource(\App\Nova\EquipmentCheckout::class),
                ])->icon('wrench')->collapsable(),

                // Certifications
                MenuSection::make('Certifications', [
                    MenuItem::resource(\App\Nova\Certification::class),
                    MenuItem::resource(\App\Nova\CertificationType::class),
                    MenuItem::resource(\App\Nova\CertificationDocument::class),
                ])->icon('academic-cap')->collapsable(),

                // Organization Management
                MenuSection::make('Organization', [
                    MenuItem::resource(\App\Nova\SystemCountry::class),
                    MenuItem::resource(\App\Nova\SystemRegion::class),
                    MenuItem::resource(\App\Nova\SystemChapter::class),
                    MenuItem::resource(\App\Nova\SystemChapterContact::class),
                    MenuItem::resource(\App\Nova\SystemLocation::class),
                ])->icon('office-building')->collapsable(),

                // System Configuration
                MenuSection::make('System', [
                    MenuItem::resource(\App\Nova\Language::class),
                    MenuItem::resource(\App\Nova\SystemModule::class),
                    MenuItem::resource(\App\Nova\SystemCategory::class),
                    MenuItem::resource(\App\Nova\SystemNotification::class),
                    MenuItem::resource(\App\Nova\SystemStatus::class),
                    MenuItem::resource(\App\Nova\SystemTag::class),
                    MenuItem::resource(\App\Nova\SystemSetting::class),
                    MenuItem::resource(\App\Nova\SystemAuditLog::class),
                    MenuItem::resource(\App\Nova\SystemMediaFile::class),
                ])->icon('cog')->collapsable(),
            ];
        });

        //Nova::script('checkin-polling', resource_path('js/nova-checkin-polling.js'));
    }
/*
    protected function resources()
    {
        Nova::resources([
            Workout::class,
            SystemCategory::class,
            SystemStatus::class,
            WeatherLocation::class,
        ]);

        Nova::resourcesIn(app_path('Nova'));
    }

    protected function navigation()
    {
        Nova::mainNavigation(function () {
            return [
                Nova::menu('Workouts', [
                    Workout::class,
                    Nova::menu('Configuration', [
                        SystemCategory::make()->path('system-categories')->setModel('App\Models\Workout'),
                        SystemStatus::make()->path('system-statuses')->setModel('App\Models\Workout'),
                    ])->collapsable(),
                ])->icon('tag'),
                // ... other main menu items
            ];
        });
    }*/

    /**
     * Register the Nova routes.
     *
     * @return void
     */
    public function routes()
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->register();
    }

    /**
     * Register the Nova gate.
     *
     * This gate determines who can access Nova in non-local environments.
     *
     * @return void
     */
    protected function gate()
    {
        Gate::define('viewNova', function ($user) {
            return $user instanceof \App\Models\User
                && $user->canAccessNova();
        });
    }

    /**
     * Get the dashboards that should be listed in the Nova sidebar.
     *
     * @return array
     */
    protected function dashboards()
    {
        return [
            new \App\Nova\Dashboards\Main,
        ];
    }

    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        return [
            //new \Laravel\Nova\Tools\WorkoutManagement\WorkoutManagement,
            //new \App\Nova\Tools\WorkoutManagement\WorkoutManagement,
        ];
    }
    protected function getDistPath($filename)
    {
        $manifest = json_decode(file_get_contents(public_path('build/.vite/manifest.json')), true);
        return '/build/' . $manifest[$filename]['file'];
    }
}
