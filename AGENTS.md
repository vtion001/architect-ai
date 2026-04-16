# AGENTS.md — Architect AI

See `CLAUDE.md` for the full project reference. This file covers only the high-signal, repo-specific facts agents would likely miss without help.

## Key Commands

```bash
# Backend
php artisan serve                    # Start dev server
php artisan queue:work               # Start queue worker (needs app + db + qdrant containers)
php artisan migrate                  # DB migrations
php artisan db:seed                  # Seed DB
php artisan db:seed --class=IAMSeeder  # IAM seed data

# Frontend (Vite dev server: localhost:5175, proxies to Docker nginx at :8081)
npm run dev                          # Dev server
npm run build                        # Production build (runs `rm -f public/hot && vite build` first)
npm run type-check                   # tsc --noEmit (JS only, no .ts files in project)

# Code quality
./vendor/bin/pint                    # Laravel code style (no pint.json = uses defaults)
./vendor/bin/phpunit                 # Run all tests
./vendor/bin/phpunit --filter=TestName   # Run single test
./vendor/bin/phpunit --testsuite=Feature # Feature or Unit suite only

# Docker
docker-compose up -d                 # All services
docker-compose up -d app            # App only
docker-compose up -d queue          # Queue worker only
docker-compose logs -f app         # Tail app logs
docker-compose exec db mysql -u root -p   # MySQL shell
```

## Testing

- **Tests run against SQLite in-memory** — no MySQL, Qdrant, or Docker services needed locally.
- `phpunit.xml` sets: `DB_CONNECTION=sqlite`, `DB_DATABASE=:memory:`, `QUEUE_CONNECTION=sync`, `BCRYPT_ROUNDS=4`.
- Run `./vendor/bin/phpunit` before committing (no DB required).

## Architecture

### Multi-Tenant
- `Tenant → SubAccount → Brand → User → Role → Permission`.
- Tenant context resolved by `TenantMiddleware`: session hot-swap (`session('current_tenant_id')`), domain-based, or `X-Tenant-Slug` header.
- Models use `tenant_id` foreign key with global scopes for automatic query scoping.
- **Major tables use UUIDs** as primary keys: `users`, `contents`, `documents`, `research`.

### Auth Split
- `routes/api.php` → `auth:sanctum` (SPA token auth).
- `routes/web.php` → session auth.

### Middleware Stack
`tenant` → `session_security` → `mfa` → `can`

### AI Service Layer
- **Primary**: MiniMax (`app/Services/AI/MiniMaxClient.php`). Fallbacks: OpenAI, OpenRouter, Perplexity.
- Token consumption happens **before** AI calls; refunds on failure.
- Vector search via Qdrant (1536 dimensions, collection `knowledge_base`).

### Document Generation
`ReportService` coordinates generators under `app/Services/Generators/`:
- `CvResumeGenerator`, `CoverLetterGenerator`, `ProposalGenerator`, `ContractGenerator`, `ReportsGenerator` (7 types)

### Queue Worker Dependencies
The `queue` container requires: `app`, `db`, `qdrant` to be running.

## Frontend Build (vite.config.js)

- Dev proxy routes `/api`, `/auth`, `/dashboard`, `/admin` to `http://localhost:8081`.
- Production: terser minification (drops `console`/`debugger`).
- Vendor chunks: `vendor-alpine`, `vendor-chart`, `vendor-icons`, `vendor-stoplight`, `vendor-axios`.

### Blog Batch Generation
- `GenerateBlogBatch` job (1 angle extraction + N blog generations) dispatches from `POST /content-creator/blog/batch`.
- Token cost: `count * 20 + 20` (1 extra call for angle extraction).
- Child `Content` records created with `tenant_id`, `parent_batch_id`, `batch_index`, and `angle` in options.
- Frontend polls `/content-creator/{id}` every 2s (max 60 polls) via `pollForBatch()` in `content-creator.js` until status reaches `completed` or `failed`.
- `getChildren()` endpoint returns batch children ordered by `batch_index` for the success modal.

## Key Files

- `CLAUDE.md` — Full project reference. **Read this first.**
- `docker-compose.yml` — Services: `app`, `queue`, `web`, `db`, `qdrant`.
- `.env.docker.example` — Docker env vars.
- `.env` — Local env (not in git).
- `vite.config.js` — Frontend build + proxy.
- `tsconfig.json` — Paths alias: `@/*` → `resources/js/*`.
- `app/Http/Controllers/` — Grouped by feature (`Auth/`, `Tenant/`, `Admin/`).
- `app/Jobs/GenerateBlogBatch.php` — Blog batch job (angle extraction + N blog posts).
- `tests/Unit/Jobs/GenerateBlogBatchTest.php` — Unit tests for angle parsing logic.
- `tests/e2e/blog-batch.spec.ts` — Playwright E2E tests (4 tests, all passing).

## Watch & Validate

Automation for test-fix-validate using the `testing-automation` toolkit on Desktop.

```bash
# Cron: run on every commit
*/5 * * * * cd ~/testing-automation && node scripts/watch-and-validate.js --once --project /path/to/architect-ai

# Daemon: watch current project live
cd ~/testing-automation && node scripts/watch-and-validate.js --daemon --project /path/to/architect-ai
```

**Test command:** `./vendor/bin/phpunit && npx playwright test`  
**E2E Playwright:** `node_modules/.bin/playwright.cmd test tests/e2e/blog-batch.spec.ts`  
**Report:** `test-results/`  
**Skills:** systematic-debugging, verification-before-completion, test-master

### Skills to load (dynamic)

- `systematic-debugging` — Load BEFORE proposing any fix
- `verification-before-completion` — Load BEFORE marking done
- `test-master` — For writing new tests or improving coverage

### Root causes found in this project

- `Content::create()` on loaded Eloquent model — impossible to mock in unit tests
- `Content` uses `BelongsToTenant` with global scope — orphaned children are silently invisible
- `batchStore()` uses `auth()->user()` in controller — test must use `actingAs()`
- Dev user token balance must be > 0 for real API calls (use `DevUserSeeder` for seeding)
