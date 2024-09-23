<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class AuthService
{
    public function register(Request $request)
    {
        $authMsUrl = env('AUTH_MS_URL', 'http://localhost:8001');
        $credentials = $request->only('name', 'email', 'password');

        try {
            $response = Http::post("$authMsUrl/api/register", $credentials);

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'Registration failed', 'details' => $response->body()];

        } catch (\Exception $e) {
            return ['error' => 'Registration failed', 'exception' => $e->getMessage()];
        }
    }

    public function login(Request $request)
    {
        $authMsUrl = env('AUTH_MS_URL', 'http://localhost:8001');
        $credentials = [
            'grant_type' => 'password',
            'client_id' => env('PASSPORT_PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSPORT_PASSWORD_CLIENT_SECRET'),
            'username' => $request->email,
            'password' => $request->password,
            'scope' => ''
        ];

        try {
            $response = Http::post("$authMsUrl/oauth/token", $credentials);

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'Login failed', 'details' => $response->body()];

        } catch (\Exception $e) {
            return ['error' => 'Login failed', 'exception' => $e->getMessage()];
        }
    }
}
