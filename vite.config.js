import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import inject from '@rollup/plugin-inject';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    optimizeDeps: {
        include: ['jquery'],
    },
    build: {
        rollupOptions: {
            plugins: [
                inject({
                    $: 'jquery',
                    jQuery: 'jquery'
                }),
            ],
        },
    },
});
