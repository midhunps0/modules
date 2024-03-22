<?php

namespace Modules\Ynotz\EasyAdmin;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Ynotz\EasyAdmin\Commands\MakeEasyadminCommand;
use Modules\Ynotz\EasyAdmin\View\Composers\SidebarComposer;
use Modules\Ynotz\EasyAdmin\Contracts\SidebarServiceInterface;
use Modules\Ynotz\EasyAdmin\Services\DashboardServiceInterface;

class EasyAdminProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // $this->mergeConfigFrom(__DIR__ . '/../config/easyadmin.php', 'easyadmin');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeEasyadminCommand::class
            ]);
        }
        $this->loadViewsFrom(__DIR__.'/resources/views', 'easyadmin');
        Blade::componentNamespace('Modules\Ynotz\EasyAdmin\\View\\Components', 'easyadmin');
        $this->loadRoutesFrom(__DIR__.'/routes/web.php', 'easyadmin');
        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'easyadmin');
        $this->publishes([
            __DIR__.'/config/easyadmin.php' => config_path('easyadmin.php'),

        ], ['easyadmin-config', 'easyadmin', 'base-modules']);
        $this->publishes([
            __DIR__.'/resources/lang' => $this->app->langPath('ynotz/easyadmin'),

        ], ['easyadmin-lang', 'easyadmin', 'base-modules']);
        $this->app->bind(DashboardServiceInterface::class, config('easyadmin.dashboard_service'));
        $this->app->bind(SidebarServiceInterface::class, config('easyadmin.sidebar_service'));

        Paginator::defaultView('easyadmin::pagination.custom');
        Paginator::defaultSimpleView('easyadmin::pagination.custom');

        View::composer('easyadmin::components.partials.sidebar', SidebarComposer::class);
    }
}
