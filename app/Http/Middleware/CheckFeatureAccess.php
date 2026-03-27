<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Enums\FeatureType;
use App\Services\FeatureCreditService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware to check feature access based on subscription plan.
 *
 * Usage in routes:
 *   ->middleware('feature:ai_agents')
 *   ->middleware('feature:knowledge_base')
 *   ->middleware('feature:brand_kits')
 *   ->middleware('feature:sub_accounts')
 *
 * For credit-based features (post_generator, video_generator, etc.),
 * use the FeatureCreditService directly in controllers instead.
 */
class CheckFeatureAccess
{
    public function __construct(
        protected FeatureCreditService $featureCreditService
    ) {}

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (! $user) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'unauthenticated',
                    'message' => 'Authentication required.',
                ], 401);
            }

            return redirect()->route('login');
        }

        // Developer bypass
        if ($this->featureCreditService->isDeveloperBypass($user)) {
            return $next($request);
        }

        $tenant = $user->tenant;

        if (! $tenant) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'no_tenant',
                    'message' => 'No workspace found.',
                ], 403);
            }
            abort(403, 'No workspace found.');
        }

        // Try to parse the feature type
        $featureType = FeatureType::tryFrom($feature);

        if (! $featureType) {
            // Invalid feature type - allow through (fail open for safety)
            return $next($request);
        }

        // Check if the user can access this feature
        if (! $this->featureCreditService->canUseFeature($user, $featureType)) {
            $planRequired = $this->getRequiredPlanForFeature($featureType);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'error' => 'feature_locked',
                    'feature' => $featureType->value,
                    'feature_label' => $featureType->label(),
                    'message' => "This feature requires the {$planRequired} plan.",
                    'upgrade_url' => route('billing.upgrade'),
                ], 403);
            }

            return redirect()->route('billing.upgrade')
                ->with('error', "Upgrade to {$planRequired} to access {$featureType->label()}.");
        }

        return $next($request);
    }

    /**
     * Get the minimum plan required for a feature.
     */
    private function getRequiredPlanForFeature(FeatureType $feature): string
    {
        return match ($feature) {
            FeatureType::AI_AGENTS,
            FeatureType::KNOWLEDGE_BASE,
            FeatureType::BRAND_KITS => 'Pro',
            FeatureType::SUB_ACCOUNTS => 'Agency',
            default => 'Starter',
        };
    }
}
