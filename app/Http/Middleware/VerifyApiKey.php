<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $expectedKey = config('services.pennypilot_api_key');
        $providedKey = $request->header('X-API-Key');

        if (
            !is_string($expectedKey) ||
            $expectedKey === '' ||
            !is_string($providedKey) ||
            !hash_equals($expectedKey, $providedKey)
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        return $next($request);
    }
}
