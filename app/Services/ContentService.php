<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ContentService
{
    protected string $apiKey;
    protected string $model;
    protected ?string $hikerApiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
        $this->hikerApiKey = config('services.hiker_api.key');
    }

    public function generateText(string $topic, string $type, ?string $context = null, array $options = []): string
    {
        $generator = $options['generator'] ?? 'post';
        $tone = $options['tone'] ?? 'Professional';
        $cta = $options['cta'] ?? '';
        $lineBreaks = ($options['addLineBreaks'] ?? true) ? "Use natural spacing and paragraph breaks for a human-like flow." : "Use standard spacing.";
        $hashtags = ($options['includeHashtags'] ?? false) ? "Include 2-3 relevant hashtags that a human would actually use (no spamming)." : "";

        // Humanizing constraints to avoid "AI-speak"
        $humanizeInstruction = "
            STRICT HUMANIZATION GUIDELINES:
            - Write like a real person sharing valuable insights, not an AI following a prompt.
            - Use natural sentence variety (mix short and long sentences).
            - Use contractions (e.g., 'don't', 'it's', 'we're') to sound conversational.
            - Avoid AI 'tells' and clichés: Do NOT use words like 'delve', 'unlock', 'embark', 'comprehensive', 'in today's digital landscape', or 'tapestry'.
            - Use active voice and focus on a direct connection with the reader.
            - Inject a bit of personality and warmth while maintaining the '$tone' tone.";

        if ($generator === 'video') {
            $platform = $options['video_platform'] ?? 'reels';
            $hook = $options['video_hook'] ?? 'Problem/Solution';
            $duration = $options['video_duration'] ?? '60s';

            $systemPrompt = "You are a creative video storyteller. Create a script for $platform that feels authentic and engaging.
                             HOOK STYLE: $hook
                             TARGET DURATION: $duration
                             
                             $humanizeInstruction
                             - Write scripts that sound natural when spoken aloud. 
                             - Include realistic pauses and verbal emphasis.
                             - Visual cues should support the narrative flow, not just describe actions.
                             - CTA: $cta";
            
            $userPrompt = "Write a $duration video script about $topic. Provide it in a clear format. Context: $context";
        } elseif ($generator === 'blog') {
            $keywords = $options['blog_keywords'] ?? '';
            $structure = $options['blog_structure'] ?? 'Standard';

            $systemPrompt = "You are an insightful thought leader. Write an article that people actually want to read.
                             ARTICLE STRUCTURE: $structure
                             TONE: $tone
                             
                             $humanizeInstruction
                             - Use Markdown headers (H1, H2, H3) that are catchy and human-centric.
                             - Explain complex ideas simply, as if explaining to a smart friend.
                             - Integrate keywords naturally; if they feel forced, prioritize readability.
                             - Mandatory CTA: $cta";
            
            $userPrompt = "Write a $structure blog post about $topic. \nKeywords to consider: $keywords. \nContext: $context";
        } else {
            // Default: Post Generator
            $count = $options['count'] ?? 1;
            
            // Fix: Ensure we don't generate blog posts in Post Generator mode
            if ($type === 'blog-post') {
                $type = 'social-media post';
            }

            // Fix: Enforce shortness if default length is used
            $length = $options['length'] ?? 'Short and punchy';
            if ($length === 'Standard') {
                $length = 'Short, concise, and engaging';
            }
            
            $viralPosts = $this->getViralPosts($topic);
            $examples = '';

            if (!empty($viralPosts)) {
                $examples = collect($viralPosts)->map(function ($post) {
                    if (is_string($post)) return $post;
                    return $post['caption_text'] ?? $post['caption']['text'] ?? null;
                })->filter()->take(5)->implode("\n\n---\n\n");
            }

            if (!empty($examples)) {
                // API Success Path
                $systemPrompt = "You are a viral content expert.
                                 BASED ON THESE HIGH-PERFORMING EXAMPLES:
                                 
                                 $examples
                                 
                                 Generate a new caption about \"$topic\" with a $tone tone.
                                 Follow the patterns, hooks, and engagement styles you see in the successful captions above.
                                 IMPORTANT: Distill the essence of these examples into a SHORT, punchy caption. Do not write long listicles unless explicitly asked.
                                 
                                 $humanizeInstruction
                                 - $lineBreaks
                                 - $hashtags
                                 - Mandatory CTA: $cta";
            } else {
                // Fallback / No API Path
                $systemPrompt = "You are a top-tier viral content creator who knows how to stop the scroll.
                                 GOAL: Create a viral post that is punchy, relatable, and highly shareable.
                                 TONE: $tone
                                 FORMAT: $type
                                 LENGTH: Short and punchy
                                 
                                 $humanizeInstruction
                                 - Keep the content SHORT and impactful (under 280 chars preferred).
                                 - $lineBreaks
                                 - $hashtags
                                 - Make the first sentence a compelling personal hook or pattern interrupt.
                                 - Mandatory CTA: $cta";
            }
            
            $userPrompt = "Create $count $type(s) about: $topic. \nContext: $context \nTone: $tone \nLength: $length. \nConstraint: Keep it short, engaging, and to the point. No fluff. Do NOT number the outputs.";
            if ($count > 1) {
                $userPrompt .= "\nRespond with distinct options separated by '---' (triple dash).";
            }
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
                    'temperature' => 0.8, // Increased for more natural variety
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

    protected function getViralPosts(string $topic): array
    {
        if (!$this->hikerApiKey) {
            return [];
        }

        $hashtag = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $topic));
        
        try {
            $response = Http::withHeaders([
                'x-access-key' => $this->hikerApiKey,
                'accept' => 'application/json',
            ])->get("https://api.hikerapi.com/v2/hashtag/medias/top", [
                'name' => $hashtag
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if (is_array($data)) {
                    return $data;
                }
                
                if (isset($data['response'])) {
                    return $data['response'];
                }
            }
        } catch (\Exception $e) {
            Log::warning("HikerAPI fetch failed: " . $e->getMessage());
        }

        return [];
    }

    public function generateImage(string $prompt): ?string
    {
        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/images/generations', [
                    'model' => 'dall-e-3',
                    'prompt' => "A professional, stunning, and organic image about: $prompt. The style should be high-end editorial photography, modern, and clean. No generic stock photo look. Use natural lighting and depth of field.",
                    'n' => 1,
                    'size' => '1024x1024',
                ]);

            if ($response->successful()) {
                return $response->json('data.0.url');
            }
            
            Log::error("Image generation failed: " . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error("Image generation exception: " . $e->getMessage());
            return null;
        }
    }
}