<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when a user has reached their personal token limit.
 */
class UserTokenLimitExceededException extends Exception
{
    public function __construct(
        public readonly int $limit,
        public readonly int $used,
        ?string $message = null
    ) {
        $message = $message ?? "You have reached your personal monthly token limit ({$limit}).";
        parent::__construct($message, 403);
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(): \Illuminate\Http\JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'limit' => $this->limit,
            'used' => $this->used,
            'suggestion' => 'Ask your administrator to increase your monthly quota.',
        ], 403);
    }
}
