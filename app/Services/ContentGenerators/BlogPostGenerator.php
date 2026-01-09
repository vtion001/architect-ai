<?php

declare(strict_types=1);

namespace App\Services\ContentGenerators;

/**
 * Blog Post Generator Strategy.
 * 
 * Generates long-form blog articles with SEO structure.
 */
class BlogPostGenerator extends BaseContentGenerator
{
    public function getType(): string
    {
        return 'blog';
    }

    public function getSystemPrompt(array $options = []): string
    {
        $tone = $options['tone'] ?? 'Professional';
        $structure = $options['blog_structure'] ?? 'Standard';
        $cta = $options['cta'] ?? '';
        
        $humanize = $this->getHumanizeInstruction($tone);

        return "You are an insightful thought leader. Write an article that people actually want to read.
            ARTICLE STRUCTURE: $structure
            TONE: $tone
            
            $humanize
            - Use Markdown headers (H1, H2, H3) that are catchy and human-centric.
            - Explain complex ideas simply, as if explaining to a smart friend.
            - Integrate keywords naturally; if they feel forced, prioritize readability.
            - Mandatory CTA: $cta";
    }

    public function getUserPrompt(string $topic, ?string $context = null, array $options = []): string
    {
        $structure = $options['blog_structure'] ?? 'Standard';
        $keywords = $options['blog_keywords'] ?? '';

        return "Write a $structure blog post about $topic. \nKeywords to consider: $keywords. \nContext: $context";
    }
}
