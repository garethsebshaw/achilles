<?php

namespace App\Http\Middleware;

use Closure;
use Laravel\Nova\Nova;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;

class InjectSocialLogin
{
    public function handle(Request $request, Closure $next)
    {
        Nova::script('social-login', asset('js/social-login.js'));
        Nova::style('social-login', asset('css/social-login.css'));

        return $next($request);
    }
}
