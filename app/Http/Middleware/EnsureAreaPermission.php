<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAreaPermission
{
    public function handle(Request $request, Closure $next, string $area, string $level = 'visible')
    {
        $user = $request->user();

        abort_unless($user && $user->hasPermission($area, $level), 403);

        return $next($request);
    }
}
