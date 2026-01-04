@extends('layouts.app')

@section('content')
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
                        <span>Live</span>
                    </div>
                </div>
                <p class="text-sm text-muted-foreground mb-1">Total Activities</p>
                <p class="text-2xl font-bold">{{ number_format($totalActivities) }}</p>
            </div>
        </div>

        <!-- Active Users -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="users" class="w-5 h-5 text-purple-600"></i>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-green-600">
                        <i data-lucide="shield-check" class="w-3 h-3"></i>
                        <span>Verified</span>
                    </div>
                </div>
                <p class="text-sm text-muted-foreground mb-1">Active Identities</p>
                <p class="text-2xl font-bold">{{ number_format($activeUsersCount) }}</p>
            </div>
        </div>

        <!-- Token Consumption -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="coins" class="w-5 h-5 text-amber-600"></i>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-amber-600">
                        <i data-lucide="zap" class="w-3 h-3"></i>
                        <span>Usage</span>
                    </div>
                </div>
                <p class="text-sm text-muted-foreground mb-1">Tokens Consumed</p>
                <p class="text-2xl font-bold">{{ number_format($tokensConsumed) }}</p>
            </div>
        </div>

        <!-- Success Rate -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div class="flex items-center gap-1 text-xs text-green-600">
                        <i data-lucide="check" class="w-3 h-3"></i>
                        <span>Stable</span>
                    </div>
                </div>
                <p class="text-sm text-muted-foreground mb-1">System Success Rate</p>
                <p class="text-2xl font-bold">{{ $successRate }}%</p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Module Activity Trends -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Module Activity Trends</h3>
                <p class="text-xs text-muted-foreground italic">Volume of actions performed across the platform over 6 months.</p>
            </div>
            <div class="p-6 pt-0">
                <div class="w-full h-[300px]">
                    <canvas id="moduleActivityChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Platform Distribution -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Activity Distribution</h3>
                <p class="text-xs text-muted-foreground italic">Comparative volume of Research vs Content vs Social actions.</p>
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
        const labels = @js($labels);
        
        // Data from Controller
        const researchData = @js($researchTrend);
        const contentData = @js($contentTrend);
        const socialData = @js($socialTrend);
        const kbData = @js($kbTrend);

        // Module Activity Trends (Line Chart)
        const lineCtx = document.getElementById('moduleActivityChart').getContext('2d');
        window.createChart(lineCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Research Engine',
                        data: researchData,
                        borderColor: '#3b82f6',
                        backgroundColor: '#3b82f6',
                        tension: 0.4,
                        pointRadius: 4
                    },
                    {
                        label: 'Content Creator',
                        data: contentData,
                        borderColor: '#8b5cf6',
                        backgroundColor: '#8b5cf6',
                        tension: 0.4,
                        pointRadius: 4
                    },
                    {
                        label: 'Social Planner',
                        data: socialData,
                        borderColor: '#10b981',
                        backgroundColor: '#10b981',
                        tension: 0.4,
                        pointRadius: 4
                    },
                    {
                        label: 'Knowledge Base',
                        data: kbData,
                        borderColor: '#f59e0b',
                        backgroundColor: '#f59e0b',
                        tension: 0.4,
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { boxWidth: 8, usePointStyle: true, font: { size: 10, weight: 'bold' } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
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
                        backgroundColor: '#3b82f6',
                        borderRadius: 4
                    },
                    {
                        label: 'Content',
                        data: contentData,
                        backgroundColor: '#8b5cf6',
                        borderRadius: 4
                    },
                    {
                        label: 'Social',
                        data: socialData,
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    },
                    {
                        label: 'Knowledge',
                        data: kbData,
                        backgroundColor: '#f59e0b',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { boxWidth: 8, usePointStyle: true, font: { size: 10, weight: 'bold' } }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { font: { size: 10 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    });
</script>
@endsection