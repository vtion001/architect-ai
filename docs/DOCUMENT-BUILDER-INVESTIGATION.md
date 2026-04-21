# Document Builder Investigation

**Date:** 2026-04-22
**Author:** Yana 🤖
**Status:** Investigation complete — no changes made

---

## Executive Summary

Four areas were investigated at Vincent's request. Three issues found, one confirmed working.

| # | Area | Status | Action Needed |
|---|---|---|---|
| 1 | AI Client (MiniMax vs OpenAI) | ❌ Wrong client configured | Swap to OpenAI |
| 2 | Template Views | ✅ All 11 exist | None |
| 3 | ReportBuilderController vs DocumentBuilderController | ⚠️ Dead code | Archive |
| 4 | Developer Bypass | ✅ Already works | None |

---

## Finding 1: Wrong AI Client — MiniMax with Dismissed Key

### Current State

`BaseGenerator` (parent class for all document generators) uses `MiniMaxClient` for content generation:

```php
// app/Services/Generators/BaseGenerator.php
use App\Services\AI\MiniMaxClient;

public function __construct(
    protected BrandResolverService $brandResolverService,
    protected SampleContentProvider $sampleContentProvider,
    protected MiniMaxClient $miniMaxClient   // ← Wrong client
) {}
```

The `MiniMaxClient` reads its API key from `config('services.minimax.key')` which maps to `MINIMAX_API_KEY` in `.env`:

```
# .env.docker
MINIMAX_API_KEY=dismissed     ← No real key, all generations fall back to sample content
OPENAI_API_KEY=sk-proj-...    ← Real key, not being used
```

### Result

**Every document generation silently falls back to sample content.** The AI call to MiniMax fails immediately because the API key is invalid, so users never see AI-generated documents — only the `SampleContentProvider` fallback HTML.

### Fix

Change `BaseGenerator` to use `OpenAIClient` instead:

```php
use App\Services\AI\OpenAIClient;  // ← Change this

protected OpenAIClient $aiClient   // ← And this
```

Both clients have the same `chat()` interface — same method signature, same return format. Only the `use` import and property name need changing.

### Files to Change

- `app/Services/Generators/BaseGenerator.php`
  - Line 8: `use App\Services\AI\MiniMaxClient;` → `use App\Services\AI\OpenAIClient;`
  - Line 32: `protected MiniMaxClient $miniMaxClient` → `protected OpenAIClient $aiClient`
  - Line 47: `$this->miniMaxClient->chat(...)` → `$this->aiClient->chat(...)`
  - Line 68: `$this->miniMaxClient->chat(...)` → `$this->aiClient->chat(...)`

### Impact

- Zero risk — both clients have identical `chat()` signatures
- Documents will actually use OpenAI instead of falling back to sample content
- Requires a valid `OPENAI_API_KEY` in `.env` (already present in `.env.docker`)

---

## Finding 2: Template Views — All Exist ✅

All 11 Blade templates are present and correctly named:

```
resources/views/reports/
├── executive-summary.blade.php
├── market-analysis.blade.php
├── financial-overview.blade.php
├── competitive-intelligence.blade.php
├── infographic.blade.php
├── trend-analysis.blade.php
├── proposal.blade.php
├── contract.blade.php
├── cv-resume.blade.php
├── cover-letter.blade.php
├── custom.blade.php
└── layout.blade.php
```

Each template has a matching entry in `ReportTemplate::view()` enum method. **No missing templates.**

---

## Finding 3: ReportBuilderController — Dead Code

### Two Controllers Exist

| Controller | Routes | Document Builder Credit Check | Queue Job | Resume Parsing |
|---|---|---|---|---|
| `DocumentBuilderController` | `/document-builder/*` (6 routes) | ✅ Yes | ✅ `GenerateDocument` | ✅ Yes |
| `ReportBuilderController` | **None** | ❌ No | ❌ Sync | ❌ No |

### ReportBuilderController Has No Routes

```bash
$ grep -r "ReportBuilderController" routes/
# No results — no routes point to this controller
```

It also references a view that doesn't exist: `report-builder.report-builder` (which would cause a crash if ever called).

### Key Differences

| Feature | DocumentBuilderController | ReportBuilderController |
|---|---|---|
| Feature credit check | ✅ `canUseFeature(DOCUMENT_BUILDER)` | ❌ None |
| Queue job | ✅ `GenerateDocument::dispatch()` | ❌ Synchronous |
| Token cost | 30 tokens | 30 tokens |
| Resume parsing | ✅ `parseResume()` | ❌ |
| Cover letter drafting | ✅ `draftCoverLetter()` | ❌ |
| Photo upload | ✅ `uploadPhoto()` | ❌ |
| Preview service | ✅ `TemplatePreviewService` | ✅ `ReportService::generatePreviewHtml()` |

### Recommendation

Archive `ReportBuilderController` — it is dead code that duplicates the simpler parts of `DocumentBuilderController` without adding value. It is never called from any route.

**File to archive:** `app/Http/Controllers/ReportBuilderController.php`

---

## Finding 4: Developer Bypass — Already Works ✅

`DocumentBuilderController::generate()` calls:

```php
if (! $this->featureCreditService->canUseFeature($user, FeatureType::DOCUMENT_BUILDER)) {
    return response()->json([...], 402);
}
$this->featureCreditService->consumeCredit($user, FeatureType::DOCUMENT_BUILDER);

// Token consumption
if (! $this->tokenService->consume($user, 30, 'report_generation', [...])) {
    return response()->json([...], 402);
}
```

Both `canUseFeature()` and `consumeCredit()` call `isDeveloperBypass()` internally, which returns `true` for `admin@dev.local` because `config('iam.developer_email')` = `admin@dev.local`.

**No action needed.** Vincent can generate unlimited documents right now.

---

## Architecture Reference

```
DocumentBuilderController (ACTIVE)
├── FeatureCreditService.canUseFeature() → developer bypass ✅
├── TokenService.consume() → developer bypass ✅
├── ReportService (Factory)
│   ├── CvResumeGenerator → MiniMaxClient ❌ (wrong AI)
│   ├── CoverLetterGenerator → MiniMaxClient ❌
│   ├── ProposalGenerator → MiniMaxClient ❌
│   ├── ContractGenerator → MiniMaxClient ❌
│   └── ReportsGenerator → MiniMaxClient ❌
├── TemplatePreviewService (decoupled) ✅
├── GenerateDocument (queue job) ✅
├── ResumeParserService ✅
└── CoverLetterDraftService ✅

ReportBuilderController (DEAD — no routes)
├── Same ReportService calls
├── Synchronous generation (no queue)
└── No credit checks
```

---

## Action Items

| Priority | Action | Files |
|---|---|---|
| P0 — Critical | Swap `MiniMaxClient` → `OpenAIClient` in `BaseGenerator` | `app/Services/Generators/BaseGenerator.php` |
| P2 — Cleanup | Archive `ReportBuilderController` | `app/Http/Controllers/ReportBuilderController.php` |
| None | Developer bypass | Already working ✅ |
| None | Template views | All exist ✅ |
