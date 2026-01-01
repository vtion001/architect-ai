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
                ->timeout(120) // Deep research takes time
                ->post('https://api.perplexity.ai/chat/completions', [
                    'model' => 'llama-3.1-sonar-large-128k-online',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a professional research analyst. Your goal is to perform a broad and deep search across at least 15-20 distinct high-quality sources (news, industry reports, official data, and specialized publications). Provide a comprehensive, data-heavy, and factual report. Include specific numbers, dates, market trends, and competitive insights. Avoid generic filler; prioritize hard data points.'
                        ],
                        [
                            'role' => 'user',
                            'content' => "Perform an exhaustive deep research on: $topic. Ensure you explore a wide breadth of sources (aiming for 20 unique sources) to provide the most detailed and factual analysis possible."
                        ],
                    ],
                    'max_tokens' => 4000,
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
