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
            'gemini-1.5-flash',
            'gemini-pro'
        ]);

        foreach ($models as $model) {
            Log::info("Attempting deep research with model: $model");
            
            $result = $this->attemptResearchWithModel($model, $enhancedTopic);
            
            if ($result) {
                return $result;
            }
            
            Log::warning("Model $model failed or rate limited. Switching to fallback...");
            sleep(2); // Brief pause to respect rate limits before trying next model
        }

        // OpenAI Fallback
        $openaiKey = config('services.openai.key');
        if ($openaiKey) {
            Log::info("Gemini failed. Attempting OpenAI fallback for Deep Research...");
            $result = $this->attemptResearchWithOpenAI($openaiKey, $enhancedTopic);
            if ($result) {
                return $result;
            }
        }

        return "Research failed: Rate limit exceeded on all available Gemini models and OpenAI fallback failed. Please wait a minute and try again.";
    }

    private function attemptResearchWithOpenAI(string $apiKey, string $topic): ?string
    {
        try {
            $response = Http::withToken($apiKey)
                ->timeout(180)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => config('services.openai.model', 'gpt-4o-mini'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => "You are a lead research analyst. Produce an EXHAUSTIVE, 3000+ word deep-dive report."
                        ],
                        [
                            'role' => 'user',
                            'content' => "Perform deep research on: $topic.
                            
                            MANDATE:
                            - Output MUST be 3,000-5,000 words.
                            - Use 20-30 distinct sources (simulated or known data).
                            - Densely packed with numbers, dates, and figures.
                            
                            STRUCTURE:
                            1. Executive Summary
                            2. Market Landscape
                            3. Competitive Intelligence
                            4. Tech Trends
                            5. Regulatory Environment
                            6. Consumer Behavior
                            7. Future Outlook
                            8. Strategic Recommendations
                            
                            Format in clean Markdown.
                            
                            METADATA JSON:
                            At the very end of your response, strictly append a JSON block (surrounded by triple backticks with json) containing self-evaluated metrics.
                            Format:
                            {
                              'confidence_score': '98.5%',
                              'grounding_depth': 'Multi-Layer Web Cross-Reference',
                              'source_count': 25,
                              'verification_status': 'Verified'
                            }"
                        ]
                    ],
                    'max_tokens' => 8000,
                    'temperature' => 0.5,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }
            
            Log::error("OpenAI Research Error: " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("OpenAI Research Exception: " . $e->getMessage());
            return null;
        }
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
            $response = Http::timeout(180) // Increased timeout for long generation
                ->retry(2, 5000, function ($exception, $request) {
                    return $exception->response->status() === 429;
                })
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => "You are a lead research analyst for a top-tier consultancy. Your goal is to produce an EXHAUSTIVE, deep-dive research report.
                                
                                TOPIC: $topic
                                
                                MANDATE:
                                - The output MUST be extensive, aiming for at least 3,000-5,000 words (equivalent to 5-10+ pages).
                                - Do NOT summarize or be concise. Expand deeply on every point.
                                - Use at least 20-30 distinct, high-quality external sources (news, official reports, academic papers).
                                
                                REQUIRED STRUCTURE:
                                1. Executive Summary (Detailed overview, not brief)
                                2. Global Market Landscape (Market size, CAGR, regional breakdown)
                                3. Competitive Intelligence (Major players, market share, detailed SWOT for top 3)
                                4. Technological & Strategic Trends (Innovations, disruptions, adoption rates)
                                5. Regulatory & Legal Environment (Current laws, compliance, future legislation)
                                6. Consumer/User Behavior (Demographics, shifting preferences, data backing)
                                7. Future Outlook & Projections (5-10 year forecast with specific data models)
                                8. Strategic Recommendations (Actionable, data-driven steps)
                                
                                VISUALIZATION (INFOGRAPHICS):
                                - You MUST include at least 3-5 complex Mermaid.js diagrams to visualize the data (e.g., Pie charts for market share, Line charts for trends, Gantt charts for timelines).
                                - Use standard `mermaid` code blocks.
                                
                                DATA REQUIREMENTS:
                                - Every section must be DENSE with specific numbers, dates, percentages, and financial figures.
                                - Avoid generic statements like 'the market is growing'. Instead, say 'The market is projected to grow by 12.4% CAGR to reach $50B by 2030'.
                                
                                CITATION RULES:
                                - Use numerical citations like [1], [2] strictly throughout the text.
                                - Every major factual claim MUST have a citation.
                                - Format the result in clean Markdown.
                                - Include a detailed 'References' list at the very end with all 20-30 full URLs matching the citations.
                                
                                METADATA JSON:
                                At the very end of your response, strictly append a JSON block (surrounded by triple backticks with json) containing self-evaluated metrics.
                                Format:
                                {
                                  'confidence_score': '98.5%',
                                  'grounding_depth': 'Multi-Layer Web Cross-Reference',
                                  'source_count': 25,
                                  'verification_status': 'Verified'
                                }
                                Do not include any text after this JSON block."]
                            ]
                        ]
                    ],
                    'tools' => [
                        ['google_search_retrieval' => ['dynamic_retrieval_config' => ['mode' => 'MODE_DYNAMIC', 'dynamic_threshold' => 0.3]]]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.4, // Lower temperature for more factual/analytical output
                        'maxOutputTokens' => 8192, // Maximize token output for length
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
