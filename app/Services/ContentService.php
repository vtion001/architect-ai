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
            $length = $options['length'] ?? 'Standard';
            
            $systemPrompt = "You are a relatable content creator and industry expert.
                             TONE: $tone
                             FORMAT: $type
                             
                             $humanizeInstruction
                             - $lineBreaks
                             - $hashtags
                             - Make the first sentence a compelling personal hook.
                             - Mandatory CTA: $cta";
            
            $userPrompt = "Create $count $type(s) about: $topic. \nContext: $context \nTone: $tone \nLength: $length";
            if ($count > 1) {
                $userPrompt .= "\nRespond with a numbered list of distinct, unique options.";
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
