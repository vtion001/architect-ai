import { test, expect } from '@playwright/test';

test.describe('Blog Batch Generation', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/auth/login');
    await page.waitForSelector('input[type="email"]', { timeout: 15000 });
    await page.fill('input[type="email"]', 'admin@dev.local');
    await page.fill('input[type="password"]', 'password123');
    await page.click('button[type="submit"]');
    await page.waitForURL('**/dashboard', { timeout: 15000 });
    await page.goto('/content-creator');
    await page.waitForLoadState('networkidle');
    await page.click('button:has-text("Blog")');
    await page.waitForTimeout(500);
  });

  test('single mode shows correct token estimate', async ({ page }) => {
    const tokenEstimate = page.locator('text=Estimated Token Consumption');
    await expect(tokenEstimate).toContainText('20 tokens');
  });

  test('batch mode enables and shows correct quantity options', async ({ page }) => {
    await page.getByRole('button', { name: 'Batch Generate' }).click();
    await page.waitForTimeout(500);

    await expect(page.locator('text=How Many Blogs?')).toBeVisible();
    await expect(page.getByRole('button', { name: '1', exact: true })).toBeVisible();
    await expect(page.getByRole('button', { name: '2', exact: true })).toBeVisible();
    await expect(page.getByRole('button', { name: '3', exact: true })).toBeVisible();
  });

  test('batch mode token estimate updates with count', async ({ page }) => {
    await page.getByRole('button', { name: 'Batch Generate' }).click();
    await page.waitForTimeout(500);

    const tokenEstimate = page.locator('text=Estimated Token Consumption');
    await expect(tokenEstimate).toContainText('40 tokens');

    await page.getByRole('button', { name: '2', exact: true }).click();
    await expect(tokenEstimate).toContainText('60 tokens');

    await page.getByRole('button', { name: '3', exact: true }).click();
    await expect(tokenEstimate).toContainText('80 tokens');
  });

  test('batch submission shows generating button state', async ({ page }) => {
    await page.route('**/content-creator/**', async (route) => {
      const url = route.request().url();
      if (url.includes('/blog/batch')) {
        await route.fulfill({
          status: 200,
          contentType: 'application/json',
          body: JSON.stringify({
            success: true,
            content: { id: 'test-batch-id', status: 'generating' },
            message: 'Blog batch initiated.',
          }),
        });
      } else {
        await route.continue();
      }
    });

    await page.getByRole('button', { name: 'Batch Generate' }).click();
    await page.waitForTimeout(300);

    await page.locator('input[placeholder*="viral"]').fill('How to Build a SaaS Product');
    await page.locator('input[placeholder*="e.g., How to Create"]').fill('SaaS Product Ideas');

    const generateBtn = page.getByRole('button', { name: /Generate 1 Blog Post/i });
    await expect(generateBtn).toBeVisible();

    await generateBtn.click();

    const generatingBtn = page.getByRole('button', { name: /Generating Blog Batch/i });
    await expect(generatingBtn).toBeVisible({ timeout: 5000 });
  });
});
