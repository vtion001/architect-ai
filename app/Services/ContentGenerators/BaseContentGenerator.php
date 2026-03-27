<?php

declare(strict_types=1);

namespace App\Services\ContentGenerators;

use App\Contracts\ContentGeneratorInterface;
use Illuminate\Support\Facades\Http;

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
    protected string $apiKey;

    protected string $model;

    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.key');
        $this->model = config('services.openrouter.content_model', 'zhipu-ai/glm-4.5-air');
        $this->baseUrl = config('services.openrouter.base_url', 'https://openrouter.ai/api/v1/chat/completions');
    }

    /**
     * Generate content using OpenRouter GLM-4.5 Air.
     */
    public function generate(string $topic, ?string $context = null, array $options = []): string
    {
        $systemPrompt = $this->getSystemPrompt($options);
        $userPrompt = $this->getUserPrompt($topic, $context, $options);

        $response = Http::withToken($this->apiKey)
            ->timeout($options['timeout'] ?? 120)
            ->post($this->baseUrl, [
                'model' => $options['model'] ?? $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => $options['temperature'] ?? 0.8,
                'max_tokens' => $options['max_tokens'] ?? 4000,
                'response_format' => $options['response_format'] ?? null,
            ]);

        if ($response->successful()) {
            $message = $response->json('choices.0.message.content') ?? '';
        } else {
            $message = 'Content generation failed.';
        }

        // If the generator expects JSON (inferred from type or explicit option)
        if (($options['response_format'] ?? null) === ['type' => 'json_object'] || $this->getType() === 'framework_calendar') {
            return $this->cleanJsonResponse($message);
        }

        return $message;
    }

    /**
     * Clean and extract JSON from AI response.
     */
    protected function cleanJsonResponse(string $text): string
    {
        // Remove markdown code blocks if present
        if (preg_match('/```(?:json)?\s*(\{.*?\})\s*```/s', $text, $matches)) {
            $text = $matches[1];
        } else {
            // Find the first { and last }
            $start = strpos($text, '{');
            $end = strrpos($text, '}');

            if ($start !== false && $end !== false && $end > $start) {
                $text = substr($text, $start, $end - $start + 1);
            }
        }

        // Validate
        json_decode($text);
        if (json_last_error() !== JSON_ERROR_NONE) {
            // Remove bad control characters but keep structure
            // Remove control chars (0-31, 127) except tab (9), newline (10), carriage return (13)
            $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        }

        return $text;
    }
}
