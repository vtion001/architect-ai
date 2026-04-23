import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: 0,
  workers: 1,
  reporter: 'list',
  use: {
    baseURL: 'http://localhost:8081',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
  },
  // ── Auto-start Vite dev server (required for frontend JS/Alpine.js) ────────────
  // Prerequisites:
  //   1. PHP server: php artisan serve --port=8081
  //   2. SQLite DB:  php artisan migrate && php artisan db:seed
  // If Vite is already running, this block is a no-op.
  webServer: [
    {
      command: 'node node_modules/vite/bin/vite.js --port 5175',
      port: 5175,
      reuseExistingServer: true,  // skip if already running
      timeout: 30000,
      stdout: 'ignore',
      stderr: 'ignore',
    },
  ],
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
