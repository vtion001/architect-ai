<?php

declare(strict_types=1);

namespace App\Helpers;

/**
 * Cloudinary Image Optimizer
 * 
 * Automatically transforms Cloudinary URLs to include:
 * - Responsive sizing
 * - Modern format (WebP/AVIF with fallback)
 * - Quality optimization
 * - Lazy loading attributes
 * 
 * This addresses Lighthouse's "Improve image delivery" diagnostic.
 */
class CloudinaryOptimizer
{
    /**
     * Default quality setting (1-100)
     */
    protected const DEFAULT_QUALITY = 80;

    /**
     * Cloudinary base URL pattern
     */
    protected const CLOUDINARY_PATTERN = '/res\.cloudinary\.com\/([^\/]+)\/image\/upload/';

    /**
     * Optimize a Cloudinary URL for responsive delivery.
     * 
     * @param string $url Original Cloudinary URL
     * @param int|null $width Desired width
     * @param int|null $height Desired height
     * @param array $options Additional options
     * @return string Optimized URL
     */
    public static function optimize(
        string $url, 
        ?int $width = null, 
        ?int $height = null,
        array $options = []
    ): string {
        // If not a Cloudinary URL, return as-is
        if (!preg_match(self::CLOUDINARY_PATTERN, $url)) {
            return $url;
        }

        // Build transformation string
        $transforms = [];

        // Auto format (WebP for supported browsers, PNG/JPG fallback)
        $transforms[] = 'f_auto';

        // Auto quality (Cloudinary's intelligent quality optimization)
        $quality = $options['quality'] ?? self::DEFAULT_QUALITY;
        $transforms[] = "q_{$quality}";

        // Dimensions
        if ($width) {
            $transforms[] = "w_{$width}";
        }
        if ($height) {
            $transforms[] = "h_{$height}";
        }

        // Crop mode
        $crop = $options['crop'] ?? 'fill';
        if ($crop && ($width || $height)) {
            $transforms[] = "c_{$crop}";
        }

        // DPR for retina displays
        if ($options['dpr'] ?? false) {
            $transforms[] = 'dpr_auto';
        }

        // Build transformation string
        $transformString = implode(',', $transforms);

        // Insert transformations into URL
        return preg_replace(
            self::CLOUDINARY_PATTERN,
            "res.cloudinary.com/$1/image/upload/{$transformString}",
            $url
        );
    }

    /**
     * Generate a responsive image with srcset.
     * 
     * @param string $url Original Cloudinary URL
     * @param array $sizes Array of widths for srcset
     * @param array $options Additional options
     * @return array ['src' => string, 'srcset' => string, 'sizes' => string]
     */
    public static function responsive(
        string $url, 
        array $sizes = [320, 640, 768, 1024, 1280],
        array $options = []
    ): array {
        $srcset = [];
        
        foreach ($sizes as $width) {
            $optimizedUrl = self::optimize($url, $width, null, $options);
            $srcset[] = "{$optimizedUrl} {$width}w";
        }

        // Default size (largest)
        $defaultWidth = max($sizes);
        
        return [
            'src' => self::optimize($url, $defaultWidth, null, $options),
            'srcset' => implode(', ', $srcset),
            'sizes' => $options['sizes'] ?? '(max-width: 640px) 100vw, 50vw',
        ];
    }

    /**
     * Generate a picture element with WebP and fallback.
     * 
     * @param string $url Original Cloudinary URL
     * @param int|null $width Display width
     * @param int|null $height Display height
     * @param array $options Additional options
     * @return string HTML picture element
     */
    public static function picture(
        string $url, 
        ?int $width = null, 
        ?int $height = null,
        array $options = []
    ): string {
        $alt = htmlspecialchars($options['alt'] ?? '', ENT_QUOTES);
        $class = htmlspecialchars($options['class'] ?? '', ENT_QUOTES);
        $loading = $options['loading'] ?? 'lazy';

        // WebP version
        $webpUrl = self::optimize($url, $width, $height, array_merge($options, ['format' => 'webp']));
        
        // AVIF version (best compression)
        $avifUrl = preg_replace('/f_auto/', 'f_avif', self::optimize($url, $width, $height, $options));
        
        // Fallback (auto format will use PNG/JPG)
        $fallbackUrl = self::optimize($url, $width, $height, $options);

        $widthAttr = $width ? "width=\"{$width}\"" : '';
        $heightAttr = $height ? "height=\"{$height}\"" : '';

        return <<<HTML
<picture>
    <source srcset="{$avifUrl}" type="image/avif">
    <source srcset="{$webpUrl}" type="image/webp">
    <img src="{$fallbackUrl}" alt="{$alt}" class="{$class}" loading="{$loading}" {$widthAttr} {$heightAttr}>
</picture>
HTML;
    }

    /**
     * Optimize URL for favicon/icon usage.
     * 
     * @param string $url Original URL
     * @param int $size Icon size
     * @return string Optimized URL
     */
    public static function favicon(string $url, int $size = 32): string
    {
        return self::optimize($url, $size, $size, [
            'quality' => 90,
            'crop' => 'fill',
        ]);
    }

    /**
     * Optimize URL for avatar/profile pictures.
     * 
     * @param string $url Original URL
     * @param int $size Avatar size
     * @return string Optimized URL
     */
    public static function avatar(string $url, int $size = 48): string
    {
        return self::optimize($url, $size, $size, [
            'quality' => 85,
            'crop' => 'fill',
        ]);
    }

    /**
     * Get blur placeholder for progressive loading.
     * 
     * @param string $url Original URL
     * @return string Tiny blurred placeholder URL
     */
    public static function placeholder(string $url): string
    {
        return self::optimize($url, 20, null, [
            'quality' => 30,
        ]) . ',e_blur:1000';
    }
}
