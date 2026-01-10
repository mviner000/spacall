<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SecurityDashboardController extends Controller
{
    /**
     * Show the security dashboard
     */
    public function index(Request $request)
    {
        // Check if already authenticated
        if ($request->session()->get('admin_authenticated')) {
            return view('security-dashboard'); // Ensure this view name matches your blade file
        }
        
        return view('security-dashboard');
    }

    /**
     * Authenticate admin access
     */
    public function authenticate(Request $request)
    {
        // 1. Validate Input
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 2. Static Credentials (for prototyping)
        $adminUsername = 'admin';
        $adminPassword = 'SecurePass123!';

        if ($validated['username'] === $adminUsername && $validated['password'] === $adminPassword) {
            // CRITICAL FIX: Regenerate session ID to prevent fixation and 419 issues
            $request->session()->regenerate();
            
            // Set session flag
            $request->session()->put('admin_authenticated', true);
            
            return response()->json(['success' => true]);
        }

        return response()->json([
            'success' => false, 
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Get system stats
     */
    public function stats(Request $request)
    {
        if (!$request->session()->get('admin_authenticated')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $blockedIps = [];
        $suspiciousIps = [];

        // Get all cache keys (using Laravel's cache store)
        $cacheStore = Cache::getStore();

        // Handle Database Store
        if ($cacheStore instanceof \Illuminate\Cache\DatabaseStore) {
            $prefix = config('cache.prefix') . ':';
            
            // Get blocked IPs
            $blockedKeys = \Illuminate\Support\Facades\DB::table('cache')
                ->where('key', 'like', $prefix . 'blocked_ip:%')
                ->get();

            foreach ($blockedKeys as $row) {
                $ip = str_replace($prefix . 'blocked_ip:', '', $row->key);
                $blockedUntil = unserialize($row->value);
                $blockedIps[] = [
                    'ip' => $ip,
                    'blocked_until' => $blockedUntil,
                ];
            }
            
            // Get suspicious IPs
            $suspiciousKeys = \Illuminate\Support\Facades\DB::table('cache')
                ->where('key', 'like', $prefix . 'suspicious_ip:%')
                ->get();

            foreach ($suspiciousKeys as $row) {
                $ip = str_replace($prefix . 'suspicious_ip:', '', $row->key);
                $count = unserialize($row->value);
                $suspiciousIps[] = [
                    'ip' => $ip,
                    'count' => $count,
                ];
            }
        }

        // Get system info
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . ' MB',
            'memory_limit' => ini_get('memory_limit'),
        ];

        return response()->json([
            'blocked_ips' => $blockedIps,
            'suspicious_ips' => $suspiciousIps,
            'system_info' => $systemInfo,
        ]);
    }

    /**
     * Logout from dashboard
     */
    public function logout(Request $request)
    {
        // Invalidate the session securely
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return response()->json(['success' => true]);
    }
}