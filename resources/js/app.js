/**
 * Main Application Entry Point
 * 
 * Performance optimizations:
 * - Alpine.js bundled (no CDN)
 * - Lucide icons bundled (no CDN)
 * - Chart.js available globally
 * - rrweb loaded only for Ghost Studio
 * - Utility functions for debounce/throttle
 */

import './bootstrap';
import Alpine from 'alpinejs';
import { createIcons, icons } from 'lucide';
import Chart from 'chart.js/auto';
import { createTeamChatWidgetComponent } from './components/team-chat-widget';
window.createTeamChatWidgetComponent = createTeamChatWidgetComponent;

// =========================================================================
// Alpine.js
// =========================================================================
window.Alpine = Alpine;

// Start Alpine after DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});

// =========================================================================
// Lucide Icons
// =========================================================================
document.addEventListener('DOMContentLoaded', () => {
    createIcons({ icons });
});

// Expose for dynamic content updates
window.refreshIcons = () => createIcons({ icons });
window.createIcons = createIcons;
window.icons = icons;
window.lucide = { createIcons: () => createIcons({ icons }) };


// =========================================================================
// Chart.js - Available globally
// =========================================================================
window.Chart = Chart;

/**
 * Create a chart with consistent defaults.
 */
window.createChart = (ctx, config) => {
    const canvas = typeof ctx === 'string' ? document.querySelector(ctx) : ctx;

    if (!canvas) {
        console.warn('Chart canvas not found:', ctx);
        return null;
    }

    return new Chart(canvas, {
        ...config,
        options: {
            ...config.options,
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                ...config.options?.plugins
            }
        }
    });
};

// =========================================================================
// rrweb - Lazy loaded for Ghost Studio
// =========================================================================
window.loadRrweb = async () => {
    if (window.rrweb) return window.rrweb;

    // Load CSS
    const css = document.createElement('link');
    css.rel = 'stylesheet';
    css.href = 'https://cdn.jsdelivr.net/npm/rrweb@2.0.0-alpha.4/dist/rrweb.min.css';
    document.head.appendChild(css);

    // Load JS
    const script = document.createElement('script');
    script.src = 'https://cdn.jsdelivr.net/npm/rrweb@2.0.0-alpha.4/dist/rrweb.min.js';

    return new Promise((resolve, reject) => {
        script.onload = () => resolve(window.rrweb);
        script.onerror = reject;
        document.head.appendChild(script);
    });
};

// =========================================================================
// Utility Functions
// =========================================================================

/**
 * Debounce function
 */
window.debounce = (fn, delay = 300) => {
    let timeoutId;
    return (...args) => {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => fn.apply(this, args), delay);
    };
};

/**
 * Throttle function
 */
window.throttle = (fn, limit = 100) => {
    let inThrottle;
    return (...args) => {
        if (!inThrottle) {
            fn.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
};

// Log initialization
console.log('🚀 ArchitGrid initialized');
