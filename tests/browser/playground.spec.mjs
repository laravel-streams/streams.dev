import { test, expect } from '@playwright/test';
import { createHash } from 'node:crypto';
import { readFileSync, readdirSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import { join } from 'node:path';

function snapshot(directory) {
    const files = {};
    for (const entry of readdirSync(directory, { withFileTypes: true })) {
        const path = join(directory, entry.name);
        if (entry.isDirectory()) Object.assign(files, snapshot(path));
        else files[path] = createHash('sha256').update(readFileSync(path)).digest('hex');
    }
    return files;
}

const seeds = fileURLToPath(new URL('../../resources/playground/', import.meta.url));
const streams = fileURLToPath(new URL('../../streams/', import.meta.url));

test('all action buttons simulate without requests, memory changes or disk changes', async ({ page }) => {
    const before = { seeds: snapshot(seeds), streams: snapshot(streams) };
    await page.goto('/ui');
    await page.waitForLoadState('networkidle');
    const requests = [];
    page.on('request', (request) => requests.push(`${request.method()} ${request.url()}`));
    let count = 0;

    for (const handle of ['people', 'posts', 'orders']) {
        await page.locator('[data-playground-stream]').selectOption(handle);
        const ids = await page.locator('[data-playground-entry] option').evaluateAll((options) => options.map((option) => option.value));
        for (const id of ids) {
            await page.locator('[data-playground-entry]').selectOption(id);
            const inputs = page.locator('[data-playground-fields] input, [data-playground-fields] textarea');
            await inputs.first().fill('Private sample edit');
            const values = await inputs.evaluateAll((elements) => elements.map((element) => element.value));
            const buttons = page.locator('[data-playground-actions] button');
            for (let index = 0; index < await buttons.count(); index++) {
                await buttons.nth(index).click();
                await expect(page.locator('[data-playground-message]')).toContainText('Simulation only; nothing was changed or sent.');
                await expect(page.locator('[data-playground-result]')).toBeVisible();
                expect(await inputs.evaluateAll((elements) => elements.map((element) => element.value))).toEqual(values);
                expect(snapshot(seeds)).toEqual(before.seeds);
                expect(snapshot(streams)).toEqual(before.streams);
                count++;
            }
        }
    }
    expect(count).toBe(24);
    expect(requests).toEqual([]);
});

test('separate browser sessions isolate edits and a new session starts from seed', async ({ browser }) => {
    const first = await browser.newContext();
    const second = await browser.newContext();
    try {
        const a = await first.newPage();
        const b = await second.newPage();
        await a.goto('/ui');
        await b.goto('/ui');
        for (const [handle, field, value] of [['people', 'name', 'Alex Morgan'], ['posts', 'title', 'Welcome to Streams'], ['orders', 'customer', 'Alex Morgan']]) {
            await a.locator('[data-playground-stream]').selectOption(handle);
            await b.locator('[data-playground-stream]').selectOption(handle);
            await a.locator(`[name="${field}"]`).fill('First session edit');
            await expect(b.locator(`[name="${field}"]`)).toHaveValue(value);
            await b.locator(`[name="${field}"]`).fill('Second session edit');
            await expect(a.locator(`[name="${field}"]`)).toHaveValue('First session edit');
        }
        expect(await a.evaluate(() => Object.keys(sessionStorage))).toEqual([]);
        await first.close();
        const fresh = await browser.newContext();
        try {
            const c = await fresh.newPage();
            await c.goto('/ui');
            await expect(c.locator('[name="name"]')).toHaveValue('Alex Morgan');
            await c.locator('[data-playground-stream]').selectOption('posts');
            await expect(c.locator('[name="title"]')).toHaveValue('Welcome to Streams');
            await c.locator('[data-playground-stream]').selectOption('orders');
            await expect(c.locator('[name="customer"]')).toHaveValue('Alex Morgan');
        } finally {
            await fresh.close();
        }
    } finally {
        await first.close();
        await second.close();
    }
});

test('reset, reload and restored pages discard edits and stale action results', async ({ page }) => {
    await page.goto('/ui');
    await page.locator('[name="name"]').fill('Temporary edit');
    await page.getByRole('button', { name: 'Preview update', exact: true }).click();
    await expect(page.locator('[data-playground-result]')).toBeVisible();
    await page.getByRole('button', { name: 'Reset all samples' }).click();
    await expect(page.locator('[name="name"]')).toHaveValue('Alex Morgan');
    await expect(page.locator('[data-playground-result]')).toBeHidden();
    await page.locator('[name="name"]').fill('Another edit');
    await page.reload();
    await expect(page.locator('[name="name"]')).toHaveValue('Alex Morgan');
    await page.locator('[name="name"]').fill('Cached edit');
    // Exercise bfcache handlers deterministically, including a second restore.
    for (let cycle = 0; cycle < 2; cycle++) {
        await page.evaluate(() => {
            window.dispatchEvent(new PageTransitionEvent('pagehide', { persisted: true }));
            window.dispatchEvent(new PageTransitionEvent('pageshow', { persisted: true }));
        });
        await expect(page.locator('[name="name"]')).toHaveValue('Alex Morgan');
        await page.locator('[name="name"]').fill('Cached edit');
    }
    await page.goto('/ui-test');
    await page.goBack();
    await expect(page.locator('[name="name"]')).toHaveValue('Alex Morgan');
});
