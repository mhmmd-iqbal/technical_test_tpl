<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$role)
    {
        if ($request->user() && in_array($request->user()->role, $role)) {
            return $next($request);
        }

        return $request->expectsJson()
            ? response()->json(['message' => 'Unauthorized'], 401)
            : redirect()->route('login');
    }
}

