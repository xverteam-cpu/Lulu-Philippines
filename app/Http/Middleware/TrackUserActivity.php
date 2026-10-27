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
}
