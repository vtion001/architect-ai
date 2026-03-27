<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when a user has insufficient tokens for an operation.
 */
class InsufficientTokensException extends Exception
{
    public function __construct(
        public readonly int $requiredTokens,
        public readonly int $availableTokens = 0,
        ?string $message = null
    ) {
        $message = $message ?? "Insufficient tokens. This operation requires {$requiredTokens} tokens.";
        parent::__construct($message, 402);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'required_tokens' => $this->requiredTokens,
            'available_tokens' => $this->availableTokens,
        ], 402);
    }
}
