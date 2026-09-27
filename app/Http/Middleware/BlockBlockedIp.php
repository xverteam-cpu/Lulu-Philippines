<?php

namespace App\Http\Middleware;

use App\Models\BlockedIp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockBlockedIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $ipAddress = $request->ip();

        if ($ipAddress !== null && BlockedIp::where('ip_address', $ipAddress)->exists()) {
            abort(403, 'Access to this website has been blocked from this IP address.');
        }

        return $next($request);
    }
}
