import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/touch.css',
                'resources/js/app.js',
                'resources/js/touch-handlers.js',
                'resources/js/dashboard-enhancements.js'
            ],
            refresh: true,
        }),
    ],
});
