/** @type {import('tailwindcss').Config} */
import defaultTheme from 'tailwindcss/defaultTheme';

export default {
    // =========================================================================
    // Content - Purge unused CSS in production
    // =========================================================================
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        // Exclude test files and stubs
        "!./resources/**/*.test.js",
        "!./resources/**/*.spec.js",
    ],

    // =========================================================================
    // Dark Mode - Class-based for manual toggle
    // =========================================================================
    darkMode: 'class',

    // =========================================================================
    // Theme Extensions
    // =========================================================================
    theme: {
        extend: {
            // =====================================================================
            // Colors - CSS Variable-based for dynamic theming
            // =====================================================================
            colors: {
                border: "var(--border)",
                input: "var(--input)",
                ring: "var(--ring)",
                background: "var(--background)",
                foreground: "var(--foreground)",
                primary: {
                    DEFAULT: "var(--primary)",
                    foreground: "var(--primary-foreground)",
                },
                secondary: {
                    DEFAULT: "var(--secondary)",
                    foreground: "var(--secondary-foreground)",
                },
                destructive: {
                    DEFAULT: "var(--destructive)",
                    foreground: "var(--destructive-foreground)",
                },
                muted: {
                    DEFAULT: "var(--muted)",
                    foreground: "var(--muted-foreground)",
                },
                accent: {
                    DEFAULT: "var(--accent)",
                    foreground: "var(--accent-foreground)",
                },
                popover: {
                    DEFAULT: "var(--popover)",
                    foreground: "var(--popover-foreground)",
                },
                card: {
                    DEFAULT: "var(--card)",
                    foreground: "var(--card-foreground)",
                },
                sidebar: {
                    DEFAULT: "var(--sidebar)",
                    foreground: "var(--sidebar-foreground)",
                    primary: "var(--sidebar-primary)",
                    'primary-foreground': "var(--sidebar-primary-foreground)",
                    accent: "var(--sidebar-accent)",
                    'accent-foreground': "var(--sidebar-accent-foreground)",
                    border: "var(--sidebar-border)",
                    ring: "var(--sidebar-ring)",
                },
            },

            // =====================================================================
            // Border Radius
            // =====================================================================
            borderRadius: {
                lg: "var(--radius)",
                md: "calc(var(--radius) - 2px)",
                sm: "calc(var(--radius) - 4px)",
            },

            // =====================================================================
            // Font Family (use system fonts as fallback for faster load)
            // =====================================================================
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },

            // =====================================================================
            // Animations (for progressive enhancement)
            // =====================================================================
            animation: {
                'fade-in': 'fadeIn 0.2s ease-in-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'pulse-soft': 'pulseSoft 2s ease-in-out infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                pulseSoft: {
                    '0%, 100%': { opacity: '1' },
                    '50%': { opacity: '0.8' },
                },
            },
        },
    },

    // =========================================================================
    // Plugins
    // =========================================================================
    plugins: [],

    // =========================================================================
    // Safelist - Classes that must always be included
    // =========================================================================
    safelist: [
        // Dynamic color classes used in Blade templates
        { pattern: /bg-(red|green|blue|yellow|indigo|purple|pink|gray)-(50|100|200|300|400|500|600|700|800|900)/ },
        { pattern: /text-(red|green|blue|yellow|indigo|purple|pink|gray)-(50|100|200|300|400|500|600|700|800|900)/ },
        { pattern: /border-(red|green|blue|yellow|indigo|purple|pink|gray)-(50|100|200|300|400|500|600|700|800|900)/ },
    ],

    // =========================================================================
    // Feature Flags - Disable unused features
    // =========================================================================
    corePlugins: {
        // Disable if not using
        preflight: true,
        container: true,
        // Keep all other plugins enabled by default
    },

    // =========================================================================
    // Future - Enable upcoming features for smaller output
    // =========================================================================
    future: {
        hoverOnlyWhenSupported: true,
        respectDefaultRingColorOpacity: true,
        disableColorOpacityUtilitiesByDefault: true,
    },
}