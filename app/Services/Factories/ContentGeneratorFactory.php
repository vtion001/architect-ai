<?php

declare(strict_types=1);

namespace App\Services\Factories;

use App\Contracts\ContentGeneratorInterface;
use App\Services\ContentGenerators\BlogPostGenerator;
use App\Services\ContentGenerators\SocialPostGenerator;
use App\Services\ContentGenerators\VideoScriptGenerator;
use InvalidArgumentException;

/**
 * Factory for creating content generators.
 * 
 * Follows the Factory Pattern to decouple creation logic from business logic.
 */
class ContentGeneratorFactory
{
    /**
     * Get the appropriate generator instance based on type.
     *
     * @param string $type The generator type (post, blog, video)
     * @return ContentGeneratorInterface
     * @throws InvalidArgumentException
     */
    public function make(string $type): ContentGeneratorInterface
    {
        return match ($type) {
            'blog' => app(BlogPostGenerator::class),
            'video' => app(VideoScriptGenerator::class),
            'post', 'social-media post' => app(SocialPostGenerator::class),
            default => app(SocialPostGenerator::class), // Default fallback
        };
    }
}
