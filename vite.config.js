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
    build: {
        // Laravel 10's @vite directive reads public/build/manifest.json (not .vite/manifest.json).
        manifest: 'manifest.json',
    },
});
