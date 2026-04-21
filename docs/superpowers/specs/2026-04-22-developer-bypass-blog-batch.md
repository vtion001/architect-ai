# Spec: Developer Bypass — Blog Batch Generation

**Date:** 2026-04-22
**Status:** Approved

## Problem

`admin@dev.local` gets "You've reached your monthly limit for Blog Generator" error when attempting batch blog generation, even though the developer bypass was configured. Two issues:

1. `config:clear` had not been run — config cache held stale value
2. `batchStore()` lacks feature credit check (inconsistent with `store()`)

## Scope

Only `ContentCreatorController.php`. No changes to `FeatureCreditService`, `TokenService`, or any other file.

## Changes

### 1. `batchStore()` — Add feature credit check

Add at top of `batchStore()` method, mirroring `store()`:

```php
$user = auth()->user();
if (! $this->featureCreditService->canUseFeature($user, FeatureType::BLOG_GENERATOR)) {
    return response()->json([
        'success' => false,
        'error' => 'credit_exhausted',
        'message' => "You've reached your monthly limit for {$featureType->label()}. Upgrade to Pro for unlimited access.",
        'feature' => $featureType->value,
        'upgrade_url' => route('billing.upgrade'),
    ], 402);
}
$this->featureCreditService->consumeCredit($user, FeatureType::BLOG_GENERATOR);
```

### 2. `batchStore()` — Fix validation syntax

```php
// Before
'count' => 'required|integer|min=1|max:3',
// After
'count' => 'required|integer|min:1|max:3',
```

### 3. `store()` — Add server-side count max

Add `max:10` to the count parameter to prevent oversized single blog requests.

## Constraints

- Developer bypass applies ONLY to `admin@dev.local` (email-gated via `config('iam.developer_email')`)
- No changes to any other controller or service
- All 53 existing PHPUnit tests must pass
- No changes to database schema
