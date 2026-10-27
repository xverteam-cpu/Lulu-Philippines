<?php

namespace App\Http\Middleware;

use App\Support\IpLocationService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user) {
            $ipAddress = (string) $request->ip();
            $attributes = [
                'last_seen_at' => now(),
                'last_ip_address' => $ipAddress,
                'last_device' => $this->detectDevice((string) $request->userAgent()),
            ];
            $locationIsStale = ! $user->ip_location_updated_at
                || $user->ip_location_updated_at->lte(now()->subDay());

            if ($user->last_ip_address !== $ipAddress || $locationIsStale) {
                $attributes['ip_location'] = app(IpLocationService::class)->lookup(
                    $ipAddress,
                    (int) $user->getKey(),
                );
                $attributes['ip_location_updated_at'] = now();
            }

            $user->forceFill($attributes)->save();
        }

        return $next($request);
    }

    private function detectDevice(string $userAgent): string
    {
        if (preg_match('/iPad|Tablet|PlayBook|Silk/i', $userAgent)) {
            return 'Tablet';
        }

        if (preg_match('/iPhone|iPod/i', $userAgent)) {
            return 'iPhone (iOS)';
        }

        if (preg_match('/Android/i', $userAgent)) {
            return preg_match('/Mobile/i', $userAgent) ? 'Android phone' : 'Android tablet';
        }

        if (preg_match('/Windows Phone/i', $userAgent)) {
            return 'Windows phone';
        }

        if (preg_match('/Windows/i', $userAgent)) {
            return 'Windows computer';
        }

        if (preg_match('/Macintosh|Mac OS X/i', $userAgent)) {
            return 'Mac computer';
        }

        if (preg_match('/Linux/i', $userAgent)) {
            return 'Linux computer';
        }

        return 'Unknown device';
    }
}
