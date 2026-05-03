<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Fortify\Fortify;

use App\Nova\SystemLocation;
use App\Nova\SystemLocationAccess;

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
        Fortify::loginView(function () {
            return view('auth.login');
        });
    }
}
