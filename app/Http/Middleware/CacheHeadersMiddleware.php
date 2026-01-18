<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Cache Headers Middleware
 * 
 * Sets appropriate cache headers for different asset types.
 * This addresses Lighthouse's "Use efficient cache lifetimes" diagnostic.
 * 
 * Cache Strategy:
 * - Static assets (versioned): 1 year (immutable)
 * - Images: 30 days
 * - API responses: no-cache or short TTL
 * - HTML pages: no-store (for dynamic content)
 */
class CacheHeadersMiddleware
{
    /**
     * Static asset patterns that should be cached long-term.
     * These are typically versioned by Vite and are safe to cache indefinitely.
     */
    protected array $longCachePatterns = [
        '/build/',
        '/assets/',
        '.woff2',
        '.woff',
        '.ttf',
    ];

    /**
     * Medium cache patterns (images, etc.)
     */
    protected array $mediumCachePatterns = [
        '/storage/',
        '.png',
        '.jpg',
        '.jpeg',
        '.gif',
        '.svg',
        '.webp',
        '.avif',
        '.ico',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip for binary file responses (already handled)
        if ($response instanceof BinaryFileResponse) {
            return $response;
        }

        $path = $request->getPathInfo();
        $contentType = $response->headers->get('Content-Type', '');

        // Check for versioned static assets (hash in filename)
        if ($this->isVersionedAsset($path)) {
            return $this->setLongCache($response);
        }

        // Check for static assets
        if ($this->matchesPatterns($path, $this->longCachePatterns)) {
            return $this->setLongCache($response);
        }

        // Check for images/media
        if ($this->matchesPatterns($path, $this->mediumCachePatterns)) {
            return $this->setMediumCache($response);
        }

        // API responses
        if (str_starts_with($path, '/api/')) {
            return $this->setApiCache($response);
        }

        // HTML pages - no cache for dynamic content
        if (str_contains($contentType, 'text/html')) {
            return $this->setNoCache($response);
        }

        return $response;
    }

    /**
     * Check if the asset path contains a version hash (from Vite).
     */
    protected function isVersionedAsset(string $path): bool
    {
        // Vite generates filenames like: app-abc123.js
        return (bool) preg_match('/\.[a-f0-9]{8,}\.(js|css|woff2?|ttf)$/', $path);
    }

    /**
     * Check if path matches any of the given patterns.
     */
    protected function matchesPatterns(string $path, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if (str_contains($path, $pattern)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set long cache headers (1 year, immutable).
     */
    protected function setLongCache(Response $response): Response
    {
        $response->headers->set('Cache-Control', 'public, max-age=31536000, immutable');
        return $response;
    }

    /**
     * Set medium cache headers (30 days).
     */
    protected function setMediumCache(Response $response): Response
    {
        $response->headers->set('Cache-Control', 'public, max-age=2592000'); // 30 days
        return $response;
    }

    /**
     * Set API cache headers.
     */
    protected function setApiCache(Response $response): Response
    {
        // Most API responses should not be cached
        // Exception: list endpoints that rarely change
        $response->headers->set('Cache-Control', 'no-cache, must-revalidate');
        return $response;
    }

    /**
     * Set no-cache headers for HTML pages.
     */
    protected function setNoCache(Response $response): Response
    {
        $response->headers->set('Cache-Control', 'no-store, must-revalidate');
        return $response;
    }
}
