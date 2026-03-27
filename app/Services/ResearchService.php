<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResearchService
{
    private ?string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct(
        protected KnowledgeBaseService $knowledgeBaseService
    ) {
        $this->apiKey = config('services.minimax.key');
        $this->model = config('services.minimax.model', 'M2.7');
        $this->baseUrl = config('services.minimax.base_url', 'https://api.minimaxi.com/v1');
    }

    /**
     * Perform deep research on a topic using MiniMax.
     */
    public function performResearch(string $topic): string
    {
        if (!$this->apiKey) {
            return "Research for: $topic (MiniMax API key not configured).";
        }

        // 1. RAG: Fetch relevant internal knowledge base assets
        $kbContext = $this->knowledgeBaseService->getContext($topic);
        $enhancedTopic = $topic;
        if ($kbContext) {
            $enhancedTopic .= "\n\nUSE THESE INTERNAL SOURCES AS GROUNDING FOR THE REPORT:\n" . $kbContext;
        }

        $response = \Http::withToken($this->apiKey)
            ->timeout(180)
            ->post($this->baseUrl . '/text/chatcompletion_v2', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are a lead research analyst. Produce an EXHAUSTIVE, 3000+ word deep-dive report."
                    ],
                    [
                        'role' => 'user',
                        'content' => $enhancedTopic
                    ]
                ],
                'max_completion_tokens' => 8000,
                'temperature' => 0.5,
            ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content') ?? '';
        }
        \Log::error('MiniMax research error: ' . $response->body());
        return "Research failed: MiniMax request failed.";
    }

    // Removed OpenAI fallback logic.

    /**
     * RAG: Retrieve relevant context from knowledge base.
     * 
     * @deprecated Use KnowledgeBaseService::getContext() directly. This is a backward-compatible delegate.
     */
    public function getKnowledgeBaseContext(string $topic): ?string
    {
        return $this->knowledgeBaseService->getContext($topic);
    }

    // Removed Gemini model logic.

    /**
     * Generate social media topic suggestions using MiniMax.
     */
    public function suggestSocialMediaTopics(string $topic): string
    {
        if (!$this->apiKey) {
            return "MiniMax API key missing.";
        }

        $response = \Http::withToken($this->apiKey)
            ->timeout(30)
            ->post($this->baseUrl . '/text/chatcompletion_v2', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a social media trend expert.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Generate 5 high-impact, engaging social media post topic ideas derived from the seed: '$topic'.\n\nFORMAT:\n- Provide ONLY a simple bulleted list.\n- Keep titles catchy, concise, and click-worthy.\n- Do not include introductory text or explanations.\n- Focus on viral potential and professional engagement."
                    ]
                ],
                'temperature' => 0.8,
                'max_completion_tokens' => 500,
            ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content') ?? "No suggestions generated.";
        }
        \Log::error('MiniMax suggestion error: ' . $response->body());
        return "Error generating suggestions. Please try again later.";
    }

    /**
     * Refine and rewrite context/mandate for better clarity.
     */
    public function refineContext(string $text): string
    {
        if (!$this->apiKey) {
            return $text; // Return original if no key
        }

        $response = \Http::withToken($this->apiKey)
            ->timeout(30)
            ->post($this->baseUrl . '/text/chatcompletion_v2', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional content editor.'
                    ],
                    [
                        'role' => 'user',
                        'content' => "Rewrite the following context/mandate to be more clear, professional, and impactful.\n\nGOAL: Improve instructions for an AI content generator.\nRULES:\n- Keep the original intent and meaning.\n- Remove ambiguity.\n- Fix grammar and flow.\n- Return ONLY the rewritten text, no explanations.\n\nOriginal Text: '$text'"
                    ]
                ],
                'temperature' => 0.7,
                'max_completion_tokens' => 500,
            ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content') ?? $text;
        }
        return $text;
    }
}
