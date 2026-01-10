<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        
        // Apply security middleware globally to API routes
        $middleware->api(prepend: [
            \App\Http\Middleware\SecurityHeaders::class,
            \App\Http\Middleware\RequestSizeLimit::class,
            \App\Http\Middleware\IpBlocker::class,
        ]);
        
        // Rate limiting aliases
        // FIX: Use env() instead of config() here to prevent "ReflectionException" during startup.
        $middleware->alias([
            'throttle.public' => \Illuminate\Routing\Middleware\ThrottleRequests::class.':'.env('APP_RATE_LIMIT_PUBLIC', 10).',1',
            'throttle.auth'   => \Illuminate\Routing\Middleware\ThrottleRequests::class.':'.env('APP_RATE_LIMIT_AUTH', 60).',1',
            'throttle.login'  => \Illuminate\Routing\Middleware\ThrottleRequests::class.':'.env('APP_RATE_LIMIT_LOGIN_ATTEMPTS', 5).',1',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();