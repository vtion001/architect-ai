import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig(({ command, mode }) => {
    const isProduction = mode === 'production';

    return {
        plugins: [
            laravel({
                input: [
                    'resources/css/app.css', 
                    'resources/js/app.js',
                    'resources/js/components/content-creator.js'
                ],
                refresh: true,
            }),
        ],

        // Build configuration
        build: {
            // Minify in production
            minify: isProduction ? 'terser' : false,
            terserOptions: isProduction ? {
                compress: {
                    drop_console: false, // Keep console logs for now
                    drop_debugger: true,
                },
            } : undefined,

            // Target modern browsers
            target: 'es2020',

            // Source maps only in dev
            sourcemap: !isProduction,

            // Chunk size warning
            chunkSizeWarningLimit: 500,
        },

        // Dev server
        server: {
            host: '0.0.0.0',
            port: 5173,
            strictPort: false,
            allowedHosts: true,
            hmr: process.env.VITE_HMR_HOST
                ? { host: process.env.VITE_HMR_HOST }
                : command === 'serve' ? { host: 'localhost' } : false,
            cors: true,
        },

        // Base path for production
        base: command === 'build' ? '/build/' : '/',
    };
});