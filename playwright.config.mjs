import { defineConfig } from '@playwright/test';

export default defineConfig({
    testDir: './tests/browser',
    testMatch: '*.spec.mjs',
    fullyParallel: false,
    workers: 1,
    use: { baseURL: process.env.PLAYGROUND_URL, browserName: 'chromium' },
});
