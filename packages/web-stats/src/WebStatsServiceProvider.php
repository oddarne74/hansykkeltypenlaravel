<?php

namespace WebStats;

use Illuminate\Support\ServiceProvider;
use WebStats\Http\Middleware\TrackWebStats;

class WebStatsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', TrackWebStats::class);
    }
}
