# architect-ai Image Creation — Implementation Plan

**Goal:** Fix all 6 image creation issues in architect-ai without breaking existing functionality.

**Architecture:** All fixes are in-place modifications to existing Laravel controller/service methods. No new files required except tests. Each fix is independently testable.

**Tech Stack:** Laravel 11, SQLite (dev), OpenAI DALL-E 3, CloudinaryService, TokenService

---

## Task 1: Fix Token Cost Config Bypass (Issue #3) 🔴

**Files:**
- Modify: `app/Http/Controllers/ContentCreatorController.php:1002` (generateMedia)
- Modify: `app/Http/Controllers/ContentCreatorController.php:465` (generateBulkImages)

**Hypothesis:** Token cost is hardcoded to 5 instead of using `config('tokens.costs.image_generation')` which is 20.

- [ ] **Step 1: Fix hardcoded token cost in generateMedia()**
  Line ~1002, change `$tokenCost = 5;` to `$tokenCost = config('tokens.costs.image_generation', 20);`

- [ ] **Step 2: Fix hardcoded token cost in generateBulkImages()**
  Line ~465, change `$tokenCost = $count * 5;` to `$tokenCost = $count * config('tokens.costs.image_generation', 20);`

- [ ] **Step 3: Verify no other hardcoded `5` for token costs exist in image-related methods**
  Search: `tokenCost.*=.*5` in ContentCreatorController.php

- [ ] **Step 4: Run tests**
  Run: `php artisan test`
  Expected: All tests pass

---

## Task 2: Fix Bulk Images Stub — Remove Token Theft (Issue #1) 🔴

**Files:**
- Modify: `app/Http/Controllers/ContentCreatorController.php:444-493`

**Hypothesis:** Method charges tokens but never generates images. Remove token charge until properly implemented.

- [ ] **Step 1: Read current generateBulkImages method**
  Read lines 444-493 to confirm exact content.

- [ ] **Step 2: Replace method body with disabled version**
  Keep method signature, replace body with 501 response:
  ```php
  public function generateBulkImages(Request $request)
  {
      $request->validate([
          'framework_id' => 'required|exists:contents,id',
          'style' => 'nullable|string|in:realistic,poster,asset-reference',
      ]);

      return response()->json([
          'success' => false,
          'message' => 'Bulk image generation is not yet available. Please generate featured images individually for each post.',
          'count' => 0,
      ], 501);
  }
  ```

- [ ] **Step 3: Verify no tokenService calls remain in method**
  Search for `tokenService`, `consume`, `tokenCost` in the new method body — all should be absent.

- [ ] **Step 4: Run tests**
  Run: `php artisan test`
  Expected: All pass

---

## Task 3: Fix Cloudinary Fallback — Prevent Broken URLs (Issue #2) 🟠

**Files:**
- Modify: `app/Services/CloudinaryService.php:203-245` (saveToLocalStorage)
- Modify: `app/Http/Controllers/ContentCreatorController.php:1029-1045` (generateMedia error handling)

**Hypothesis:** When Cloudinary fails, saveToLocalStorage rejects DALL-E URLs (wrong allowlist) and returns raw temp URL which expires.

- [ ] **Step 1: Fix saveToLocalStorage allowed hosts**
  Add OpenAI CDN to allowed hosts:
  ```php
  $allowedHosts = [
      'res.cloudinary.com',
      'api.cloudinary.com',
      'oaidalleapiprodscus.blob.core.windows.net', // OpenAI DALL-E temp URLs
  ];
  ```

- [ ] **Step 2: Return error indicator instead of broken URL**
  When URL is not storable, return `['url' => null, 'source' => 'upload_failed']` instead of returning the raw temp URL.

- [ ] **Step 3: Update generateMedia() error handling**
  After `$uploadResult = $cloudinaryService->uploadFromUrl(...)`, check if url is null/empty and return proper 500 error to user instead of proceeding.

- [ ] **Step 4: Run tests**
  Run: `php artisan test`
  Expected: All pass

---

## Task 4: Add Rate Limit to /generate-image-prompt (Issue #4) 🟢

**Files:**
- Modify: `routes/web.php:180`

- [ ] **Step 1: Add throttle middleware to route**
  Change:
  ```php
  Route::post('/content-creator/generate-image-prompt', [...])->name(...);
  ```
  To:
  ```php
  Route::post('/content-creator/generate-image-prompt', [...])
      ->middleware('throttle:10,1')
      ->name(...);
  ```

- [ ] **Step 2: Run tests**
  Run: `php artisan test`
  Expected: All pass

---

## Task 5: Improve Generic Fallback Prompt (Issue #6) 🟡

**Files:**
- Modify: `app/Http/Controllers/ContentCreatorController.php:788-791`

- [ ] **Step 1: Read current fallback at line 788-791**
  Confirm exact content.

- [ ] **Step 2: Replace generic prompt with structured cinematic prompt**
  Replace the 1-line generic prompt with a properly structured DALL-E prompt covering: subject, mood, lighting, composition, quality standards.

- [ ] **Step 3: Run tests**
  Run: `php artisan test`
  Expected: All pass

---

## Task 6: Add MiniMax Fallback for Image Generation (Issue #5) 🟡

**Files:**
- Modify: `app/Services/ContentService.php` (add MiniMax image gen method)
- Modify: `app/Http/Controllers/ContentCreatorController.php:1029` (cascade call)

**Hypothesis:** MiniMax has image generation API. Adding it as fallback follows established pattern from text generation.

- [ ] **Step 1: Check MiniMaxClient for image support**
  Search for `image` in MiniMaxClient.php.

- [ ] **Step 2: Add generateImageMiniMax() to ContentService**
  Add method that calls MiniMax image generation API (model: minimax-image-01).

- [ ] **Step 3: Update generateMedia() to cascade**
  After DALL-E returns null, call MiniMax fallback.

- [ ] **Step 4: Run tests**
  Run: `php artisan test`
  Expected: All pass

---

## Final Verification

- [ ] Run: `php artisan test` — all pass
- [ ] Run: `git add -A && git commit -m "fix: image creation issues (token costs, bulk stub, Cloudinary fallback, rate limits, MiniMax cascade)"`
- [ ] Run: `git push`
