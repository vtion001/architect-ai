<?php

declare(strict_types=1);

namespace App\Services\ContentGenerators;

use App\Contracts\ContentGeneratorInterface;
use App\Services\AI\OpenAIClient;
use App\Services\AI\PromptBuilder;

/**
 * Base class for content generators.
 * 
 * Uses modular AI services for maintainability.
 * All generators extend this class and implement
 * getSystemPrompt() and getUserPrompt() methods.
 * 
 * @see OpenAIClient for API calls
 * @see PromptBuilder for prompt construction
 */
abstract class BaseContentGenerator implements ContentGeneratorInterface
{
    protected OpenAIClient $aiClient;
    protected PromptBuilder $promptBuilder;

    public function __construct()
    {
        $this->aiClient = app(OpenAIClient::class);
        $this->promptBuilder = app(PromptBuilder::class);
    }

    /**
     * Humanization instructions to avoid AI "tells".
     */
    protected function getHumanizeInstruction(string $tone = 'Professional'): string
    {
        return $this->promptBuilder->humanizeInstructions($tone);
    }

    /**
     * Generate content using this strategy.
     */
    public function generate(string $topic, ?string $context = null, array $options = []): string
    {
        $systemPrompt = $this->getSystemPrompt($options);
        $userPrompt = $this->getUserPrompt($topic, $context, $options);

        $response = $this->aiClient->chat([
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ], [
            'model' => $options['model'] ?? config('services.openai.model', 'gpt-4o-mini'),
            'temperature' => $options['temperature'] ?? 0.8,
            'max_tokens' => $options['max_tokens'] ?? 4000,
            'timeout' => $options['timeout'] ?? 120,
        ]);

        if (!$response['success']) {
            throw new \Exception("AI Generation failed: " . ($response['error'] ?? 'Unknown error'));
        }

        return $response['message'];
    }
}
