<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
    }

    public function generateText(string $topic, string $type, ?string $context = null, array $options = []): string
    {
        $generator = $options['generator'] ?? 'post';
        $tone = $options['tone'] ?? 'Professional';
        $cta = $options['cta'] ?? '';
        $lineBreaks = ($options['addLineBreaks'] ?? true) ? "Use generous line breaks for readability." : "Use standard spacing.";
        $hashtags = ($options['includeHashtags'] ?? false) ? "Include relevant hashtags." : "";

        if ($generator === 'video') {
            $platform = $options['video_platform'] ?? 'reels';
            $hook = $options['video_hook'] ?? 'Problem/Solution';
            $duration = $options['video_duration'] ?? '60s';

            $systemPrompt = "You are an expert video scriptwriter. Create a viral-ready script for $platform.
                             HOOK STYLE: $hook
                             TARGET DURATION: $duration
                             PLATFORM: $platform
                             
                             STRICT GUIDELINES:
                             - Start with a high-impact hook using the $hook style.
                             - Provide clear visual cues in brackets [Script: Visual Cue].
                             - Keep the language punchy and suitable for $platform.
                             - Include a strong call to action at the end: $cta";
            
            $userPrompt = "Write a $duration video script about $topic. Context: $context";
        } elseif ($generator === 'blog') {
            $keywords = $options['blog_keywords'] ?? '';
            $structure = $options['blog_structure'] ?? 'Standard';

            $systemPrompt = "You are a senior SEO content strategist and technical writer. 
                             ARTICLE STRUCTURE: $structure
                             TARGET KEYWORDS: $keywords
                             TONE: $tone
                             
                             STRICT GUIDELINES:
                             - Use Markdown headers (H1, H2, H3) for structure.
                             - Ensure technical depth and professional authority.
                             - Seamlessly integrate keywords.
                             - Provide a clear summary and next steps.
                             - Mandatory CTA: $cta";
            
            $userPrompt = "Write a comprehensive $structure blog post about $topic. \nKeywords to include: $keywords. \nContext: $context";
        } else {
            // Default: Post Generator
            $count = $options['count'] ?? 1;
            $length = $options['length'] ?? 'Standard';
            
            $systemPrompt = "You are an expert social media and content architect.
                             TONE: $tone
                             FORMAT: $type
                             
                             STRICT GUIDELINES:
                             - $lineBreaks
                             - $hashtags
                             - $tone brand voice.
                             - Clear, actionable structure.
                             - Mandatory CTA: $cta";
            
            $userPrompt = "Generate $count unique $type(s) about: $topic. \nContext: $context \nTone: $tone \nLength: $length";
            if ($count > 1) {
                $userPrompt .= "\nFormat as a numbered list of distinct options.";
            }
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $this->model,
                    'messages' => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user', 'content' => $userPrompt],
                    ],
                    'max_tokens' => 2500,
                ]);

            if ($response->failed()) {
                throw new \Exception("AI Generation failed: " . $response->body());
            }

            return $response->json('choices.0.message.content');
        } catch (\Exception $e) {
            Log::error("Content generation error: " . $e->getMessage());
            throw $e;
        }
    }

    public function generateImage(string $prompt): ?string
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/images/generations', [
                    'model' => 'dall-e-3',
                    'prompt' => "A professional, high-quality, photorealistic image for a social media post about: $prompt. Style: Modern, Architectural, Clean.",
                    'n' => 1,
                    'size' => '1024x1024',
                ]);

            if ($response->successful()) {
                return $response->json('data.0.url');
            }
            
            Log::error("Image generation failed: " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("Image generation exception: " . $e->getMessage());
            return null;
        }
    }
}
