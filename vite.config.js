import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css',
                    'resources/css/customer.css',
                    'resources/js/app.js',
                    'resources/js/customer-app.js',
                ],
            refresh: true,
        }),
    ],
});
