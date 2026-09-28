<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('database.default') !== 'sqlite') {
            return;
        }

        $database = config('database.connections.sqlite.database');

        if (empty($database) || str_contains($database, ':memory:')) {
            return;
        }

        $directory = dirname($database);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (! file_exists($database)) {
            touch($database);
        }
    }
}
