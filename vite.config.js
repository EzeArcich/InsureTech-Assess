import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/sass/surfaces.scss',
                'resources/js/app.js',
                'resources/js/admin-claims.js',
                'resources/js/inspection.js',
                'resources/js/route-sheets.js',
                'resources/js/settings-rates.js',
                'resources/js/coordination-location.js',
                'resources/js/claim-wizard.js',
                'resources/js/email-templates.js',
                'resources/js/producer-email.js',
            ],
            refresh: true,
        }),
    ],
});
