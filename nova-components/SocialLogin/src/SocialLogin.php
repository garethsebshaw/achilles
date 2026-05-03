<?php

namespace Achilles\SocialLogin;

use Illuminate\Http\Request;
use Laravel\Nova\Menu\MenuSection;
use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class SocialLogin extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     */
    public function boot(): void
    {
        //Nova::mix('social-login', __DIR__.'/../dist/mix-manifest.json');
        Nova::script('social-login', __DIR__.'/../dist/js/tool.js');
    }

    /**
     * Build the menu that renders the navigation links for the tool.
     */
    public function menu(Request $request): MenuSection
    {
        /*
        return MenuSection::make('Social Login')
            ->path('/social-login')
            ->icon('server');
        */
        return MenuSection::make('Social Login')
            ->path('/social-login')
            ->icon('user');
    }
}
