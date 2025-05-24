<?php

namespace App\Http\Middleware;

use App\Http\Helpers\APIHelpers;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VetCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = Auth::guard('sanctum')->user();
            if ($user->is_vet == 1) {
                return $next($request);
            } else {
                return response()->json(['message' => 'Unauthroized'], 401);
            }
        } catch (Exception $error) {
            return APIHelpers::responseAPI(['message' => 'Unauthroized', 'error' => $error->getMessage()], 401);
        }
    }
}
