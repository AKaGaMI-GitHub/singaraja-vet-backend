<?php

namespace App\Http\Middleware;

use App\Http\Helpers\APIHelpers;
use Closure;
use Exception;
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
        try {
            $user = Auth::guard('sanctum')->user();
            if ($user->is_active == 1) {
                return $next($request);
            } else if ($user->is_active == 0) {
                return response()->json([
                    'error' => 'Silahkan melengkapi detail identitas anda!'
                ], 401);
            } else {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (Exception $error) {
            return APIHelpers::responseAPI(['message' => 'Unauthroized', 'error' => $error->getMessage()], 401);
        }
    }
}
