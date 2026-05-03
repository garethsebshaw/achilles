<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Nova;

//use App\Policies\SystemLocationAccessPolicy;
//use App\Policies\SystemLocationPolicy;

class AppServiceProvider extends ServiceProvider
{
    /*
    protected $policies = [
        SystemLocation::class => SystemLocationPolicy::class,
        SystemLocationAccess::class => SystemLocationAccessPolicy::class,
        SystemCategory::class => \App\Policies\SystemCategoryPolicy::class,
    ];
*/
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $novaBasePath = '/'.trim(Nova::path(), '/');
        $novaDashboardPath = $novaBasePath === '/'
            ? '/dashboards/main'
            : $novaBasePath.'/dashboards/main';

        config([
            'fortify.home' => $novaDashboardPath,
            'fortify.redirects.login' => $novaDashboardPath,
            'fortify.redirects.logout' => '/login',
        ]);
    }
}
