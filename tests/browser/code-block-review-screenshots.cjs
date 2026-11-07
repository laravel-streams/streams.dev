const {chromium} = require('playwright');
const {spawn} = require('node:child_process');
const fs = require('node:fs');
const net = require('node:net');
const path = require('node:path');

const phase = process.env.SCREENSHOT_PHASE === 'before' ? 'before' : 'after';
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
    console.log(`Owned preview PID: ${server.pid}; port: ${port}; phase: ${phase}`);

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
        const docsPanel = phase === 'before'
            ? page.locator('.documentation-content pre').first()
            : page.locator('.st-code').first();
        await docsPanel.waitFor();
        await docsPanel.screenshot({path: path.join(outDir, `${phase}-docs-form-layouts-light.png`)});

        await page.evaluate(() => window.StreamsTheme?.set('dark'));
        await page.waitForTimeout(150);
        await docsPanel.screenshot({path: path.join(outDir, `${phase}-docs-form-layouts-dark.png`)});

        await page.goto(`http://127.0.0.1:${port}/playground/text-input`);
        await page.waitForFunction(() => Boolean(window.Livewire));
        const playgroundPanel = page.locator('.st-code').first();
        await playgroundPanel.waitFor();
        const editor = page.locator('[data-playground-config]');

        if (phase === 'after') {
            const node = JSON.parse(await editor.inputValue());
            node.defaults.placeholder = 'Screenshot highlight check';
            await editor.fill(JSON.stringify(node, null, 2));
            await page.waitForFunction(() => document.querySelector('[data-playground-config-highlight] .token'));
        }

        await page.evaluate(() => window.StreamsTheme?.set('light'));
        await page.waitForTimeout(150);
        await playgroundPanel.screenshot({path: path.join(outDir, `${phase}-playground-light.png`)});

        await page.evaluate(() => window.StreamsTheme?.set('dark'));
        await page.waitForTimeout(150);
        await playgroundPanel.screenshot({path: path.join(outDir, `${phase}-playground-dark.png`)});

        console.log(`Screenshots written to ${outDir} (${phase})`);
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
