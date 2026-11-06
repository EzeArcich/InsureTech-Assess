import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/js/admin-claims.js',
                'resources/js/inspection.js',
                'resources/js/route-sheets.js',
                'resources/js/settings-rates.js',
                'resources/js/coordination-location.js',
            ],
            refresh: true,
        }),
    ],
});
