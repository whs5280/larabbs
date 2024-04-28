<?php

namespace App\Package\Mission;

use App\Package\Mission\Periods\MissionPeriodManager;
use App\Package\Mission\Repositories\Repository as MissionRepositoryManager;
use Illuminate\Support\ServiceProvider;

class MissionServiceProvider extends ServiceProvider
{
    public function register()
    {
        // 任务仓库
        $this->app->singleton('mission-repo', function () {
            return new MissionRepositoryManager();
        });
        // 任务周期
        $this->app->singleton('mission-period', function ($app) {
            return new MissionPeriodManager($app);
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        }
        $this->loadTranslationsFrom(__DIR__ . '/Translations', 'mission');
    }
}
