<?php

declare(strict_types=1);

namespace App\Services\ContentGenerators;

use App\Services\TokenService;

/**
 * Social Media Post Generator Strategy.
 * 
 * Generates engaging social media posts with viral patterns.
 * 
 * TOKEN CONSUMPTION:
 * - Tokens are consumed at the CONTROLLER level (ContentCreatorController)
 * - Single post: TokenService::COSTS['social_post'] = 10 tokens
 * - Batch generation: TokenService::COSTS['content_batch'] = 25 tokens
 * 
 * TENANT ISOLATION:
 * - This service is stateless and doesn't access tenant data directly
 * - All tenant scoping is handled by the calling controller
 * 
 * @see \App\Http\Controllers\ContentCreatorController::store()
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
        $count = (int)($options['count'] ?? 1);
        
        $lineBreaks = ($options['addLineBreaks'] ?? true) 
            ? "Use natural spacing and paragraph breaks for a human-like flow." 
            : "Use standard spacing.";
        $hashtags = ($options['includeHashtags'] ?? false) 
            ? "Include 2-3 relevant hashtags that a human would actually use (no spamming)." 
            : "";
        
        $humanize = $this->getHumanizeInstruction($tone);

        $quantityRule = "";
        if ($count > 1) {
            $quantityRule = "\n- BATCH GENERATION: You are generating EXACTLY $count distinct, unique, and separate pieces of content. Each must be high-quality and different from the others.";
        }

        // Check if we have viral examples
        $examples = $options['viral_examples'] ?? '';
        
        if (!empty($examples)) {
            return "You are a viral content expert. Your task is to generate social media captions.
                
                STRICT GUIDELINES BASED ON THESE HIGH-PERFORMING EXAMPLES:
                $examples
                
                RULES:
                - Follow the patterns, hooks, and engagement styles you see in the successful captions above.
                - Distill the essence of these examples into SHORT, punchy captions.
                - $humanize
                - $lineBreaks
                - $hashtags$quantityRule
                - Mandatory CTA: $cta";
        }

        return "You are a top-tier viral content creator who knows how to stop the scroll.
            GOAL: Create viral posts that are punchy, relatable, and highly shareable.
            TONE: $tone
            LENGTH: Short and punchy
            
            RULES:
            - $humanize
            - Keep the content SHORT and impactful (under 280 chars preferred).
            - $lineBreaks
            - $hashtags$quantityRule
            - Make the first sentence a compelling personal hook or pattern interrupt.
            - Mandatory CTA: $cta";
    }

    public function getUserPrompt(string $topic, ?string $context = null, array $options = []): string
    {
        $count = (int)($options['count'] ?? 1);
        $type = $options['type'] ?? 'social media post';
        $tone = $options['tone'] ?? 'Professional';
        $length = $options['length'] ?? 'Short, concise, and engaging';

        // Normalize type
        if ($type === 'social-media') {
            $type = 'social media post';
        }
        if ($type === 'blog-post') {
            $type = 'social-media post';
        }

        $prompt = "TASK: Generate EXACTLY $count distinct and unique $type(s) about the topic: \"$topic\".\n";
        
        if ($count > 1) {
            $prompt .= "\nCRITICAL FORMATTING RULE:\n";
            $prompt .= "Separate each distinct $type with exactly three dashes '---' on their own line.\n";
            $prompt .= "Example format for multiple items:\n";
            $prompt .= "Content for item 1\n---\nContent for item 2\n---\n... and so on until item $count\n\n";
            $prompt .= "STRICT ADHERENCE: Do not add any introductory text, titles, or concluding remarks. Start immediately with the first item. Total number of items must be exactly $count.";
        }
        
        $prompt .= "\n\nADDITIONAL CONTEXT: $context \nTONE: $tone \nLENGTH: $length.\n";

        if ($count > 1) {
            $prompt .= "\nFINAL QUANTITY COMMAND: You MUST generate EXACTLY $count distinct items. Do not stop until you have produced $count separate posts separated by '---'.";
        }

        return $prompt;
    }

    /**
     * Generate content using this strategy.
     */
    public function generate(string $topic, ?string $context = null, array $options = []): string
    {
        $count = (int)($options['count'] ?? 1);
        
        // Upgrade to high-tier model for batch generation to ensure instruction following
        if ($count > 1) {
            $options['model'] = 'gpt-4o';
        }

        return parent::generate($topic, $context, $options);
    }
}
