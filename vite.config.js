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

        // Dev server — proxy to Laravel backend
        // In Docker: use 'host.docker.internal' for host machine, or 'app:9000' for container network
        // On host: use 'localhost:8081'
        server: {
            host: '0.0.0.0',   // Must bind to all interfaces — Nginx is another process
            port: 5175,         // Host-facing port (container maps 5175:5175 in docker-compose.dev.yml)
            strictPort: false,  // Don't error if port is taken — fallback to random port
            allowedHosts: true,
            cors: true,
            hmr: {
                // HMR WebSocket — must match the server port for Vite to inject correct WS URL
                port: 5175,
                host: 'localhost',
            },
            proxy: {
                // All Laravel routes proxy through Nginx (port 80) to PHP-FPM
                // Inside Docker: VITE_PROXY_TARGET=http://localhost:80 (Nginx in same container)
                // On host (local dev): falls back to http://localhost:8081 (php artisan serve)
                '/api': {
                    target: process.env.VITE_PROXY_TARGET || 'http://localhost:8081',
                    changeOrigin: true,
                    secure: false,
                },
                '/auth': {
                    target: process.env.VITE_PROXY_TARGET || 'http://localhost:8081',
                    changeOrigin: true,
                    secure: false,
                },
                '/dashboard': {
                    target: process.env.VITE_PROXY_TARGET || 'http://localhost:8081',
                    changeOrigin: true,
                    secure: false,
                },
                '/admin': {
                    target: process.env.VITE_PROXY_TARGET || 'http://localhost:8081',
                    changeOrigin: true,
                    secure: false,
                },
                '/content-creator': {
                    target: process.env.VITE_PROXY_TARGET || 'http://localhost:8081',
                    changeOrigin: true,
                    secure: false,
                },
                '/settings': {
                    target: process.env.VITE_PROXY_TARGET || 'http://localhost:8081',
                    changeOrigin: true,
                    secure: false,
                },
                '/billing': {
                    target: process.env.VITE_PROXY_TARGET || 'http://localhost:8081',
                    changeOrigin: true,
                    secure: false,
                },
            },
        },

        // Base path for production
        base: command === 'build' ? '/build/' : '/',
    };
});