import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/componentes-produtos/component-card.js', // Adicionado
            ],
            refresh: true,
        }),
    ],
});
