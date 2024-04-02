<?php

namespace App\Package\Lottery;


use App\Package\Lottery\Commands;
use App\Package\Lottery\Http\Middleware\Lock;
use App\Package\Lottery\Repositories\PrizeRepository;
use Illuminate\Support\ServiceProvider;

class LotteryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/Migrations');
        }
        $this->loadTranslationsFrom(__DIR__ . '/Translations', 'lottery');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        $this->addMiddlewareAlias('lock', Lock::class);

        $this->commands([
            Commands\HandlePrizeCount::class,
        ]);

        $this->app->bind('App\Package\Lottery\Repositories\PrizeInterface', 'App\Package\Lottery\Repositories\PrizeRepository');
        $this->app->singleton('prize-repo', function () {
            return new PrizeRepository();
        });
    }

    /**
     * Register a short-hand name for a middleware. For compatibility
     * with Laravel < 5.4 check if aliasMiddleware exists since this
     * method has been renamed.
     *
     * @param string $name
     * @param string $class
     *
     * @return void
     */
    protected function addMiddlewareAlias(string $name, string $class)
    {
        $router = $this->app['router'];

        if (method_exists($router, 'aliasMiddleware')) {
            return $router->aliasMiddleware($name, $class);
        }

        return $router->middleware($name, $class);
    }
}
