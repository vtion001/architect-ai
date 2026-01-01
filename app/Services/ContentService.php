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
        $tone = $options['tone'] ?? 'Default Tone';
        $length = $options['length'] ?? 'Default Length';
        $count = $options['count'] ?? 1;
        $cta = $options['cta'] ?? '';
        $hashtags = ($options['includeHashtags'] ?? false) ? "Include relevant hashtags." : "Do not include hashtags.";
        $lineBreaks = ($options['addLineBreaks'] ?? true) ? "Use generous line breaks for readability." : "Use standard paragraph spacing.";

        $systemPrompt = "You are an expert content creator and brand strategist. Your goal is to generate high-quality, engaging content that sounds human and professional.
                         CONTENT TYPE: $type
                         TONE: $tone
                         LENGTH: $length
                         
                         DIRECTIONS:
                         - Maintain a consistent, authoritative brand voice ($tone).
                         - Use appropriate formatting (Markdown) with headers, lists, and bold text.
                         - $lineBreaks
                         - $hashtags
                         - Ensure the content is SEO-optimized.
                         - Aim for a clear introduction, structured body, and call-to-action.
                         " . ($cta ? "MANDATORY CALL TO ACTION: $cta" : "");

        $userPrompt = "Please generate $count unique $type(s) about: $topic. \nContext: $context \nDesired Length: $length \nDesired Tone: $tone";
        
        if ($count > 1) {
            $userPrompt .= "\n\nPlease format the output as a numbered list of posts, clearly separated.";
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
        // Placeholder for Banana Pro or specialized image generation
        // For now, returning null or a generic high-end placeholder URL if needed
        return null; 
    }
}
