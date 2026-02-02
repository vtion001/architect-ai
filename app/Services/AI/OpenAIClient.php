<?php

declare(strict_types=1);

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * OpenAI Client Service
 * 
 * Centralized client for all OpenAI API calls.
 * Handles authentication, timeouts, and error handling.
 * 
 * USAGE:
 *   $client = app(OpenAIClient::class);
 *   $response = $client->chat($messages, $options);
 */
class OpenAIClient
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openai.com/v1';
    protected int $defaultTimeout = 90;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key', '');
    }

    /**
     * Send a chat completion request.
     */
    public function chat(array $messages, array $options = []): array
    {
        if (empty($this->apiKey)) {
            Log::error('OpenAI API key not configured');
            return $this->errorResponse('AI service not configured');
        }

        $timeout = $options['timeout'] ?? $this->defaultTimeout;
        
        // Allowed OpenAI parameters
        $allowedParams = [
            'model', 'temperature', 'max_tokens', 'top_p', 'frequency_penalty', 
            'presence_penalty', 'stop', 'n', 'response_format', 'seed', 'user', 'tools', 'tool_choice'
        ];

        // Default values
        $defaults = [
            'model' => config('services.openai.model', 'gpt-4o-mini'),
            'messages' => $messages,
            'temperature' => 0.7,
            'max_tokens' => 2000,
        ];

        // Filter options to only include allowed params
        $filteredOptions = array_filter(
            $options, 
            fn($key) => in_array($key, $allowedParams), 
            ARRAY_FILTER_USE_KEY
        );

        $payload = array_merge($defaults, $filteredOptions);

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout($timeout)
                ->post("{$this->baseUrl}/chat/completions", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => $response->json('choices.0.message.content'),
                    'usage' => $response->json('usage'),
                ];
            }

            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload
            ]);

            return $this->errorResponse($response->body());

        } catch (\Throwable $e) {
            Log::error('OpenAI API exception', ['error' => $e->getMessage()]);
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Check if the client is properly configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Format an error response.
     */
    protected function errorResponse(string $error): array
    {
        return [
            'success' => false,
            'error' => $error,
            'message' => null,
        ];
    }
}
