<?php

namespace App\Http\Middleware;

use App\Models\MobileAccessToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateMobileToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearer = $request->bearerToken();

        if (!$bearer) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        $token = MobileAccessToken::with('user.role')
            ->where('token_hash', hash('sha256', $bearer))
            ->first();

        if (!$token || !$token->user) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid access token.',
            ], 401);
        }

        if ($token->user->status !== 'active') {
            $token->delete();

            return response()->json([
                'success' => false,
                'message' => 'Account is inactive.',
            ], 403);
        }

        if ($token->expires_at && $token->expires_at->isPast()) {
            $token->delete();

            return response()->json([
                'success' => false,
                'message' => 'Access token expired.',
            ], 401);
        }

        $token->forceFill(['last_used_at' => now()])->save();

        Auth::setUser($token->user);
        $request->attributes->set('mobile_access_token', $token);

        return $next($request);
    }
}
