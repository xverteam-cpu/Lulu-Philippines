<?php

use App\Http\Middleware\BlockBlockedIp;
use App\Http\Middleware\PreventAuthenticatedPageCaching;
use App\Http\Middleware\RestrictUserAccess;
use App\Http\Middleware\TrackUserActivity;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('interest:accrue')->dailyAt('00:05');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            BlockBlockedIp::class,
            TrackUserActivity::class,
            RestrictUserAccess::class,
            PreventAuthenticatedPageCaching::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
