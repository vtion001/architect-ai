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

        try {
            $response = Http::timeout(120)
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}", [
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
                return $candidate ?? 'No research results generated.';
            }

            Log::error('Gemini API Error: ' . $response->body());
            return "Research failed for: $topic. API Error.";
        } catch (\Exception $e) {
            Log::error('Research Exception: ' . $e->getMessage());
            return "Research failed for: $topic. Technical error.";
        }
    }
}
