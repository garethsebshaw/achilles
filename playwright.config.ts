import { defineConfig } from '@playwright/test';

const baseURL = process.env.BASE_URL ?? 'http://achillesworkouts.test';

export default defineConfig({
    testDir: './tests/browser',
    outputDir: 'playwright-artifacts',
    fullyParallel: false,
    forbidOnly: !!process.env.CI,
    retries: process.env.CI ? 1 : 0,
    timeout: 120000,
    expect: {
        timeout: 15000,
    },
    reporter: [['list'], ['html', { outputFolder: 'playwright-report', open: 'never' }]],
    use: {
        baseURL,
        headless: true,
        screenshot: 'only-on-failure',
        trace: 'retain-on-failure',
        video: 'retain-on-failure',
        ignoreHTTPSErrors: true,
    },
});
