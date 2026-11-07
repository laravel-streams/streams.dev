const {chromium} = require('playwright');
const {spawn} = require('node:child_process');
const fs = require('node:fs');
const net = require('node:net');
const path = require('node:path');

const outDir = path.join(process.cwd(), '.ops', 'task-676-screenshots');

(async () => {
    fs.mkdirSync(outDir, {recursive: true});

    const socket = net.createServer();
    await new Promise((r) => socket.listen(0, '127.0.0.1', r));
    const port = socket.address().port;
    await new Promise((r) => socket.close(r));

    const server = spawn('php', ['-S', `127.0.0.1:${port}`, '-t', '.', '../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php'], {
        cwd: path.join(process.cwd(), 'public'),
        stdio: ['ignore', 'ignore', 'pipe'],
    });
    server.stderr.resume();
    console.log(`Owned preview PID: ${server.pid}; port: ${port}`);

    let browser;
    try {
        for (let i = 0; i < 50; i++) {
            try {
                if ((await fetch(`http://127.0.0.1:${port}/docs/ui/form-layouts`)).ok) {
                    break;
                }
            } catch {
                /* wait */
            }
            await new Promise((r) => setTimeout(r, 100));
        }

        browser = await chromium.launch({headless: true});
        const page = await browser.newPage();
        await page.setViewportSize({width: 1280, height: 900});

        await page.goto(`http://127.0.0.1:${port}/docs/ui/form-layouts`);
        await page.locator('.st-code').first().waitFor();
        await page.screenshot({path: path.join(outDir, 'docs-form-layouts-light.png'), fullPage: false});

        await page.evaluate(() => window.StreamsTheme?.set('dark'));
        await page.waitForTimeout(150);
        await page.screenshot({path: path.join(outDir, 'docs-form-layouts-dark.png'), fullPage: false});

        await page.goto(`http://127.0.0.1:${port}/playground/text-input`);
        await page.waitForFunction(() => Boolean(window.Livewire));
        await page.locator('.st-code').first().waitFor();
        const editor = page.locator('[data-playground-config]');
        const node = JSON.parse(await editor.inputValue());
        node.defaults.placeholder = 'Screenshot highlight check';
        await editor.fill(JSON.stringify(node, null, 2));
        await page.waitForFunction(() => document.querySelector('[data-playground-config-highlight] .token'));
        await page.screenshot({path: path.join(outDir, 'playground-json-highlight-dark.png'), fullPage: false});

        await page.evaluate(() => window.StreamsTheme?.set('light'));
        await page.waitForTimeout(150);
        await page.screenshot({path: path.join(outDir, 'playground-json-highlight-light.png'), fullPage: false});

        console.log(`Screenshots written to ${outDir}`);
    } finally {
        if (browser) {
            await browser.close();
        }
        if (server.exitCode === null && server.signalCode === null) {
            const stopped = new Promise((r) => server.once('exit', r));
            server.kill('SIGTERM');
            await stopped;
        }
        console.log(`Stopped owned preview PID: ${server.pid}`);
    }
})().catch((e) => {
    console.error(e);
    process.exitCode = 1;
});
