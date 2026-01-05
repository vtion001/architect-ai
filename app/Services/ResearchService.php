<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ResearchService
{
    private ?string $apiKey;
    private string $model;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.key');
        $this->model = config('services.gemini.model', 'gemini-1.5-pro');
    }

    /**
     * Perform deep research on a topic using Gemini API.
     */
    public function performResearch(string $topic): string
    {
        if (!$this->apiKey) {
            return "Research for: $topic (Gemini API key not configured).";
        }

        // 1. RAG: Fetch relevant internal knowledge base assets
        $kbContext = $this->getKnowledgeBaseContext($topic);
        $enhancedTopic = $topic;
        if ($kbContext) {
            $enhancedTopic .= "\n\nUSE THESE INTERNAL SOURCES AS GROUNDING FOR THE REPORT:\n" . $kbContext;
        }

        // List of models to try in order: Primary -> Fallback 1 -> Fallback 2
        $models = array_unique([
            $this->model, 
            'gemini-1.5-pro', 
            'gemini-1.5-flash'
        ]);

        foreach ($models as $model) {
            Log::info("Attempting deep research with model: $model");
            
            $result = $this->attemptResearchWithModel($model, $enhancedTopic);
            
            if ($result) {
                return $result;
            }
            
            Log::warning("Model $model failed or rate limited. Switching to fallback...");
        }

        return "Research failed: Rate limit exceeded on all available Gemini models. Please wait a minute and try again.";
    }

    /**
     * RAG: Retrieve relevant context from the tenant's knowledge base.
     */
    protected function getKnowledgeBaseContext(string $topic): ?string
    {
        $tenant = app(\App\Models\Tenant::class);
        if (!$tenant) return null;

        $assets = \App\Models\KnowledgeBaseAsset::where('tenant_id', $tenant->id)
            ->where(function($q) use ($topic) {
                $q->where('title', 'like', "%$topic%")
                  ->orWhere('content', 'like', "%$topic%");
            })
            ->limit(3)
            ->get();

        if ($assets->isEmpty()) return null;

        return $assets->map(fn($a) => "--- INTERNAL SOURCE: {$a->title} ---\n{$a->content}")->implode("\n\n");
    }

    private function attemptResearchWithModel(string $model, string $topic): ?string
    {
        try {
            $response = Http::timeout(120)
                ->retry(2, 5000, function ($exception, $request) {
                    return $exception->response->status() === 429;
                })
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => "You are a professional research analyst. Your goal is to perform a broad and deep search across at least 15-20 distinct high-quality sources (news, industry reports, official data, and specialized publications). 
                                
                                Perform an exhaustive deep research on: $topic. 
                                
                                Provide a comprehensive, data-heavy, and factual report. 
                                Include specific numbers, dates, market trends, and competitive insights. 
                                Avoid generic filler; prioritize hard data points. 
                                
                                CITATION RULES:
                                - Use numerical citations like [1], [2] throughout the text.
                                - Every major factual claim MUST have a citation.
                                - Format the result in clean Markdown with a detailed 'Sources' section at the end corresponding to the [1], [2] numbers."]
                            ]
                        ]
                    ],
                    'tools' => [
                        ['google_search_retrieval' => ['dynamic_retrieval_config' => ['mode' => 'MODE_DYNAMIC', 'dynamic_threshold' => 0.3]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 4000,
                    ]
                ]);

            if ($response->successful()) {
                $candidate = $response->json('candidates.0.content.parts.0.text');
                return $candidate; // Return null if empty, handled by loop
            }

            Log::error("Gemini API Error ($model): " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("Research Exception ($model): " . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate social media topic suggestions using Gemini.
     */
    public function suggestSocialMediaTopics(string $topic): string
    {
        if (!$this->apiKey) {
            return "Gemini API key missing.";
        }

        // Models to try: Configured -> Flash 1.5 Latest -> Pro 1.5 Latest -> Gemini Pro (1.0)
        $models = array_unique([
            $this->model,
            'gemini-1.5-flash-latest',
            'gemini-1.5-pro-latest',
            'gemini-pro'
        ]);

        foreach ($models as $model) {
            try {
                $response = Http::timeout(30)
                    ->retry(1, 2000) // Brief retry for network blips
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}", [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    ['text' => "You are a social media trend expert. 
                                    Generate 5 high-impact, engaging social media post topic ideas derived from the seed: '$topic'.
                                    
                                    FORMAT:
                                    - Provide ONLY a simple bulleted list.
                                    - Keep titles catchy, concise, and click-worthy.
                                    - Do not include introductory text or explanations.
                                    - Focus on viral potential and professional engagement."]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.8,
                            'maxOutputTokens' => 500,
                        ]
                    ]);

                if ($response->successful()) {
                    return $response->json('candidates.0.content.parts.0.text') ?? "No suggestions generated.";
                }

                Log::warning("Gemini Suggestions Error ($model): " . $response->json('error.message', 'Unknown error'));

            } catch (\Exception $e) {
                Log::warning("Gemini Suggestions Exception ($model): " . $e->getMessage());
            }
        }

        Log::error("All Gemini models failed. Attempting OpenAI fallback...");

        // OpenAI Fallback
        $openaiKey = config('services.openai.key');
        if ($openaiKey) {
            try {
                $response = Http::withToken($openaiKey)
                    ->timeout(30)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => config('services.openai.model', 'gpt-4o-mini'),
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'You are a social media trend expert.'
                            ],
                            [
                                'role' => 'user',
                                'content' => "Generate 5 high-impact, engaging social media post topic ideas derived from the seed: '$topic'.
                                
                                FORMAT:
                                - Provide ONLY a simple bulleted list.
                                - Keep titles catchy, concise, and click-worthy.
                                - Do not include introductory text or explanations.
                                - Focus on viral potential and professional engagement."
                            ]
                        ],
                        'temperature' => 0.8,
                        'max_tokens' => 500,
                    ]);

                if ($response->successful()) {
                    return $response->json('choices.0.message.content') ?? "No suggestions generated (OpenAI).";
                }

                Log::error("OpenAI Fallback Error: " . $response->body());

            } catch (\Exception $e) {
                Log::error("OpenAI Fallback Exception: " . $e->getMessage());
            }
        }

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

        // Models to try: Configured -> Flash 1.5 Latest -> Pro 1.5 Latest
        $models = array_unique([
            $this->model,
            'gemini-1.5-flash-latest',
            'gemini-1.5-pro-latest'
        ]);

        foreach ($models as $model) {
            try {
                $response = Http::timeout(30)
                    ->retry(1, 2000)
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}", [
                        'contents' => [
                            [
                                'role' => 'user',
                                'parts' => [
                                    ['text' => "You are a professional content editor. 
                                    Rewrite the following context/mandate to be more clear, professional, and impactful. 
                                    
                                    GOAL: Improve instructions for an AI content generator.
                                    RULES:
                                    - Keep the original intent and meaning.
                                    - Remove ambiguity.
                                    - Fix grammar and flow.
                                    - Return ONLY the rewritten text, no explanations.
                                    
                                    Original Text: '$text'"]
                                ]
                            ]
                        ],
                        'generationConfig' => [
                            'temperature' => 0.7,
                            'maxOutputTokens' => 500,
                        ]
                    ]);

                if ($response->successful()) {
                    return $response->json('candidates.0.content.parts.0.text') ?? $text;
                }
            } catch (\Exception $e) {
                // Continue to next model
            }
        }

        // OpenAI Fallback
        $openaiKey = config('services.openai.key');
        if ($openaiKey) {
            try {
                $response = Http::withToken($openaiKey)
                    ->timeout(30)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => config('services.openai.model', 'gpt-4o-mini'),
                        'messages' => [
                            ['role' => 'system', 'content' => 'You are a professional content editor.'],
                            ['role' => 'user', 'content' => "Rewrite the following text to be more clear, professional, and impactful for an AI generator instructions. Return ONLY the rewritten text. Text: '$text'"]
                        ],
                    ]);

                if ($response->successful()) {
                    return $response->json('choices.0.message.content') ?? $text;
                }
            } catch (\Exception $e) {
                // Ignore
            }
        }

        return $text; // Fallback to original
    }
}
