<?php $__env->startSection('content'); ?>
<div class="p-8 max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Analytics</h1>
        <p class="text-muted-foreground">Track performance and insights across all modules</p>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Activities -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-green-600">
                        <i data-lucide="arrow-up" class="w-3 h-3"></i>
                        <span>+12.5%</span>
                    </div>
                </div>
                <p class="text-sm text-muted-foreground mb-1">Total Activities</p>
                <p class="text-2xl font-bold">8,547</p>
            </div>
        </div>

        <!-- Active Users -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-green-600">
                        <i data-lucide="arrow-up" class="w-3 h-3"></i>
                        <span>+8.3%</span>
                    </div>
                </div>
                <p class="text-sm text-muted-foreground mb-1">Active Users</p>
                <p class="text-2xl font-bold">342</p>
            </div>
        </div>

        <!-- Avg. Processing Time -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="clock" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-green-600">
                        <i data-lucide="arrow-up" class="w-3 h-3"></i>
                        <span>-15.2%</span>
                    </div>
                </div>
                <p class="text-sm text-muted-foreground mb-1">Avg. Processing Time</p>
                <p class="text-2xl font-bold">2.4s</p>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-green-600">
                        <i data-lucide="arrow-up" class="w-3 h-3"></i>
                        <span>+2.1%</span>
                    </div>
                </div>
                <p class="text-sm text-muted-foreground mb-1">Success Rate</p>
                <p class="text-2xl font-bold">98.7%</p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Module Activity Trends -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Module Activity Trends</h3>
            </div>
            <div class="p-6 pt-0">
                <div class="w-full h-[300px]">
                    <canvas id="moduleActivityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Comparison -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Monthly Comparison</h3>
            </div>
            <div class="p-6 pt-0">
                <div class="w-full h-[300px]">
                    <canvas id="monthlyComparisonChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        const labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun"];
        
        // Data
        const researchData = [145, 167, 189, 201, 223, 245];
        const contentData = [234, 256, 298, 312, 334, 367];
        const socialData = [187, 201, 223, 245, 267, 289];
        const kbData = [98, 112, 134, 145, 167, 178];

        // Module Activity Trends (Line Chart)
        const lineCtx = document.getElementById('moduleActivityChart').getContext('2d');
        window.createChart(lineCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Research',
                        data: researchData,
                        borderColor: '#3b82f6',
                        backgroundColor: '#3b82f6',
                        tension: 0.4
                    },
                    {
                        label: 'Content',
                        data: contentData,
                        borderColor: '#8b5cf6',
                        backgroundColor: '#8b5cf6',
                        tension: 0.4
                    },
                    {
                        label: 'Social',
                        data: socialData,
                        borderColor: '#10b981',
                        backgroundColor: '#10b981',
                        tension: 0.4
                    },
                    {
                        label: 'Knowledge Base',
                        data: kbData,
                        borderColor: '#f59e0b',
                        backgroundColor: '#f59e0b',
                        tension: 0.4
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'oklch(0.90 0.01 264)' },
                        ticks: { color: 'oklch(0.52 0.015 264)' }
                    },
                    x: {
                        grid: { color: 'oklch(0.90 0.01 264)' },
                        ticks: { color: 'oklch(0.52 0.015 264)' }
                    }
                }
            }
        });

        // Monthly Comparison (Bar Chart)
        const barCtx = document.getElementById('monthlyComparisonChart').getContext('2d');
        window.createChart(barCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Research',
                        data: researchData,
                        backgroundColor: '#3b82f6'
                    },
                    {
                        label: 'Content',
                        data: contentData,
                        backgroundColor: '#8b5cf6'
                    },
                    {
                        label: 'Social',
                        data: socialData,
                        backgroundColor: '#10b981'
                    },
                    {
                        label: 'KB',
                        data: kbData,
                        backgroundColor: '#f59e0b'
                    }
                ]
            },
            options: {
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'oklch(0.90 0.01 264)' },
                        ticks: { color: 'oklch(0.52 0.015 264)' }
                    },
                    x: {
                        grid: { color: 'oklch(0.90 0.01 264)' },
                        ticks: { color: 'oklch(0.52 0.015 264)' }
                    }
                }
            }
        });
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/analytics/index.blade.php ENDPATH**/ ?>