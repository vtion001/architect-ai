<?php

declare(strict_types=1);

namespace App\Http\Controllers\Traits;

use App\Services\TokenService;
use Illuminate\Http\JsonResponse;

/**
 * Trait for controllers that consume tokens.
 * 
 * Provides convenient methods for token consumption with
 * automatic error responses and balance checking.
 * 
 * @see config/tokens.php for cost configuration
 */
trait ConsumesTokens
{
    /**
     * Consume tokens by operation type from config.
     * 
     * @param string $operation Operation key from config/tokens.php
     * @param array $metadata Additional metadata for the transaction
     * @return JsonResponse|null Returns JsonResponse on failure, null on success
     */
    protected function consumeTokens(string $operation, array $metadata = []): ?JsonResponse
    {
        $tokenService = app(TokenService::class);
        $cost = $tokenService->getCost($operation);
        
        return $this->consumeTokensAmount($cost, $operation, $metadata);
    }

    /**
     * Consume a specific amount of tokens.
     *
     * @param int $amount Token amount to consume
     * @param string $reason Reason for consumption (for audit)
     * @param array $metadata Additional metadata for the transaction
     * @return JsonResponse|null Returns JsonResponse on failure, null on success
     */
    protected function consumeTokensAmount(int $amount, string $reason, array $metadata = []): ?JsonResponse
    {
        $tokenService = app(TokenService::class);
        
        if (!$tokenService->consume(auth()->user(), $amount, $reason, $metadata)) {
            return response()->json([
                'success' => false,
                'message' => "Insufficient tokens. This operation requires {$amount} tokens.",
                'required_tokens' => $amount,
                'current_balance' => $this->getTokenBalance(),
            ], 402);
        }

        return null;
    }

    /**
     * Refund tokens on failed operation.
     */
    protected function refundTokens(int $amount, string $reason): void
    {
        $tokenService = app(TokenService::class);
        $tokenService->refund(auth()->user()->tenant, $amount, $reason);
    }

    /**
     * Get current token balance for the authenticated user's tenant.
     */
    protected function getTokenBalance(): int
    {
        $tokenService = app(TokenService::class);
        return $tokenService->getBalance(auth()->user()->tenant);
    }

    /**
     * Check if user has enough tokens for an operation.
     */
    protected function hasTokensFor(string $operation): bool
    {
        $tokenService = app(TokenService::class);
        $cost = $tokenService->getCost($operation);
        return $tokenService->hasBalance(auth()->user()->tenant, $cost);
    }
}
