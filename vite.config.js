import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        tailwindcss(),
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
        }),
    ],
    // `composer dev`: app on 127.0.0.1:8427, Vite dev server + HMR pinned to 5427.
    server: {
        host: '127.0.0.1',
        port: 5427,
        strictPort: true,
        hmr: {
            host: '127.0.0.1',
            port: 5427,
        },
    },
    build: {
        // Laravel's @vite directive (10 through 12) reads public/build/manifest.json, not .vite/manifest.json.
        manifest: 'manifest.json',
    },
});
