<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Authenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if ($user->is_active == 1) {
            return $next($request);
        } else if ($user->is_active == 0) {
            return response()->json([
                'error' => 'Silahkan melengkapi detail identitas anda!'
            ], 401);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}
