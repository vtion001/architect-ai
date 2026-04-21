# Developer Bypass — Blog Batch Implementation Plan

> **For agentic workers:** Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add feature credit check to `batchStore()`, fix validation syntax, and add server-side count max — all scoped to `ContentCreatorController.php` only.

**Architecture:** Three targeted edits to `ContentCreatorController.php`. No new services, no schema changes, no changes to any other file.

**Tech Stack:** Laravel, FeatureCreditService, TokenService

---

## File Map

- Modify: `app/Http/Controllers/ContentCreatorController.php`
  - `batchStore()` (line ~295) — add feature credit check + fix validation syntax
  - `store()` (line ~70) — add `count` max validation
- Test: `tests/Feature/ContentCreatorTest.php` (existing) + Playwright E2E

---

## Task 1: Write Failing Test for `batchStore()` Feature Credit Check

**File:** Create `tests/Feature/ContentCreatorBatchTest.php`

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Enums\FeatureType;
use App\Models\User;
use App\Services\FeatureCreditService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentCreatorBatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_batch_store_requires_feature_credit(): void
    {
        // Setup: create user with exhausted blog_generator credit
        $user = User::factory()->create();
        $user->tenant->update(['plan' => 'starter']);

        // Exhaust the blog generator credit
        $fcs = new FeatureCreditService();
        $fcs->provisionCreditsForUser($user);
        $credit = $fcs->getUserCredit($user, FeatureType::BLOG_GENERATOR);
        $credit->update(['limit' => 0, 'used' => 1]);

        $this->actingAs($user);

        $response = $this->postJson('/content-creator/blog/batch', [
            'topic' => 'Test Topic',
            'count' => 2,
        ]);

        $response->assertStatus(402)
            ->assertJson([
                'success' => false,
                'error' => 'credit_exhausted',
            ]);
    }

    public function test_developer_bypasses_batch_feature_credit_check(): void
    {
        // Setup: developer user (is_developer = true via email match)
        $user = User::factory()->create([
            'email' => config('iam.developer_email'),
        ]);
        $user->tenant->update(['plan' => 'starter']);

        // Exhaust credit
        $fcs = new FeatureCreditService();
        $fcs->provisionCreditsForUser($user);
        $credit = $fcs->getUserCredit($user, FeatureType::BLOG_GENERATOR);
        $credit->update(['limit' => 0, 'used' => 1]);

        $this->actingAs($user);

        $response = $this->postJson('/content-creator/blog/batch', [
            'topic' => 'Test Topic',
            'count' => 2,
        ]);

        // Developer should bypass credit check — should get 200/201, not 402
        $this->assertNotEquals(402, $response->status());
    }

    public function test_batch_store_validation_count_min_syntax(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // count < 1 should fail validation
        $response = $this->postJson('/content-creator/blog/batch', [
            'topic' => 'Test Topic',
            'count' => 0,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['count']);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

```bash
php artisan test tests/Feature/ContentCreatorBatchTest.php
```

Expected: `test_batch_store_requires_feature_credit` PASSES (credit system works), but `test_developer_bypasses_batch_feature_credit_check` **FAILS** with 402 (developer bypass not implemented in batchStore yet).

- [ ] **Step 3: Implement feature credit check in `batchStore()`**

Find `batchStore()` in `ContentCreatorController.php` (around line 295). Add after the validation block, before token consumption:

```php
// Check and consume feature credit
$user = auth()->user();
if (! $this->featureCreditService->canUseFeature($user, FeatureType::BLOG_GENERATOR)) {
    return response()->json([
        'success' => false,
        'error' => 'credit_exhausted',
        'message' => "You've reached your monthly limit for " . FeatureType::BLOG_GENERATOR->label() . ". Upgrade to Pro for unlimited access.",
        'feature' => FeatureType::BLOG_GENERATOR->value,
        'upgrade_url' => route('billing.upgrade'),
    ], 402);
}
$this->featureCreditService->consumeCredit($user, FeatureType::BLOG_GENERATOR);
```

- [ ] **Step 4: Fix validation syntax in `batchStore()`**

```php
// Before
'count' => 'required|integer|min=1|max:3',
// After
'count' => 'required|integer|min:1|max:3',
```

- [ ] **Step 5: Run tests to verify they pass**

```bash
php artisan test tests/Feature/ContentCreatorBatchTest.php
```

Expected: ALL PASS

- [ ] **Step 6: Run full test suite**

```bash
php artisan test
```

Expected: All 53+ tests pass

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/ContentCreatorController.php tests/Feature/ContentCreatorBatchTest.php
git commit -m "feat: add feature credit check to batchStore(), fix validation syntax"
```

---

## Task 2: Add Server-Side Count Max to `store()`

**File:** Modify `app/Http/Controllers/ContentCreatorController.php` — `store()` method

- [ ] **Step 1: Find the existing validation for `count` in `store()` or `StoreContentRequest`**

Check `app/Http/Requests/StoreContentRequest.php` for existing validation rules on `count`.

- [ ] **Step 2: Add `max:10` to count validation**

```php
'count' => 'nullable|integer|min:1|max:10',
```

- [ ] **Step 3: Run tests**

```bash
php artisan test
```

Expected: All pass

- [ ] **Step 4: Commit**

```bash
git add app/Http/Requests/StoreContentRequest.php
git commit -m "feat: add server-side count max to store()"
```

---

## Task 3: Playwright E2E Verification

**File:** `architect-ai/sys_test_batch.cjs` (temporary test)

- [ ] **Step 1: Write Playwright E2E test**

```javascript
const { chromium } = require('@playwright/test');
const BASE = 'http://127.0.0.1:8092';

(async () => {
  const browser = await chromium.launch({ headless: true });
  const ctx = await browser.newContext();
  const page = await ctx.newPage();

  // Login
  await page.goto(`${BASE}/auth/login`, { waitUntil: 'networkidle' });
  await page.fill('input[type="email"]', 'admin@dev.local');
  await page.fill('input[type="password"]', 'password123');
  await Promise.all([
    page.waitForResponse(r => r.url().includes('/auth/login') && r.request().method() === 'POST', { timeout: 10000 }),
    page.click('button[type="submit"]'),
  ]);
  await page.waitForLoadState('networkidle');
  await page.waitForTimeout(1500);
  console.log('Login:', page.url().includes('dashboard') ? 'OK' : 'FAIL');

  // Navigate to content-creator
  await page.goto(`${BASE}/content-creator`, { waitUntil: 'networkidle' });
  await page.waitForTimeout(1000);

  // Click Blog tab
  const blogTab = page.locator('button:has-text("Blog")').first();
  await blogTab.click();
  await page.waitForTimeout(500);

  // Switch to Batch mode
  const batchTab = page.locator('button:has-text("Batch Generate")').first();
  await batchTab.click();
  await page.waitForTimeout(500);

  // Fill topic
  const topicInput = page.locator('input[placeholder*="blog" i], input[placeholder*="topic" i]').first();
  await topicInput.fill('The Future of AI in Healthcare: A Complete Guide');

  // Wait a moment
  await page.waitForTimeout(1000);

  // Click generate button
  const generateBtn = page.locator('button:has-text("Generate"), button:has-text("Generate Blog")').first();
  await generateBtn.click();

  // Wait for response
  await page.waitForTimeout(3000);

  // Check for "monthly limit" error in page
  const bodyText = await page.locator('body').innerText();
  if (bodyText.includes('monthly limit') || bodyText.includes('Upgrade to Pro')) {
    console.log('FAIL: Still showing monthly limit error');
    console.log('Page text sample:', bodyText.substring(0, 500));
  } else {
    console.log('PASS: No monthly limit error — developer bypass working');
  }

  await browser.close();
})();
```

- [ ] **Step 2: Ensure server running on port 8092, then execute**

```bash
node sys_test_batch.cjs
```

Expected: `PASS: No monthly limit error`

- [ ] **Step 3: Clean up test file**

```bash
rm sys_test_batch.cjs
```

---
