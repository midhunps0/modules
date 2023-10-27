<?php

namespace Modules\Ynotz\SmartPages;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Ynotz\SmartPages\View\Composers\SidebarComposer;

class SmartPagesProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // $this->mergeConfigFrom(__DIR__ . '/../config/smartpages.php', 'smartpages');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'smartpages');
        Blade::componentNamespace('Modules\Ynotz\SmartPages\\View\\Components', 'smartpages');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php', 'smartpages');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'smartpages');
        // $this->publishes([
        //     __DIR__.'/../config/smartpages.php' => config_path('smartpages.php'),
        //     __DIR__.'/../resources/lang/en/icons.php' => resource_path('/lang/en/icons.php'),
        //     __DIR__.'/../resources/js/app-example.js' => resource_path('/js/app-example.js'),
        //     __DIR__.'/../resources/js/components' => resource_path('/js/components'),

        // ], 'smartpages');
        $this->publishes([
            __DIR__.'/resources/js/app-example.js' => resource_path('/js/app-example.js'),
            __DIR__.'/resources/js/components' => resource_path('/js/components'),

        ], 'smartpages-js');
    }
}
