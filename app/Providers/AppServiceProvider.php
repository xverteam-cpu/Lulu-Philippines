<?php

namespace App\Providers;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
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
        Event::listen(Saml2LoginEvent::class, function (Saml2LoginEvent $event): void {
            $samlUser = $event->getSaml2User();
            $email = $samlUser->getAttribute('emailAddress')[0] ?? $samlUser->getAttribute('mail')[0] ?? null;
            $name = $samlUser->getAttribute('displayName')[0] ?? $samlUser->getAttribute('cn')[0] ?? null;
            $userId = $samlUser->getUserId();

            if (! is_string($email) || $email === '') {
                return;
            }

            $user = User::where('email', $email)->first();

            if (! $user) {
                $user = User::create([
                    'name' => $name ?? $email,
                    'username' => strtolower(str_replace([' ', '@', '.'], '-', $email)).'-google',
                    'email' => $email,
                    'password' => bcrypt(str()->random(24)),
                    'is_admin' => false,
                ]);
            }

            Auth::login($user);
            request()->session()->regenerate();
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
