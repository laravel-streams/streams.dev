import { spawn } from 'node:child_process';
import { createServer } from 'node:net';
import { once } from 'node:events';
import { fileURLToPath } from 'node:url';

const root = fileURLToPath(new URL('../../', import.meta.url));
const probe = createServer();
await new Promise((resolve) => probe.listen(0, '127.0.0.1', resolve));
const port = probe.address().port;
await new Promise((resolve) => probe.close(resolve));
const url = `http://127.0.0.1:${port}`;
const server = spawn('php', ['-S', `127.0.0.1:${port}`, '../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php'], {
    cwd: fileURLToPath(new URL('../../public/', import.meta.url)),
    env: {
        ...process.env,
        APP_ENV: 'testing',
        APP_KEY: 'base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=',
        CACHE_DRIVER: 'array',
        SESSION_DRIVER: 'array',
        APP_DEBUG: 'false',
    },
    stdio: ['ignore', 'ignore', 'pipe'],
});
console.log(`Owned playground server PID ${server.pid}, port ${port}`);
let serverError = '';
server.stderr.on('data', (data) => { serverError = (serverError + data).slice(-2000); });
const serverClosed = once(server, 'close');

try {
    let ready = false;
    for (let attempt = 0; attempt < 100; attempt++) {
        if (server.exitCode !== null) throw new Error(`Preview server failed: ${serverError}`);
        try {
            ready = (await fetch(`${url}/ui`, { signal: AbortSignal.timeout(1000) })).ok;
        } catch { /* Wait for this owned server to boot. */ }
        if (ready) break;
        await new Promise((resolve) => setTimeout(resolve, 100));
    }
    if (!ready) throw new Error('Owned preview server did not become ready.');

    const runner = spawn(process.execPath, ['node_modules/@playwright/test/cli.js', 'test'], {
        cwd: root,
        env: { ...process.env, PLAYGROUND_URL: url },
        stdio: 'inherit',
    });
    const [code] = await once(runner, 'close');
    process.exitCode = code ?? 1;
} finally {
    // This is the exact PID spawned above; never touch another port owner.
    if (server.exitCode === null) server.kill('SIGTERM');
    await serverClosed;
    console.log(`Owned playground server PID ${server.pid} stopped`);
}
