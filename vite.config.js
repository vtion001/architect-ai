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
                    'resources/js/components/content-creator.js',
                    'resources/css/elements.css',
                    'resources/js/elements.js'
                ],
                refresh: [
                    'app/Http/Controllers/**/*.php',
                    'resources/views/**/*.blade.php',
                    'routes/**/*.php',
                ],
            }),
        ],

        // Build configuration
        build: {
            // Minify in production
            minify: isProduction ? 'terser' : false,
            terserOptions: isProduction ? {
                compress: {
                    drop_console: true,
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

        // Dev server - proxy to Docker nginx for backend routes only
        server: {
            host: '0.0.0.0',
            port: 5175,
            strictPort: false,
            allowedHosts: true,
            hmr: process.env.VITE_HMR_HOST
                ? { host: process.env.VITE_HMR_HOST }
                : command === 'serve' ? { host: 'localhost' } : false,
            cors: true,
            proxy: {
                // Proxy API routes to Laravel backend
                '/api': {
                    target: 'http://localhost:8081',
                    changeOrigin: true,
                    secure: false,
                },
                // Proxy auth routes to Laravel backend
                '/auth': {
                    target: 'http://localhost:8081',
                    changeOrigin: true,
                    secure: false,
                },
                // Proxy other web routes that need PHP processing
                '/dashboard': {
                    target: 'http://localhost:8081',
                    changeOrigin: true,
                    secure: false,
                },
                '/admin': {
                    target: 'http://localhost:8081',
                    changeOrigin: true,
                    secure: false,
                },
            },
        },

        // Base path for production
        base: command === 'build' ? '/build/' : '/',
    };
});