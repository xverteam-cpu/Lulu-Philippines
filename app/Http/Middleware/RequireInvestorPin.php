<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireInvestorPin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('investor_pin_verified') !== true) {
            return redirect()->route('investors');
        }

        return $next($request);
    }
}
