import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ command }) => ({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: false,
        // For ngrok/tunnel access: disable HMR or use the tunnel URL
        hmr: process.env.VITE_HMR_HOST
            ? { host: process.env.VITE_HMR_HOST }
            : command === 'serve' ? { host: 'localhost' } : false,
        cors: true,
        // Allow requests from any origin (for ngrok)
        origin: '*',
    },
    // Ensure assets use relative paths for production
    base: command === 'build' ? '/build/' : '/',
}));