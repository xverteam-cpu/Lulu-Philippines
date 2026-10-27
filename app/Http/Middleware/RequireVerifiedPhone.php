<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireVerifiedPhone
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (
            ! $user
            || $user->phone_verified_at
            || $request->routeIs('phone.capture', 'phone.store', 'logout', 'activity.heartbeat')
        ) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Verify your mobile number to continue.',
                'redirect_url' => route('phone.capture'),
            ], 403);
        }

        return redirect()->route('phone.capture');
    }
}
