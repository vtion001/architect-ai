<?php

declare(strict_types=1);

namespace App\Services;

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
        if (! $this->apiKey) {
            return "Research for: $topic (MiniMax API key not configured).";
        }

        // 1. RAG: Fetch relevant internal knowledge base assets
        $kbContext = $this->knowledgeBaseService->getContext($topic);
        $enhancedTopic = $topic;
        if ($kbContext) {
            $enhancedTopic .= "\n\nUSE THESE INTERNAL SOURCES AS GROUNDING FOR THE REPORT:\n".$kbContext;
        }

        $response = \Http::withToken($this->apiKey)
            ->timeout(180)
            ->post($this->baseUrl.'/text/chatcompletion_v2', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a lead research analyst. Produce an EXHAUSTIVE, 3000+ word deep-dive report.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $enhancedTopic,
                    ],
                ],
                'max_completion_tokens' => 8000,
                'temperature' => 0.5,
            ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content') ?? '';
        }
        \Log::error('MiniMax research error: '.$response->body());

        return 'Research failed: MiniMax request failed.';
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
     * Generate blog topic suggestions from a seed keyword.
     */
    public function suggestBlogTopics(string $keyword): string
    {
        if (! $this->apiKey) {
            return $this->generateFallbackBlogTopics($keyword);
        }

        $response = \Http::withToken($this->apiKey)
            ->timeout(30)
            ->post($this->baseUrl.'/text/chatcompletion_v2', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a blog content strategist specializing in SEO-driven content ideas.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Based on the keyword: '$keyword'\n\nGenerate exactly 5 catchy, SEO-friendly blog post topic ideas that would perform well in search engines and social sharing.\n\nFORMAT:\n- Each topic should be on its own line\n- Start each line with a number followed by a period (1. 2. etc)\n- Include a brief description after a dash (e.g., \"1. How to Create Viral Content - A comprehensive guide to...\")\n- Topics should cover different content angles: how-to, listicles, comparisons, case studies, and trending analysis\n- Do not include introductory text or explanations",
                    ],
                ],
                'temperature' => 0.8,
                'max_completion_tokens' => 800,
            ]);

        if ($response->successful()) {
            $content = $response->json('choices.0.message.content') ?? '';
            if ($content) {
                return $content;
            }
        }
        Log::error('MiniMax blog suggestion error: '.$response->body());

        return $this->generateFallbackBlogTopics($keyword);
    }

    /**
     * Generate fallback blog topics without API.
     */
    private function generateFallbackBlogTopics(string $keyword): string
    {
        return "1. The Ultimate Guide to " . ucfirst($keyword) . " - Everything you need to know to get started\n" .
               "2. 10 " . ucfirst($keyword) . " Strategies That Actually Work - Proven methods for real results\n" .
               "3. " . ucfirst($keyword) . " vs Alternatives: Which is Right for You? - An honest comparison\n" .
               "4. Common " . ucfirst($keyword) . " Mistakes to Avoid - Learn from these frequently made errors\n" .
               "5. Why " . ucfirst($keyword) . " Matters in 2024 - The impact and benefits explained";
    }

    /**
     * Generate social media topic suggestions using MiniMax.
     */
    public function suggestSocialMediaTopics(string $topic): string
    {
        if (! $this->apiKey) {
            return 'MiniMax API key missing.';
        }

        $response = \Http::withToken($this->apiKey)
            ->timeout(30)
            ->post($this->baseUrl.'/text/chatcompletion_v2', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a social media trend expert.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Generate 5 high-impact, engaging social media post topic ideas derived from the seed: '$topic'.\n\nFORMAT:\n- Provide ONLY a simple bulleted list.\n- Keep titles catchy, concise, and click-worthy.\n- Do not include introductory text or explanations.\n- Focus on viral potential and professional engagement.",
                    ],
                ],
                'temperature' => 0.8,
                'max_completion_tokens' => 500,
            ]);

        if ($response->successful()) {
            return $response->json('choices.0.message.content') ?? 'No suggestions generated.';
        }
        \Log::error('MiniMax suggestion error: '.$response->body());

        return 'Error generating suggestions. Please try again later.';
    }

    /**
     * Generate SEO keyword suggestions from a blog topic.
     */
    public function suggestSeoKeywords(string $topic): string
    {
        if (! $this->apiKey) {
            return $this->generateFallbackSeoKeywords($topic);
        }

        $response = \Http::withToken($this->apiKey)
            ->timeout(30)
            ->post($this->baseUrl.'/text/chatcompletion_v2', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an SEO keyword research expert. Generate high-value keywords that will help this content rank well in search engines.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Based on the blog topic: '$topic'\n\nGenerate exactly 8 SEO keywords that:\n- Are highly relevant to the topic\n- Have good search intent alignment\n- Include a mix of short-tail and long-tail keywords\n- Include question-based keywords where appropriate\n- Cover different search intents (informational, commercial, transactional)\n\nFORMAT: Return ONLY a comma-separated list of keywords. No explanations, no bullets, just the keywords separated by commas.",
                    ],
                ],
                'temperature' => 0.7,
                'max_completion_tokens' => 400,
            ]);

        if ($response->successful()) {
            $content = $response->json('choices.0.message.content') ?? '';
            if ($content) {
                return $content;
            }
        }
        Log::error('MiniMax SEO keyword error: '.$response->body());

        return $this->generateFallbackSeoKeywords($topic);
    }

    /**
     * Generate fallback SEO keywords without API call.
     */
    private function generateFallbackSeoKeywords(string $topic): string
    {
        $cleanTopic = strtolower(trim($topic));
        $keywords = [
            $cleanTopic,
            $cleanTopic.' guide',
            $cleanTopic.' tips',
            'best '.$cleanTopic,
            'how to '.$cleanTopic,
            $cleanTopic.' strategies',
            $cleanTopic.' for beginners',
            $cleanTopic.' examples',
        ];

        return implode(', ', $keywords);
    }

    /**
     * Refine and rewrite context/mandate for better clarity.
     */
    public function refineContext(string $text): string
    {
        if (! $this->apiKey) {
            return $text; // Return original if no key
        }

        $response = \Http::withToken($this->apiKey)
            ->timeout(30)
            ->post($this->baseUrl.'/text/chatcompletion_v2', [
                'model' => $this->model,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional content editor.',
                    ],
                    [
                        'role' => 'user',
                        'content' => "Rewrite the following context/mandate to be more clear, professional, and impactful.\n\nGOAL: Improve instructions for an AI content generator.\nRULES:\n- Keep the original intent and meaning.\n- Remove ambiguity.\n- Fix grammar and flow.\n- Return ONLY the rewritten text, no explanations.\n\nOriginal Text: '$text'",
                    ],
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
