<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('api')->user();

        if ($user && $user->isAdmin()) {
            return $next($request);
        }

        return response()->json(['error' => 'Unauthorized. Admin role required.'], 403);
    }
}
