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
                    passes: 2,
                    unsafe_arrows: true,
                    unsafe_methods: true,
                },
                mangle: {
                    safari10: true,
                },
            } : undefined,

            // Target modern browsers
            target: 'es2020',

            // Source maps only in dev
            sourcemap: !isProduction,

            // Chunk size warning (lowered to catch issues)
            chunkSizeWarningLimit: 300,

            // CSS code splitting
            cssCodeSplit: true,

            // Chunk splitting configuration
            rollupOptions: {
                output: {
                    manualChunks: (id) => {
                        // Split vendor chunks for better caching
                        if (id.includes('node_modules')) {
                            // Alpine.js - core framework, load early
                            if (id.includes('alpinejs')) {
                                return 'vendor-alpine';
                            }
                            // Chart.js - heavy, load async
                            if (id.includes('chart.js') || id.includes('chartjs')) {
                                return 'vendor-chart';
                            }
                            // Lucide icons - can be lazy loaded
                            if (id.includes('lucide')) {
                                return 'vendor-icons';
                            }
                            // Stoplight Elements - very heavy, only load on API docs pages
                            if (id.includes('@stoplight/elements') || id.includes('@stoplight/mosaic')) {
                                return 'vendor-stoplight';
                            }
                            // Axios - API client
                            if (id.includes('axios')) {
                                return 'vendor-axios';
                            }
                            // Default vendor chunk
                            return 'vendor';
                        }
                    },
                },
            },
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
                // Proxy to Docker app container (hardcoded in built assets)
                '/content-creator': {
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