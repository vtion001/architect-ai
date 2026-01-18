<?php

declare(strict_types=1);

namespace App\DTOs;

/**
 * Result DTO for AI chat processing.
 * 
 * Immutable data transfer object representing the result
 * of processing an AI chat message.
 */
readonly class ChatProcessingResult
{
    public function __construct(
        public bool $success,
        public string $message,
        public ?string $error = null,
        public ?array $metadata = null
    ) {}

    /**
     * Create a successful result.
     */
    public static function success(string $message, ?array $metadata = null): self
    {
        return new self(
            success: true,
            message: $message,
            metadata: $metadata
        );
    }

    /**
     * Create a failed result.
     */
    public static function failure(string $message, string $error): self
    {
        return new self(
            success: false,
            message: $message,
            error: $error
        );
    }

    /**
     * Convert to array for JSON responses.
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'error' => $this->error,
            'metadata' => $this->metadata,
        ];
    }
}
