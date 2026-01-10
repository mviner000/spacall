<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequestSizeLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $maxSize = config('app.max_request_size', 2048); // KB
        $contentLength = $request->header('Content-Length');

        if ($contentLength && $contentLength > ($maxSize * 1024)) {
            return response()->json([
                'message' => 'Request size too large',
                'max_size' => $maxSize . 'KB',
            ], 413);
        }

        return $next($request);
    }
}