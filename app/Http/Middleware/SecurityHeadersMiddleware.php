<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $csp = "default-src 'self'; " .
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.tailwindcss.com unpkg.com fonts.bunny.net cdn.jsdelivr.net http://localhost:* https://localhost:* *.ngrok-free.app; " .
               "script-src-elem 'self' 'unsafe-inline' cdn.tailwindcss.com unpkg.com fonts.bunny.net cdn.jsdelivr.net http://localhost:* https://localhost:* *.ngrok-free.app; " .
               "style-src 'self' 'unsafe-inline' fonts.bunny.net fonts.googleapis.com http://localhost:* https://localhost:* *.ngrok-free.app; " .
               "style-src-elem 'self' 'unsafe-inline' fonts.bunny.net fonts.googleapis.com http://localhost:* https://localhost:* *.ngrok-free.app; " .
               "img-src 'self' data: blob: i.pravatar.cc gravatar.com *.gravatar.com images.unsplash.com res.cloudinary.com *.cloudinary.com *.blob.core.windows.net *.fbcdn.net *.facebook.com *.instagram.com *.ngrok-free.app; " .
               "media-src 'self' blob: res.cloudinary.com *.cloudinary.com *.blob.core.windows.net *.ngrok-free.app; " .
                "font-src 'self' data: fonts.bunny.net fonts.gstatic.com; " .
               "connect-src 'self' unpkg.com res.cloudinary.com *.cloudinary.com *.ngrok-free.app cdn.jsdelivr.net http://localhost:* https://localhost:* ws://localhost:* wss://localhost:* wss://*.ngrok-free.app; " .
               "frame-ancestors 'none';";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}