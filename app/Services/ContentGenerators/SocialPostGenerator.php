<?php

declare(strict_types=1);

namespace App\Services\ContentGenerators;

/**
 * Social Media Post Generator Strategy.
 * 
 * Generates engaging social media posts with viral patterns.
 */
class SocialPostGenerator extends BaseContentGenerator
{
    public function getType(): string
    {
        return 'post';
    }

    public function getSystemPrompt(array $options = []): string
    {
        $tone = $options['tone'] ?? 'Professional';
        $cta = $options['cta'] ?? '';
        $lineBreaks = ($options['addLineBreaks'] ?? true) 
            ? "Use natural spacing and paragraph breaks for a human-like flow." 
            : "Use standard spacing.";
        $hashtags = ($options['includeHashtags'] ?? false) 
            ? "Include 2-3 relevant hashtags that a human would actually use (no spamming)." 
            : "";
        
        $humanize = $this->getHumanizeInstruction($tone);

        // Check if we have viral examples
        $examples = $options['viral_examples'] ?? '';
        
        if (!empty($examples)) {
            return "You are a viral content expert.
                BASED ON THESE HIGH-PERFORMING EXAMPLES:
                
                $examples
                
                Generate a new caption about the given topic with a $tone tone.
                Follow the patterns, hooks, and engagement styles you see in the successful captions above.
                IMPORTANT: Distill the essence of these examples into a SHORT, punchy caption.
                
                $humanize
                - $lineBreaks
                - $hashtags
                - Mandatory CTA: $cta";
        }

        return "You are a top-tier viral content creator who knows how to stop the scroll.
            GOAL: Create a viral post that is punchy, relatable, and highly shareable.
            TONE: $tone
            LENGTH: Short and punchy
            
            $humanize
            - Keep the content SHORT and impactful (under 280 chars preferred).
            - $lineBreaks
            - $hashtags
            - Make the first sentence a compelling personal hook or pattern interrupt.
            - Mandatory CTA: $cta";
    }

    public function getUserPrompt(string $topic, ?string $context = null, array $options = []): string
    {
        $count = $options['count'] ?? 1;
        $type = $options['type'] ?? 'social-media post';
        $tone = $options['tone'] ?? 'Professional';
        $length = $options['length'] ?? 'Short, concise, and engaging';

        // Normalize type
        if ($type === 'blog-post') {
            $type = 'social-media post';
        }

        $prompt = "Create $count $type(s) about: $topic. \nContext: $context \nTone: $tone \nLength: $length. \nConstraint: Keep it short, engaging, and to the point. No fluff. Do NOT number the outputs.";
        
        if ($count > 1) {
            $prompt .= "\nRespond with distinct options separated STRICTLY by '---' (triple dash) on its own line. Ensure there are exactly $count distinct options.";
        }

        return $prompt;
    }
}
