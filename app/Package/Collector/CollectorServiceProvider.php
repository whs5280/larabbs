<?php

namespace App\Package\Collector;

use Illuminate\Support\ServiceProvider;

class CollectorServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/collector.php', 'collector');
    }
}
