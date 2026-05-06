<?php

namespace App\Providers;

use App\Modules\Logging\Models\AuditLog;
use App\Modules\Logging\Models\JobLog;
use App\Modules\Logging\Models\ModuleHealthCheck;
use App\Modules\Logging\Models\OperatorEvent;
use App\Modules\Logging\Models\SystemLog;
use App\Policies\AuditLogPolicy;
use App\Policies\JobLogPolicy;
use App\Policies\ModuleHealthCheckPolicy;
use App\Policies\OperatorEventPolicy;
use App\Policies\SystemLogPolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(SystemLog::class, SystemLogPolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);
        Gate::policy(JobLog::class, JobLogPolicy::class);
        Gate::policy(ModuleHealthCheck::class, ModuleHealthCheckPolicy::class);
        Gate::policy(OperatorEvent::class, OperatorEventPolicy::class);

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
