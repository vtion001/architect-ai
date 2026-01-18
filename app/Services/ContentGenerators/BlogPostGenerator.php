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

        return "You are an expert content writer and SEO specialist. Write a comprehensive, high-ranking blog article.
            
            CONFIGURATION:
            - Structure: $structure
            - Tone: $tone
            - Goal: High engagement and SEO ranking
            
            $humanize
            
            REQUIREMENTS:
            - Use proper Markdown formatting (H1 for title, H2, H3).
            - Include a compelling Meta Description at the very top (labeled 'Meta Description:').
            - Break up text with bullet points and short paragraphs for readability.
            - Include a 'Key Takeaways' section after the intro.
            - Ensure the content is substantive (aim for depth and value).
            - Mandatory Call-to-Action (CTA) at the end: $cta";
    }

    public function getUserPrompt(string $topic, ?string $context = null, array $options = []):
    {
        $keywords = $options['blog_keywords'] ?? '';
        
        return "Write a blog post about: \"$topic\".
        Target Keywords: $keywords.
        Specific Context/Mandates: $context.
        
        Ensure the content is original, valuable, and directly addresses user intent.";
    }
}