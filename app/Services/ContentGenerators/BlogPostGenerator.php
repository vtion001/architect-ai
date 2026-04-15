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
        $brandTone = $options['brand_tone'] ?? '';

        $humanize = $this->getHumanizeInstruction($tone);
        $brandInstruction = $brandTone ? "Adopt this brand voice: $brandTone." : '';

        return "You are an expert content writer and SEO specialist. Write a comprehensive, high-ranking blog article.
            
            CONFIGURATION:
            - Structure: $structure
            - Tone: $tone
            - Goal: High engagement and SEO ranking
            
            $brandInstruction
            
            $humanize
            
            REQUIREMENTS:
            - Use proper Markdown formatting (H1 for title, H2, H3).
            - Include a compelling Meta Description at the very top (labeled 'Meta Description:').
            - Break up text with bullet points and short paragraphs for readability.
            - Include a 'Key Takeaways' section after the intro.
            - Ensure the content is substantive (aim for depth and value).
            - Mandatory Call-to-Action (CTA) at the end: $cta";
    }

    public function getUserPrompt(string $topic, ?string $context = null, array $options = []): string
    {
        $keywords = $options['blog_keywords'] ?? '';
        $angle = $options['angle'] ?? null;
        $focusKeyword = $options['focus_keyword'] ?? '';

        if ($angle) {
            return "Write a blog post about: \"$angle — $topic\".
            Target Keywords: $focusKeyword".($keywords ? ", $keywords" : '').".
            Focus: This article should dive deep into the angle of \"$angle\".
            Specific Context/Mandates: $context.

            Ensure the content is original, substantive (1500+ words), and directly addresses this specific angle of the topic. Use a different structure and perspective than a general overview.";
        }

        return "Write a blog post about: \"$topic\".
        Target Keywords: $keywords.
        Specific Context/Mandates: $context.

        Ensure the content is original, valuable, and directly addresses user intent.";
    }

    public function getAngleExtractionPrompt(string $topic, int $count, string $keywords = ''): string
    {
        return "You are an expert content strategist. Given the topic \"$topic\" and keywords \"$keywords\", generate exactly $count distinct sub-topic angles for creating $count separate, unique blog posts.

Return your response ONLY as valid JSON in this exact format:
```json
[
  {\"angle\": \"The Sub-Topic Title\", \"keyword\": \"primary keyword for this angle\", \"description\": \"One sentence describing what this angle covers\"},
  ... (exactly $count items)
]
```

Requirements:
- Each angle must be genuinely different from the others
- Angles should cover different aspects: types/kinds, how-to steps, common mistakes, best practices, case studies, comparison, beginner guide, advanced tactics, industry trends, etc.
- Each angle should have a distinct primary keyword that differs from the others
- Make angles specific and actionable, not generic
- Output ONLY the JSON array, no explanations or markdown outside the code block";
    }
}
