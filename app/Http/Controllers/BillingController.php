<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\FeatureType;
use App\Enums\PlanType;
use App\Services\FeatureCreditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller for subscription and billing management.
 *
 * Handles:
 * - Viewing current plan and feature credits
 * - Plan upgrade/downgrade landing pages
 * - Credit usage statistics
 */
class BillingController extends Controller
{
    public function __construct(
        protected FeatureCreditService $featureCreditService
    ) {}

    /**
     * Display the billing/subscription page.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;

        $currentPlan = $tenant?->getPlanType() ?? PlanType::STARTER;
        $credits = $this->featureCreditService->getUserCredits($user);

        // Get all plans for comparison
        $plans = [
            'starter' => [
                'name' => config('features.plans.starter.name'),
                'description' => config('features.plans.starter.description'),
                'current' => $currentPlan === PlanType::STARTER,
                'features' => $this->getPlanFeatures('starter'),
                'price' => 0, // Free tier
            ],
            'pro' => [
                'name' => config('features.plans.pro.name'),
                'description' => config('features.plans.pro.description'),
                'current' => $currentPlan === PlanType::PRO,
                'features' => $this->getPlanFeatures('pro'),
                'price' => 49, // Example pricing
            ],
            'agency' => [
                'name' => config('features.plans.agency.name'),
                'description' => config('features.plans.agency.description'),
                'current' => $currentPlan === PlanType::AGENCY,
                'features' => $this->getPlanFeatures('agency'),
                'price' => 149, // Example pricing
            ],
        ];

        return view('billing.billing', [
            'currentPlan' => $currentPlan->value,
            'credits' => $credits,
            'plans' => $plans,
            'tenant' => $tenant,
        ]);
    }

    /**
     * Show the upgrade page.
     */
    public function upgrade(Request $request)
    {
        $user = $request->user();
        $tenant = $user->tenant;
        $currentPlan = $tenant?->getPlanType() ?? PlanType::STARTER;

        return view('billing.upgrade', [
            'currentPlan' => $currentPlan->value,
            'tenant' => $tenant,
        ]);
    }

    /**
     * API endpoint to get current feature credits.
     */
    public function credits(Request $request): JsonResponse
    {
        $user = $request->user();
        $tenant = $user->tenant;

        if (! $tenant) {
            return response()->json([
                'success' => false,
                'error' => 'no_tenant',
                'message' => 'No workspace found.',
            ], 403);
        }

        $credits = $this->featureCreditService->getUserCredits($user);
        $currentPlan = $tenant->getPlanType();

        // Check access for gated features
        $featureAccess = [];
        foreach (FeatureType::accessGatedFeatures() as $feature) {
            $featureAccess[$feature->value] = [
                'label' => $feature->label(),
                'accessible' => $tenant->canAccessFeature($feature),
            ];
        }

        return response()->json([
            'success' => true,
            'plan' => [
                'type' => $currentPlan->value,
                'name' => $currentPlan->label(),
                'has_unlimited_credits' => $currentPlan->hasUnlimitedCredits(),
                'has_pro_features' => $currentPlan->hasProFeatures(),
                'can_create_sub_accounts' => $currentPlan->canCreateSubAccounts(),
            ],
            'credits' => $credits,
            'feature_access' => $featureAccess,
            'is_developer' => $this->featureCreditService->isDeveloperBypass($user),
        ]);
    }

    /**
     * Check if a specific feature is accessible.
     */
    public function checkFeature(Request $request, string $feature): JsonResponse
    {
        $user = $request->user();
        $featureType = FeatureType::tryFrom($feature);

        if (! $featureType) {
            return response()->json([
                'success' => false,
                'error' => 'invalid_feature',
                'message' => 'Invalid feature type.',
            ], 400);
        }

        $canUse = $this->featureCreditService->canUseFeature($user, $featureType);

        return response()->json([
            'success' => true,
            'feature' => $featureType->value,
            'feature_label' => $featureType->label(),
            'accessible' => $canUse,
            'is_credit_based' => $featureType->isCreditBased(),
            'is_access_gated' => $featureType->isAccessGated(),
        ]);
    }

    /**
     * Get the feature list for a plan.
     */
    private function getPlanFeatures(string $plan): array
    {
        $features = [];
        $planConfig = config("features.plans.{$plan}", []);

        // Add credit-based features
        foreach ($planConfig['credits'] ?? [] as $feature => $limit) {
            $featureType = FeatureType::tryFrom($feature);
            if ($featureType) {
                $features[] = [
                    'name' => $featureType->label(),
                    'value' => $limit === -1 ? 'Unlimited' : ($limit > 0 ? "{$limit}/month" : 'Not included'),
                    'included' => $limit !== 0,
                ];
            }
        }

        // Add access-gated features
        foreach ($planConfig['access'] ?? [] as $feature => $accessible) {
            $featureType = FeatureType::tryFrom($feature);
            if ($featureType) {
                $features[] = [
                    'name' => $featureType->label(),
                    'value' => $accessible ? 'Included' : 'Not included',
                    'included' => $accessible,
                ];
            }
        }

        return $features;
    }
}
