<?php

namespace App\Traits;
use App\Nova\SystemModule;

trait RegisterModule
{
    public static function registerModule()
    {
        $module = SystemModule::firstOrCreate(
            ['code' => static::$moduleCode],
            [
                'name' => static::$moduleName,
                'model_type' => static::class,
                'description' => static::$moduleDescription ?? null,
                'active' => true
            ]
        );

        return $module;
    }
}
