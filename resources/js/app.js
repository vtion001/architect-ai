import './bootstrap';
import { createIcons, icons } from 'lucide';
import Chart from 'chart.js/auto';

// Initialize icons
createIcons({ icons });

// Expose functionality globally
window.Chart = Chart;
window.createIcons = createIcons;
window.icons = icons;

// Re-initialize icons on dynamic updates
window.refreshIcons = () => createIcons({ icons });

// Helper for standardized chart config
window.createChart = (ctx, config) => {
    return new Chart(ctx, {
        ...config,
        options: {
            ...config.options,
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                ...config.options?.plugins
            }
        }
    });
};
