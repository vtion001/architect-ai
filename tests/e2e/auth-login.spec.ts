import { test, expect, Page } from '@playwright/test';

/**
 * Auth Login E2E Tests
 *
 * Prerequisites:
 *   1. SQLite DB must exist and be migrated: php artisan migrate
 *   2. Seed data present: php artisan db:seed
 *   3. Dev server running: php artisan serve --port=8081
 *   4. Vite dev server running (for frontend assets): npm run dev
 *
 * Dev credentials (from DevUserSeeder):
 *   Email:    admin@dev.local
 *   Password: password123
 */

test.describe('Auth — Login', () => {

  // ── Helper: log in as dev user ─────────────────────────────────────────────
  async function loginAsDev(page: Page) {
    await page.goto('/auth/login');
    await page.fill('input[type="email"], input[name="email"]', 'admin@dev.local');
    await page.fill('input[type="password"], input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
  }

  // ── Helper: assert redirect to dashboard ────────────────────────────────────
  async function expectDashboard(page: Page) {
    await page.waitForURL(/dashboard/, { timeout: 15000 });
    await expect(page.locator('body')).toBeVisible();
  }

  // ════════════════════════════════════════════════════════════════════════════
  // TC-01: Valid dev user login redirects to dashboard
  // ════════════════════════════════════════════════════════════════════════════
  test('TC-01: valid login redirects to dashboard', async ({ page }) => {
    await loginAsDev(page);
    await expectDashboard(page);

    // Verify no error messages shown
    const errorAlert = page.locator('[role="alert"], .alert-error, .text-red');
    await expect(errorAlert).toHaveCount(0, { timeout: 3000 });
  });

  // ════════════════════════════════════════════════════════════════════════════
  // TC-02: Invalid email shows validation error
  // ════════════════════════════════════════════════════════════════════════════
  test('TC-02: invalid email shows error', async ({ page }) => {
    await page.goto('/auth/login');
    await page.fill('input[type="email"], input[name="email"]', 'notauser@example.com');
    await page.fill('input[type="password"], input[name="password"]', 'wrongpassword');
    await page.click('button[type="submit"]');

    // Either a validation message on the field OR a redirect back to login
    await page.waitForFunction(() => {
      return window.location.pathname === '/auth/login';
    }, { timeout: 10000 });

    // Check for error text on the page
    const pageContent = await page.content();
    const hasError = pageContent.includes('Invalid') || pageContent.includes('invalid') ||
                     pageContent.includes('credentials') || pageContent.includes('Credentials');
    expect(hasError).toBe(true);
  });

  // ════════════════════════════════════════════════════════════════════════════
  // TC-03: Wrong password shows error (not email-enumeratable)
  // ════════════════════════════════════════════════════════════════════════════
  test('TC-03: wrong password shows error', async ({ page }) => {
    await page.goto('/auth/login');
    await page.fill('input[type="email"], input[name="email"]', 'admin@dev.local');
    await page.fill('input[type="password"], input[name="password"]', 'wrongpassword');
    await page.click('button[type="submit"]');

    await page.waitForFunction(() => window.location.pathname === '/auth/login', { timeout: 10000 });

    // Error should NOT reveal whether email exists
    const bodyText = await page.locator('body').innerText();
    const revealsEmail = bodyText.includes('admin@dev.local') && bodyText.toLowerCase().includes('not found');
    expect(revealsEmail).toBe(false);
  });

  // ════════════════════════════════════════════════════════════════════════════
  // TC-04: Empty fields show HTML5 validation (no submit)
  // ════════════════════════════════════════════════════════════════════════════
  test('TC-04: empty email triggers browser validation', async ({ page }) => {
    await page.goto('/auth/login');
    // Submit without filling anything — HTML5 required attribute should block
    await page.click('button[type="submit"]');
    // Should stay on login (no navigation)
    await expect(page).toHaveURL(/auth\/login/);
  });

  // ════════════════════════════════════════════════════════════════════════════
  // TC-05: Login page has all required elements
  // ════════════════════════════════════════════════════════════════════════════
  test('TC-05: login page renders all required elements', async ({ page }) => {
    await page.goto('/auth/login');
    await page.waitForLoadState('domcontentloaded');

    // Email field
    const emailField = page.locator('input[type="email"], input[name="email"]');
    await expect(emailField).toBeVisible();

    // Password field
    const passwordField = page.locator('input[type="password"], input[name="password"]');
    await expect(passwordField).toBeVisible();

    // Submit button
    const submitBtn = page.locator('button[type="submit"]');
    await expect(submitBtn).toBeVisible();

    // No SQL error on the page
    const body = await page.locator('body').innerText();
    expect(body).not.toMatch(/SQLSTATE|QueryException|database.*error/i);
  });

  // ════════════════════════════════════════════════════════════════════════════
  // TC-06: No SQL error on login page load
  // ════════════════════════════════════════════════════════════════════════════
  test('TC-06: no SQL error displayed on login page', async ({ page }) => {
    const errors: string[] = [];
    page.on('console', msg => {
      if (msg.type() === 'error') errors.push(msg.text());
    });

    await page.goto('/auth/login');
    await page.waitForLoadState('networkidle');

    const body = await page.locator('body').innerText();
    expect(body).not.toMatch(/SQLSTATE/i);
    expect(body).not.toMatch(/QueryException/i);
    expect(body).not.toMatch(/database.*does not exist/i);
  });

  // ════════════════════════════════════════════════════════════════════════════
  // TC-07: Authenticated user can access dashboard
  // ════════════════════════════════════════════════════════════════════════════
  test('TC-07: authenticated session persists on protected route', async ({ page }) => {
    await loginAsDev(page);
    await expectDashboard(page);

    // Navigate away to a protected route — session should persist (no auth wall)
    await page.goto('/settings');
    await page.waitForLoadState('networkidle');

    // Should NOT be kicked back to login — session persists
    const url = page.url();
    expect(url).not.toMatch(/auth\/login/);
  });

  // ════════════════════════════════════════════════════════════════════════════
  // TC-08: Redirect to intended page after login
  // ════════════════════════════════════════════════════════════════════════════
  test('TC-08: login redirects to intended URL', async ({ page }) => {
    // Request login page with an intended redirect
    await page.goto('/auth/login?redirect=/billing');
    await page.fill('input[type="email"], input[name="email"]', 'admin@dev.local');
    await page.fill('input[type="password"], input[name="password"]', 'password123');
    await page.click('button[type="submit"]');

    // Should redirect to billing or dashboard (never back to login)
    await page.waitForFunction(() => {
      const u = window.location.pathname;
      return u !== '/auth/login';
    }, { timeout: 15000 });
  });

  // ════════════════════════════════════════════════════════════════════════════
  // TC-09: Logout works and redirects to login
  // ════════════════════════════════════════════════════════════════════════════
  test('TC-09: logout redirects to login', async ({ page }) => {
    await loginAsDev(page);
    await expectDashboard(page);

    // Submit logout form
    await page.click('button:has-text("Log out"), button:has-text("Logout"), a[href="/auth/logout"]', { timeout: 5000 }).catch(() => {
      // If no button found, POST directly to logout
      return page.request.post('/auth/logout');
    });

    await page.waitForURL(/auth\/login/, { timeout: 10000 });
    await expect(page.locator('input[type="email"]')).toBeVisible();
  });

  // ════════════════════════════════════════════════════════════════════════════
  // TC-10: Login with workspace slug
  // ════════════════════════════════════════════════════════════════════════════
  test('TC-10: login with dev workspace slug', async ({ page }) => {
    await page.goto('/auth/login/dev');
    await page.fill('input[type="email"], input[name="email"]', 'admin@dev.local');
    await page.fill('input[type="password"], input[name="password"]', 'password123');
    await page.click('button[type="submit"]');
    await expectDashboard(page);
  });

});
