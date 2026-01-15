<?php

declare(strict_types=1);

namespace App\Services\ContentGenerators;

use App\Contracts\ContentGeneratorInterface;

/**
 * Base class for content generators.
 * 
 * Contains shared humanization instructions and common methods.
 */
abstract class BaseContentGenerator implements ContentGeneratorInterface
{
    /**
     * Humanization instructions to avoid AI "tells".
     */
    protected function getHumanizeInstruction(string $tone = 'Professional'): string
    {
        return "
            STRICT HUMANIZATION GUIDELINES:
            - Write like a real person sharing valuable insights, not an AI following a prompt.
            - Use natural sentence variety (mix short and long sentences).
            - Use contractions (e.g., 'don't', 'it's', 'we're') to sound conversational.
            - Avoid AI 'tells' and clichés: Do NOT use words like 'delve', 'unlock', 'embark', 'comprehensive', 'in today's digital landscape', or 'tapestry'.
            - Use active voice and focus on a direct connection with the reader.
            - Inject a bit of personality and warmth while maintaining the '$tone' tone.";
    }

    /**
     * Make an OpenAI API call with the given prompts.
     */
    protected function callOpenAI(string $systemPrompt, string $userPrompt, array $options = []): string
    {
        $apiKey = config('services.openai.key');
        $model = $options['model'] ?? config('services.openai.model', 'gpt-4o-mini');

        $response = \Illuminate\Support\Facades\Http::withToken($apiKey)
            ->timeout($options['timeout'] ?? 120)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'max_tokens' => $options['max_tokens'] ?? 4000,
                'temperature' => $options['temperature'] ?? 0.8,
            ]);

        if ($response->failed()) {
            throw new \Exception("AI Generation failed: " . $response->body());
        }

        return $response->json('choices.0.message.content');
    }

    /**
     * Generate content using this strategy.
     */
    public function generate(string $topic, ?string $context = null, array $options = []): string
    {
        $systemPrompt = $this->getSystemPrompt($options);
        $userPrompt = $this->getUserPrompt($topic, $context, $options);

        return $this->callOpenAI($systemPrompt, $userPrompt, $options);
    }
}
