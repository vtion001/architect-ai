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
  // ── Auto-start dev servers ──────────────────────────────────────────────────────────
  // Starts PHP (Laravel) on 8081 and Vite on 5175. Both are reused if already running.
  webServer: [
    {
      command: 'php artisan serve --port=8081',
      port: 8081,
      reuseExistingServer: true,
      timeout: 30000,
      stdout: 'ignore',
      stderr: 'ignore',
    },
    {
      command: 'node node_modules/vite/bin/vite.js --port 5175',
      port: 5175,
      reuseExistingServer: true,
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
