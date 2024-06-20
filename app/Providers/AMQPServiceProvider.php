<?php

namespace App\Providers;

use App\Common\Components\RabbitMQ\RabbitMQ;
use Illuminate\Support\ServiceProvider;

class AMQPServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('MQ', function () {
            $rabbitMq = new RabbitMQ();
            $rabbitMq->init();
            return $rabbitMq;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

    }
}
