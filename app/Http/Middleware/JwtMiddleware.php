<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
// use Illuminate\Http\Request;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken(); // Get the token from the Authorization header

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        try {
            $publicKey = file_get_contents(storage_path('oauth-public.key'));
            $decoded = JWT::decode($token, new Key($publicKey, 'RS256'));

            // Attach the decoded payload to the request
            $request->attributes->set('user', $decoded);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid or expired token', 'message'=>$e->getMessage(), 'token'=>$token], 401);
        }

        return $next($request);
    }
}
