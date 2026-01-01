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

    public function generateText(string $topic, string $type, ?string $context = null): string
    {
        $systemPrompt = "You are an expert content creator and brand strategist. Your goal is to generate high-quality, engaging content that sounds human and professional.
                         CONTENT TYPE: $type
                         TOPIC: $topic
                         
                         DIRECTIONS:
                         - Maintain a consistent, authoritative brand voice.
                         - Use appropriate formatting (Markdown) with headers, lists, and bold text.
                         - Ensure the content is SEO-optimized.
                         - If it's a social media post, include relevant hashtags.
                         - If it's a blog post, aim for a clear introduction, structured body, and call-to-action.";

        $userPrompt = "Please generate $type about: $topic. \nContext: $context";

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
