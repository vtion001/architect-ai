<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResearchService
{
    private ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.perplexity.key');
    }

    /**
     * Perform deep research on a topic using Perplexity API.
     */
    public function performResearch(string $topic): string
    {
        if (!$this->apiKey) {
            return "Research for: $topic (Perplexity API key not configured).";
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->post('https://api.perplexity.ai/chat/completions', [
                    'model' => 'llama-3.1-sonar-small-128k-online',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a deep research assistant. Provide detailed, factual, and up-to-date information on the requested topic. Use bullet points and clear sections.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Perform a deep research on: $topic"
                        ],
                    ],
                    'max_tokens' => 2000,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content') ?? '';
            }

            Log::error('Perplexity API Error: ' . $response->body());
            return "Research failed for: $topic. API Error.";
        } catch (\Exception $e) {
            Log::error('Research Exception: ' . $e->getMessage());
            return "Research failed for: $topic. Technical error.";
        }
    }
}
