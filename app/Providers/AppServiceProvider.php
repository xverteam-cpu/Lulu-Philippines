<?php

namespace App\Providers;

use App\Models\IdentityVerification;
use App\Models\Investment;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer('partials.admin-nav', function ($view): void {
            $pendingRequests = ['verifications' => 0, 'withdrawals' => 0, 'deposits' => 0];

            if (Auth::user()?->is_admin) {
                $pendingRequests = [
                    'verifications' => IdentityVerification::query()->where('status', 'pending')->count(),
                    'withdrawals' => Withdrawal::query()->where('status', 'pending')->count(),
                    'deposits' => Investment::query()->where('status', 'pending')->count(),
                ];
            }

            $view->with('pendingAdminRequests', $pendingRequests);
        });

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
