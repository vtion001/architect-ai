<?php

declare(strict_types=1);

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * MiniMax Client Service
 *
 * Centralized client for MiniMax API (minimaxi.com).
 * Handles authentication, timeouts, and error handling.
 *
 * USAGE:
 *   $client = app(MiniMaxClient::class);
 *   $response = $client->chat($messages, $options);
 */
class MiniMaxClient
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.minimax.io/v1';
    protected string $model = 'minimax-m2.7';
    protected int $defaultTimeout = 90;

    public function __construct()
    {
        $this->apiKey = config('services.minimax.key', '');
        $this->model = config('services.minimax.model', 'M2.7');
    }

    /**
     * Send a chat completion request.
     */
    public function chat(array $messages, array $options = []): array
    {
        if (empty($this->apiKey)) {
            Log::error('MiniMax API key not configured');
            return $this->errorResponse('MiniMax AI service not configured');
        }

        $timeout = $options['timeout'] ?? $this->defaultTimeout;
        $model = $options['model'] ?? $this->model;

        // MiniMax-specific parameters
        $payload = [
            'model' => $model,
            'messages' => $messages,
            'stream' => false,
            'temperature' => $options['temperature'] ?? 0.7,
            'max_completion_tokens' => $options['max_tokens'] ?? 2048,
        ];

        // Optional parameters
        if (isset($options['top_p'])) {
            $payload['top_p'] = $options['top_p'];
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->timeout($timeout)
                ->post("{$this->baseUrl}/text/chatcompletion_v2", $payload);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'message' => $data['choices'][0]['message']['content'] ?? '',
                    'usage' => $data['usage'] ?? null,
                ];
            }

            Log::error('MiniMax API error', [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload,
            ]);

            return $this->errorResponse($response->body());

        } catch (\Throwable $e) {
            Log::error('MiniMax API exception', ['error' => $e->getMessage()]);
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
