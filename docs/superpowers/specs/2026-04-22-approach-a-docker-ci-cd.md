# Approach A Spec: Dockerize architect-ai with CI/CD

**Date:** 2026-04-22
**Status:** Draft — for review only. No implementation yet.
**Author:** Yana 🤖

---

## Context

architect-ai needs reliable CI/CD and a production-ready Docker setup. The app currently:
- Runs on `php artisan serve` locally (PHP built-in server)
- Has 57 PHPUnit tests that only run locally — no CI
- Has a broken `Dockerfile` (incomplete, no build steps)
- Deploys to Render via `render.yaml` but the Dockerfile is not actually used by Render
- Uses **SQLite in production** (`.env`) and **MySQL in docker-compose** (`.env.docker`) — inconsistency

---

## Goals

1. Fix the broken `Dockerfile` so it actually builds the app
2. Add `tests.yml` CI workflow — PHPUnit + Playwright on every push/PR
3. Ensure Docker Compose gives a dev environment that mirrors production (SQLite, not MySQL)
4. Add `/healthcheck` route for container health monitoring
5. Make Render deploys use the proper Dockerfile

---

## Current State

| Component | Status | Issue |
|-----------|--------|-------|
| `Dockerfile` | Broken | No `COPY`, no `composer install`, no build steps |
| `docker-compose.yml` | Misconfigured | No `entrypoint` reference, MySQL defined but env uses `CACHE_DRIVER=file` + `QUEUE_CONNECTION=sync` |
| `docker/entrypoint.sh` | Exists | Not called by docker-compose |
| `HEALTHCHECK_PATH=/healthcheck` | Unused | Env var set but no route exists |
| CI tests | Missing | No `tests.yml` — 57 tests only run locally |
| Render deploy | Partial | Uses host build, not Dockerfile |
| Production DB | SQLite | `.env` uses `DB_CONNECTION=sqlite` |
| Docker DB | MySQL | `.env.docker` uses MySQL — **inconsistent** |

---

## Proposed Changes

### 1. Fix `Dockerfile`

**File:** `Dockerfile`

```dockerfile
FROM serversideup/php:8.3-fpm-nginx

USER root

# Copy dependency files first (layer caching)
COPY composer.json composer.lock* ./
RUN composer install --no-dev --optimize-autoloader

COPY package.json package-lock.json* ./
RUN npm ci && npm run build

# Copy application source
COPY . .

# Fix storage permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache && \
    chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8080

# Healthcheck
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
  CMD curl -f http://localhost/healthcheck || exit 1

ENTRYPOINT ["./docker/entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
```

**Rationale:**
- Multi-stage layer caching: dependency files copied and installed before source code
- `npm ci` (not `npm install`) for reproducible builds
- `HEALTHCHECK` for container orchestrator visibility
- `ENTRYPOINT` points to existing `entrypoint.sh`

---

### 2. Fix `docker-compose.yml`

**File:** `docker-compose.yml`

Add `entrypoint` to the `app` service so the existing `entrypoint.sh` is actually called:

```yaml
app:
  build:
    context: .
    dockerfile: Dockerfile
  entrypoint: ['./docker/entrypoint.sh']   # ← Add this line
  env_file:
    - .env.docker
  # ... rest unchanged
```

**Note:** Also update `.env.docker` to use `DB_CONNECTION=sqlite` instead of MySQL (see below) to match production.

---

### 3. Update `.env.docker` to use SQLite

**File:** `.env.docker`

Change from MySQL to SQLite to match production:

```env
# Change from:
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=architect_ai
DB_USERNAME=root
DB_PASSWORD=your-password-here

# To:
DB_CONNECTION=sqlite
```

Remove the MySQL-related variables. Remove the `db` service from `docker-compose.yml` or keep it for reference but don't connect the app to it.

---

### 4. Add `/healthcheck` route

**File:** `routes/web.php`

Add a route that returns a simple health response:

```php
Route::get('/healthcheck', function () {
    return response()->json([
        'status' => 'ok',
        'app' => config('app.name'),
        'time' => now()->toISOString(),
    ]);
})->withoutMiddleware(['auth', 'tenant', \App\Http\Middleware\SessionSecurityMiddleware::class]);
```

**Rationale:** The `HEALTHCHECK` in Dockerfile uses this endpoint. Also useful for Render and load balancer health probes.

---

### 5. Add `tests.yml` CI workflow

**File:** `.github/workflows/tests.yml`

```yaml
name: Tests

on:
  push:
    branches: [main, master]
  pull_request:
    branches: [main, master]

concurrency:
  group: tests-${{ github.ref }}
  cancel-in-progress: true

jobs:
  phpunit:
    name: PHPUnit
    runs-on: ubuntu-latest

    steps:
      - name: Checkout
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v4
        with:
          php-version: '8.3'
          extensions: pdo_sqlite, sqlite3, dom, curl, gd, imagick, zip

      - name: Cache Composer
        uses: actions/cache@v4
        with:
          path: ~/.composer/cache
          key: composer-${{ hashFiles('composer.lock') }}

      - name: Install Composer dependencies
        run: composer install --no-interaction --prefer-dist

      - name: Run migrations (SQLite)
        run: php artisan migrate --database=sqlite --force

      - name: Run PHPUnit
        run: php artisan test --without-tty

  playwright:
    name: Playwright E2E
    runs-on: ubuntu-latest
    needs: phpunit

    steps:
      - name: Checkout
        uses: actions/checkout@v4

      - name: Setup PHP
        uses: shivammathur/setup-php@v4
        with:
          php-version: '8.3'

      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'

      - name: Cache Composer
        uses: actions/cache@v4
        with:
          path: ~/.composer/cache
          key: composer-${{ hashFiles('composer.lock') }}

      - name: Install Composer dependencies
        run: composer install --no-interaction --prefer-dist

      - name: Install Node dependencies
        run: npm ci

      - name: Build assets
        run: npm run build

      - name: Install Playwright browsers
        run: npx playwright install --with-deps chromium

      - name: Start server
        run: php artisan serve --port=8093 &
        env:
          APP_ENV: testing

      - name: Wait for server
        run: npx wait-on http://localhost:8093 --timeout 30000

      - name: Run Playwright
        run: npx playwright test --project=chromium
```

---

### 6. Update `render.yaml` to use proper Dockerfile

**File:** `render.yaml`

The `startCommand` should match what the Dockerfile CMD does. Already mostly correct, but verify:

```yaml
startCommand: sh -c "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT"
```

**Note:** Render uses its own build command (`npm install && npm run build`) which is defined separately. The Dockerfile's `ENTRYPOINT` and `CMD` handle the serve step.

---

## Breaking Change Risk

| Change | Risk Level | Reason |
|--------|------------|--------|
| Rewrite `Dockerfile` | **Low** | Render doesn't use it currently. Only affects explicit `docker compose build`. |
| Edit `docker-compose.yml` | **Medium** | Only affects local Docker dev. Docker daemon currently not running on dev machine. |
| Add `/healthcheck` route | **Very Low** | Net-new route. No existing route modified. |
| Add `tests.yml` | **None** | Net-new file. No impact on app code. |
| Update `.env.docker` | **Medium** | Only affects `docker compose up`. Changes DB driver from MySQL to SQLite. |

**No existing production code is modified by any of these changes.**

---

## DB Driver Inconsistency — Critical Note

| Environment | DB Driver | File |
|-------------|-----------|------|
| Production | **SQLite** | `.env` |
| Docker local | MySQL (proposed: SQLite) | `.env.docker` |
| CI tests | **SQLite** | GitHub Actions |

**Decision needed:** If we change `.env.docker` to SQLite (matching production), the MySQL service in `docker-compose.yml` becomes unused. Options:
1. Remove `db` service entirely (simplest)
2. Keep it for future use (e.g., if you migrate to MySQL later)

---

## Scope Constraints

- **No changes to application PHP code**
- **No changes to database schema**
- **No changes to authentication, billing, or content generation logic**
- **Only infrastructure files: Dockerfile, docker-compose.yml, .env.docker, routes/web.php, and new CI workflow**

---

## Open Questions

1. Should the `db` (MySQL) service be removed from docker-compose or kept for future use?
2. Should Docker Desktop be enabled on the dev machine for local Docker testing?
3. Is Render the intended production host, or should this spec also cover Coolify/VPS?
4. Should Lighthouse CI be integrated into the same `tests.yml` or kept separate?

---

## Files Touched

| File | Action |
|------|--------|
| `Dockerfile` | Rewrite |
| `docker-compose.yml` | Edit (add `entrypoint`) |
| `.env.docker` | Edit (SQLite over MySQL) |
| `routes/web.php` | Edit (add healthcheck) |
| `.github/workflows/tests.yml` | Create new |
| `docker/mysql/` | Consider removing (MySQL no longer used) |
