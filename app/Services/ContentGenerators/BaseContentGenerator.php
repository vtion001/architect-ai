<?php

declare(strict_types=1);

namespace App\Services\ContentGenerators;

use App\Contracts\ContentGeneratorInterface;
use App\Services\AI\MiniMaxClient;
use App\Services\AI\OpenAIClient;

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
    protected OpenAIClient $openAIClient;
    protected MiniMaxClient $miniMaxClient;

    public function __construct()
    {
        $this->openAIClient = app(OpenAIClient::class);
        $this->miniMaxClient = app(MiniMaxClient::class);
    }

    /**
     * Generate content using OpenAI with MiniMax fallback.
     */
    public function generate(string $topic, ?string $context = null, array $options = []): string
    {
        $systemPrompt = $this->getSystemPrompt($options);
        $userPrompt = $this->getUserPrompt($topic, $context, $options);

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
            ['role' => 'user', 'content' => $userPrompt],
        ];

        $chatOptions = [
            'temperature' => $options['temperature'] ?? 0.8,
            'max_tokens' => $options['max_tokens'] ?? 4000,
            'timeout' => $options['timeout'] ?? 120,
        ];

        if (isset($options['model'])) {
            $chatOptions['model'] = $options['model'];
        }

        $lastError = null;

        // Try OpenAI first
        if ($this->openAIClient->isConfigured()) {
            $response = $this->openAIClient->chat($messages, $chatOptions);

            if ($response['success']) {
                $message = $response['message'] ?? '';

                if (($options['response_format'] ?? null) === ['type' => 'json_object'] || $this->getType() === 'framework_calendar') {
                    return $this->cleanJsonResponse($message);
                }

                return $message;
            }

            $lastError = $response['error'] ?? 'OpenAI request failed';
            Log::warning('ContentGenerator OpenAI failed, falling back to MiniMax: '.$lastError);
        }

        // Fall back to MiniMax
        if ($this->miniMaxClient->isConfigured()) {
            $response = $this->miniMaxClient->chat($messages, $chatOptions);

            if ($response['success']) {
                $message = $response['message'] ?? '';

                if (($options['response_format'] ?? null) === ['type' => 'json_object'] || $this->getType() === 'framework_calendar') {
                    return $this->cleanJsonResponse($message);
                }

                return $message;
            }

            $lastError = $response['error'] ?? 'MiniMax request failed';
            Log::warning('ContentGenerator MiniMax failed: '.$lastError);
        }

        // Both failed
        $errorMsg = $lastError ?? 'No AI service configured';
        Log::error('ContentGenerator: all AI providers failed. Last error: '.$errorMsg);

        if (($options['response_format'] ?? null) === ['type' => 'json_object'] || $this->getType() === 'framework_calendar') {
            return $this->cleanJsonResponse('{}');
        }

        return 'Content generation failed. '.$errorMsg;
    }

    /**
     * Get humanization instructions based on tone.
     */
    protected function getHumanizeInstruction(string $tone = 'Professional'): string
    {
        return "STRICT HUMANIZATION GUIDELINES:
- Write like a real person sharing valuable insights, not an AI following a prompt.
- Use natural sentence variety (mix short and long sentences).
- Use contractions (e.g., 'don't', 'it's', 'we're') to sound conversational.
- Avoid AI 'tells' and clichés: Do NOT use words like 'delve', 'unlock', 'embark', 'comprehensive', 'in today's digital landscape', 'step into', 'step into the world', or 'tapestry'.
- Use active voice and focus on a direct connection with the reader.
- Inject a bit of personality and warmth while maintaining the '{$tone}' tone.";
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
