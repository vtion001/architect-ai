<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Compression Middleware
 *
 * Enables gzip/deflate compression for HTML responses.
 * This addresses Lighthouse's "No compression applied" diagnostic.
 *
 * Note: In production, it's better to enable compression at the
 * web server level (Nginx/Apache) for better performance.
 * This middleware is a fallback for environments where
 * server-level compression isn't available.
 */
class CompressionMiddleware
{
    /**
     * Minimum content length to compress (in bytes).
     * Small responses don't benefit from compression.
     */
    protected const MIN_COMPRESS_SIZE = 1024; // 1KB

    /**
     * MIME types that should be compressed.
     */
    protected const COMPRESSIBLE_TYPES = [
        'text/html',
        'text/plain',
        'text/css',
        'text/javascript',
        'application/javascript',
        'application/json',
        'application/xml',
        'text/xml',
        'image/svg+xml',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip if not compressible
        if (! $this->shouldCompress($request, $response)) {
            return $response;
        }

        $content = $response->getContent();

        // Skip if content is not a string or is empty
        if (! is_string($content) || strlen($content) === 0) {
            return $response;
        }

        // Skip small responses
        if (strlen($content) < self::MIN_COMPRESS_SIZE) {
            return $response;
        }

        // Get preferred encoding
        $encoding = $this->getPreferredEncoding($request);

        if (! $encoding) {
            return $response;
        }

        // Compress the content
        $compressedContent = $this->compress($content, $encoding);

        if ($compressedContent === false) {
            return $response;
        }

        // Only use compressed content if it's actually smaller
        if (strlen($compressedContent) >= strlen($content)) {
            return $response;
        }

        $response->setContent($compressedContent);
        $response->headers->set('Content-Encoding', $encoding);
        $response->headers->set('Vary', 'Accept-Encoding');
        $response->headers->remove('Content-Length'); // Let the server calculate

        return $response;
    }

    /**
     * Check if the response should be compressed.
     */
    protected function shouldCompress(Request $request, Response $response): bool
    {
        // Don't compress if already encoded
        if ($response->headers->has('Content-Encoding')) {
            return false;
        }

        // Don't compress non-2xx responses
        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            return false;
        }

        // Check content type
        $contentType = $response->headers->get('Content-Type', '');

        foreach (self::COMPRESSIBLE_TYPES as $type) {
            if (str_contains($contentType, $type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the client's preferred compression encoding.
     */
    protected function getPreferredEncoding(Request $request): ?string
    {
        $acceptEncoding = $request->header('Accept-Encoding', '');

        // Prefer gzip (most widely supported)
        if (str_contains($acceptEncoding, 'gzip') && function_exists('gzencode')) {
            return 'gzip';
        }

        // Fallback to deflate
        if (str_contains($acceptEncoding, 'deflate') && function_exists('gzdeflate')) {
            return 'deflate';
        }

        return null;
    }

    /**
     * Compress content using the specified encoding.
     */
    protected function compress(string $content, string $encoding): string|false
    {
        return match ($encoding) {
            'gzip' => gzencode($content, 6), // Level 6 is a good balance
            'deflate' => gzdeflate($content, 6),
            default => false,
        };
    }
}
