<?php

namespace Modules\Ynotz\AppSettings;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Modules\Ynotz\EasyAdmin\View\Composers\SidebarComposer;
use Modules\Ynotz\EasyAdmin\Services\SidebarServiceInterface;
use Modules\Ynotz\EasyAdmin\Services\DashboardServiceInterface;

class AppSettingsProvider extends ServiceProvider
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
        $this->loadViewsFrom(__DIR__.'/resources/views', 'ynotz_appsettings');
        // Blade::componentNamespace('Ynotz\AppSettings\\View\\Components', 'ynotz_appsettings');
        $this->loadMigrationsFrom(__DIR__.'/Database/migrations/');
    }
}
