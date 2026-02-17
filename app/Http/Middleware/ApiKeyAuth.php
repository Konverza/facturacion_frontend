<?php

namespace App\Http\Middleware;

use App\Models\Business;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $authHeader = $request->header('Authorization');
        if (!$authHeader) {
            return response()->json([
                'success' => false,
                'message' => 'Missing Authorization header.'
            ], 401);
        }

        $token = trim($authHeader);
        if (stripos($token, 'Bearer ') === 0) {
            $token = trim(substr($token, 7));
        } elseif (stripos($token, 'ApiKey ') === 0) {
            $token = trim(substr($token, 7));
        }

        if ($token === '') {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API key.'
            ], 401);
        }

        $tokenHash = hash('sha256', $token);
        $business = Business::where('api_key_hash', $tokenHash)->first();

        if (!$business || !$business->has_api_access) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.'
            ], 401);
        }

        $request->attributes->set('api_business', $business);
        Session::put('business', $business->id);

        return $next($request);
    }
}
