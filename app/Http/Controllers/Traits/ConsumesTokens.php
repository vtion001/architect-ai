<?php

declare(strict_types=1);

namespace App\Http\Controllers\Traits;

use App\Exceptions\InsufficientTokensException;
use App\Services\TokenService;
use Illuminate\Http\JsonResponse;

/**
 * Trait for controllers that consume tokens.
 * 
 * Follows DRY principle - centralizes token consumption logic
 * that was repeated across multiple controllers.
 */
trait ConsumesTokens
{
    /**
     * Attempt to consume tokens, returning a JSON error response if insufficient.
     *
     * @param int $amount Token amount to consume
     * @param string $reason Reason for consumption (for audit)
     * @param array $metadata Additional metadata for the transaction
     * @return JsonResponse|null Returns JsonResponse on failure, null on success
     */
    protected function consumeTokensOrFail(int $amount, string $reason, array $metadata = []): ?JsonResponse
    {
        $tokenService = app(TokenService::class);
        
        if (!$tokenService->consume(auth()->user(), $amount, $reason, $metadata)) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. This operation requires {$amount} tokens.",
                'required_tokens' => $amount,
            ], 402);
        }

        return null;
    }

    /**
     * Refund tokens on failed operation.
     *
     * @param int $amount Amount to refund
     * @param string $reason Reason for refund
     * @return void
     */
    protected function refundTokens(int $amount, string $reason): void
    {
        $tokenService = app(TokenService::class);
        $tokenService->grant(auth()->user()->tenant, $amount, $reason);
    }

    /**
     * Get current token balance for the authenticated user's tenant.
     *
     * @return int
     */
    protected function getTokenBalance(): int
    {
        $tokenService = app(TokenService::class);
        return $tokenService->getBalance(auth()->user()->tenant);
    }
}
