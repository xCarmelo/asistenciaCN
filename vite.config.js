import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/sass/app.scss', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        cors: true,
        host: 'asistencia.ni',
        port: 5173,
        strictPort: true,
        origin: 'http://asistencia.ni:5173',
    },
});
