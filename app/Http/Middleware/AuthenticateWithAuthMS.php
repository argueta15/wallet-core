<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class AuthenticateWithAuthMS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        $authMsUrl = env('AUTH_MS_URL', 'http://localhost:8001');

        try {
            $response = Http::withToken($token)->get("$authMsUrl/api/validate-token");
            $userData = $response->json();
            $request->merge(['user' => collect($userData['user'])]);

            if ($response->successful()) {
                return $next($request);
            }

            return response()->json(['error' => 'Invalid token'], 401);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Authentication failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
