<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
{
    $user = $request->user();

    if ($user && $user->role === 'superadmin') {
        return $next($request);
    }

    if ($user && in_array($user->role, $roles)) {
        return $next($request);
    }

    return response()->json(['message' => 'Akses ditolak!'], 403);
}
}
