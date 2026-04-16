<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\AI\OpenAIClient;
use Illuminate\Support\Facades\Log;

class ResearchService
{
    private const MODEL_RESEARCH = 'gpt-4o';

    private const MODEL_SUGGESTIONS = 'gpt-4o-mini';

    public function __construct(
        protected KnowledgeBaseService $knowledgeBaseService,
        protected OpenAIClient $openAIClient
    ) {}

    /**
     * Perform deep research on a topic using OpenAI.
     */
    public function performResearch(string $topic, ?string $analysisType = null): string
    {
        $kbContext = $this->knowledgeBaseService->getContext($topic);
        $enhancedTopic = $topic;
        if ($kbContext) {
            $enhancedTopic .= "\n\nUSE THESE INTERNAL SOURCES AS GROUNDING FOR THE REPORT:\n".$kbContext;
        }

        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a lead research analyst. Produce an EXHAUSTIVE, 3000+ word deep-dive report.',
            ],
            [
                'role' => 'user',
                'content' => $enhancedTopic,
            ],
        ];

        $result = $this->openAIClient->chat($messages, [
            'model' => self::MODEL_RESEARCH,
            'max_tokens' => 8000,
            'temperature' => 0.5,
            'timeout' => 240,
        ]);

        if ($result['success'] ?? false) {
            return $result['message'];
        }

        Log::error('OpenAI research error: '.($result['error'] ?? 'Unknown error'));

        return 'Research failed: OpenAI request failed.';
    }

    /**
     * RAG: Retrieve relevant context from knowledge base.
     *
     * @deprecated Use KnowledgeBaseService::getContext() directly. This is a backward-compatible delegate.
     */
    public function getKnowledgeBaseContext(string $topic): ?string
    {
        return $this->knowledgeBaseService->getContext($topic);
    }

    /**
     * Generate blog topic suggestions from a seed keyword.
     */
    public function suggestBlogTopics(string $keyword): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a blog content strategist specializing in SEO-driven content ideas.',
            ],
            [
                'role' => 'user',
                'content' => "Based on the keyword: '$keyword'\n\nGenerate exactly 5 catchy, SEO-friendly blog post topic ideas that would perform well in search engines and social sharing.\n\nFORMAT:\n- Each topic should be on its own line\n- Start each line with a number followed by a period (1. 2. etc)\n- Include a brief description after a dash (e.g., \"1. How to Create Viral Content - A comprehensive guide to...\")\n- Topics should cover different content angles: how-to, listicles, comparisons, case studies, and trending analysis\n- Do not include introductory text or explanations",
            ],
        ];

        $result = $this->openAIClient->chat($messages, [
            'model' => self::MODEL_SUGGESTIONS,
            'max_tokens' => 800,
            'temperature' => 0.8,
            'timeout' => 30,
        ]);

        if ($result['success'] ?? false) {
            return $result['message'];
        }

        Log::error('OpenAI blog suggestion error: '.($result['error'] ?? 'Unknown error'));

        return $this->generateFallbackBlogTopics($keyword);
    }

    /**
     * Generate fallback blog topics without API.
     */
    private function generateFallbackBlogTopics(string $keyword): string
    {
        return '1. The Ultimate Guide to '.ucfirst($keyword)." - Everything you need to know to get started\n".
               '2. 10 '.ucfirst($keyword)." Strategies That Actually Work - Proven methods for real results\n".
               '3. '.ucfirst($keyword)." vs Alternatives: Which is Right for You? - An honest comparison\n".
               '4. Common '.ucfirst($keyword)." Mistakes to Avoid - Learn from these frequently made errors\n".
               '5. Why '.ucfirst($keyword).' Matters in 2024 - The impact and benefits explained';
    }

    /**
     * Generate social media topic suggestions using OpenAI.
     */
    public function suggestSocialMediaTopics(string $topic): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a social media trend expert.',
            ],
            [
                'role' => 'user',
                'content' => "Generate 5 high-impact, engaging social media post topic ideas derived from the seed: '$topic'.\n\nFORMAT:\n- Provide ONLY a simple bulleted list.\n- Keep titles catchy, concise, and click-worthy.\n- Do not include introductory text or explanations.\n- Focus on viral potential and professional engagement.",
            ],
        ];

        $result = $this->openAIClient->chat($messages, [
            'model' => self::MODEL_SUGGESTIONS,
            'max_tokens' => 500,
            'temperature' => 0.8,
            'timeout' => 30,
        ]);

        if ($result['success'] ?? false) {
            return $result['message'];
        }

        Log::error('OpenAI social media suggestion error: '.($result['error'] ?? 'Unknown error'));

        return 'Error generating suggestions. Please try again later.';
    }

    /**
     * Generate SEO keyword suggestions from a blog topic.
     */
    public function suggestSeoKeywords(string $topic): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are an SEO keyword research expert. Generate high-value keywords that will help this content rank well in search engines.',
            ],
            [
                'role' => 'user',
                'content' => "Based on the blog topic: '$topic'\n\nGenerate exactly 8 SEO keywords that:\n- Are highly relevant to the topic\n- Have good search intent alignment\n- Include a mix of short-tail and long-tail keywords\n- Include question-based keywords where appropriate\n- Cover different search intents (informational, commercial, transactional)\n\nFORMAT: Return ONLY a comma-separated list of keywords. No explanations, no bullets, just the keywords separated by commas.",
            ],
        ];

        $result = $this->openAIClient->chat($messages, [
            'model' => self::MODEL_SUGGESTIONS,
            'max_tokens' => 400,
            'temperature' => 0.7,
            'timeout' => 30,
        ]);

        if ($result['success'] ?? false) {
            return $result['message'];
        }

        Log::error('OpenAI SEO keyword error: '.($result['error'] ?? 'Unknown error'));

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
     * Generate framework topic suggestions for the 4-Pillar calendar.
     */
    public function suggestFrameworkTopics(string $topic): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a content strategy expert specializing in the 4-Pillar content framework.',
            ],
            [
                'role' => 'user',
                'content' => "Based on the niche/topic: '$topic'\n\nGenerate exactly 5 catchy, engaging content angle ideas for a weekly social media calendar.\n\nFORMAT:\n- Each angle should be on its own line\n- Start each line with a number followed by a period (1. 2. etc)\n- Include a brief description after a dash (e.g., \"1. How to Create Viral Content - A comprehensive guide to...\")\n- Ideas should be diverse: educational how-tos, case studies, community engagement posts, product/service highlights\n- Do not include introductory text or explanations\n- These will be used to generate a 4-pillar calendar (3 educational, 2 showcase, 2 conversational, 1 promotional posts)",
            ],
        ];

        $result = $this->openAIClient->chat($messages, [
            'model' => self::MODEL_SUGGESTIONS,
            'max_tokens' => 800,
            'temperature' => 0.8,
            'timeout' => 30,
        ]);

        if ($result['success'] ?? false) {
            return $result['message'];
        }

        Log::error('OpenAI framework suggestion error: '.($result['error'] ?? 'Unknown error'));

        return $this->generateFallbackFrameworkTopics($topic);
    }

    /**
     * Generate fallback framework topics without API.
     */
    private function generateFallbackFrameworkTopics(string $keyword): string
    {
        return '1. The Ultimate Guide to '.ucfirst($keyword)." - Everything you need to know to get started\n".
               '2. 10 '.ucfirst($keyword)." Strategies That Actually Work - Proven methods for real results\n".
               '3. '.ucfirst($keyword)." Success Stories - Real case studies and transformations\n".
               '4. Common '.ucfirst($keyword)." Mistakes to Avoid - Learn from these frequently made errors\n".
               '5. Exclusive '.ucfirst($keyword).' Offer - Special promotion for our community';
    }

    /**
     * Refine and rewrite context/mandate for better clarity.
     */
    public function refineContext(string $text): string
    {
        $messages = [
            [
                'role' => 'system',
                'content' => 'You are a professional content editor.',
            ],
            [
                'role' => 'user',
                'content' => "Rewrite the following context/mandate to be more clear, professional, and impactful.\n\nGOAL: Improve instructions for an AI content generator.\nRULES:\n- Keep the original intent and meaning.\n- Remove ambiguity.\n- Fix grammar and flow.\n- Return ONLY the rewritten text, no explanations.\n\nOriginal Text: '$text'",
            ],
        ];

        $result = $this->openAIClient->chat($messages, [
            'model' => self::MODEL_SUGGESTIONS,
            'max_tokens' => 500,
            'temperature' => 0.7,
            'timeout' => 30,
        ]);

        if ($result['success'] ?? false) {
            return $result['message'];
        }

        return $text;
    }
}
