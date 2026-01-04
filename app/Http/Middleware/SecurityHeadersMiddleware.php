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
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' unpkg.com fonts.bunny.net http://*:5173 https://*:5173; " .
               "script-src-elem 'self' 'unsafe-inline' unpkg.com fonts.bunny.net http://*:5173 https://*:5173; " .
               "style-src 'self' 'unsafe-inline' fonts.bunny.net fonts.googleapis.com http://*:5173 https://*:5173; " .
               "style-src-elem 'self' 'unsafe-inline' fonts.bunny.net fonts.googleapis.com http://*:5173 https://*:5173; " .
               "img-src 'self' data: i.pravatar.cc images.unsplash.com res.cloudinary.com *.fbcdn.net *.facebook.com *.instagram.com; " .
               "font-src 'self' data: fonts.bunny.net fonts.gstatic.com; " .
               "connect-src 'self' unpkg.com *.ngrok-free.app http://*:5173 https://*:5173 ws://*:5173 wss://*:5173; " .
               "frame-ancestors 'none';";

        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }
}