<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use WebStats\Http\Middleware\TrackWebStats;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php', commands: __DIR__.'/../routes/console.php', health: '/up')
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            TrackWebStats::class,
        ]);
    })
    ->withExceptions(fn (Exceptions $exceptions) => null)
    ->create();
