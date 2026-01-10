<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IpBlocker
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!config('app.enable_ip_blocking', true)) {
            return $next($request);
        }

        $ip = $request->ip();
        $blockKey = 'blocked_ip:' . $ip;
        $suspiciousKey = 'suspicious_ip:' . $ip;

        // Check if IP is blocked
        if (Cache::has($blockKey)) {
            $blockedUntil = Cache::get($blockKey);
            
            Log::warning('Blocked IP attempted access', [
                'ip' => $ip,
                'blocked_until' => $blockedUntil,
                'path' => $request->path(),
            ]);

            return response()->json([
                'message' => 'Access temporarily blocked due to suspicious activity',
                'retry_after' => $blockedUntil,
            ], 429);
        }

        // Track suspicious activity (failed auth attempts, etc.)
        $suspiciousCount = Cache::get($suspiciousKey, 0);
        
        if ($suspiciousCount >= 10) {
            $blockDuration = config('app.block_duration', 3600); // seconds
            $blockedUntil = now()->addSeconds($blockDuration);
            
            Cache::put($blockKey, $blockedUntil, $blockDuration);
            Cache::forget($suspiciousKey);
            
            Log::warning('IP blocked due to suspicious activity', [
                'ip' => $ip,
                'suspicious_count' => $suspiciousCount,
                'blocked_until' => $blockedUntil,
            ]);

            return response()->json([
                'message' => 'Access blocked due to suspicious activity',
                'retry_after' => $blockedUntil,
            ], 429);
        }

        return $next($request);
    }

    /**
     * Mark IP as suspicious
     */
    public static function markSuspicious(string $ip): void
    {
        $key = 'suspicious_ip:' . $ip;
        $count = Cache::get($key, 0);
        Cache::put($key, $count + 1, now()->addHours(1));
    }
}