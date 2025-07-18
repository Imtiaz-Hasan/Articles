<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class DailyRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $maxAttempts = 1000; // Set your daily limit here
        
        // Generate unique key based on user or IP
        $key = $request->user()
            ? 'daily_user_' . $request->user()->id
            : 'daily_ip_' . $request->ip();
        
        // Set expiration to end of current day
        $expiresAt = now()->endOfDay();
        
        // Get current count, default to 0 if not exists
        $count = Cache::get($key, 0);
        
        // Check if limit exceeded
        if ($count >= $maxAttempts) {
            return response()->json([
                'error' => 'API rate limit exceeded. Try again later.'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }
        
        // Increment count and store with expiration
        Cache::put($key, $count + 1, $expiresAt);
        
        return $next($request);
    }
}
