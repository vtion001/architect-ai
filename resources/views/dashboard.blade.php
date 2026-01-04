@extends('layouts.app')

@section('content')
<div class="p-8">
    <!-- Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Research Requests -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground mb-1">Research Reports</p>
                        <p class="text-3xl font-bold mb-2">{{ number_format($researchCount) }}</p>
                        <div class="flex items-center gap-1 text-xs text-green-600">
                            <i data-lucide="activity" class="w-3 h-3"></i>
                            <span>Live Telemetry</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="brain" class="w-5 h-5 text-blue-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Generated -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground mb-1">Content Projects</p>
                        <p class="text-3xl font-bold mb-2">{{ number_format($contentCount) }}</p>
                        <div class="flex items-center gap-1 text-xs text-blue-600">
                            <i data-lucide="check-circle" class="w-3 h-3"></i>
                            <span>Synced to social</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="pencil" class="w-5 h-5 text-cyan-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Token Balance -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground mb-1">Token Treasury</p>
                        <p class="text-3xl font-bold mb-2">{{ number_format($tokenBalance) }}</p>
                        <div class="flex items-center gap-1 text-xs text-amber-600">
                            <i data-lucide="coins" class="w-3 h-3"></i>
                            <span>Enterprise Allocation</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="wallet" class="w-5 h-5 text-amber-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Modules -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground mb-1">Online Modules</p>
                        <p class="text-3xl font-bold mb-2">{{ count($moduleUsageData) }}/{{ count($moduleUsageData) }}</p>
                        <div class="flex items-center gap-1 text-xs text-green-600">
                            <i data-lucide="shield-check" class="w-3 h-3"></i>
                            <span>All Systems Operational</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="server" class="w-5 h-5 text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Module Usage Statistics -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-row items-center justify-between p-6 pb-2">
                <h3 class="text-base font-semibold">Module Usage Statistics</h3>
                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 w-9">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div class="relative w-48 h-48">
                        <canvas id="moduleUsageChart"></canvas>
                        <div class="absolute inset-0 flex items-center justify-center flex-col pointer-events-none">
                            <p class="text-3xl font-bold">{{ number_format(collect($moduleUsageData)->sum('value')) }}</p>
                            <p class="text-xs text-muted-foreground">Total Actions</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach($moduleUsageData as $module)
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-2 h-2 rounded-full" style="background-color: {{ $module['color'] }}"></div>
                                <p class="text-sm font-medium">{{ $module['name'] }}</p>
                            </div>
                            <p class="text-2xl font-bold ml-4">{{ number_format($module['value']) }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Generation Trends -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-row items-center justify-between p-6 pb-2">
                <h3 class="text-base font-semibold">Content Generation Trends</h3>
                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 w-9">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
            </div>
            <div class="p-6">
                <div class="w-full h-[240px]">
                    <canvas id="contentTrendsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities Table -->
    <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
        <div class="flex flex-row items-center justify-between p-6">
            <h3 class="text-base font-semibold">Recent Platform Protocol Events</h3>
            <div class="flex gap-2">
                <a href="{{ route('analytics.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                    Analyze All
                </a>
            </div>
        </div>
        <div class="p-6 pt-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border">
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground">ID</th>
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground">Identity</th>
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground">Module</th>
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground">Context / Topic</th>
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground">Timestamp</th>
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground">Protocol</th>
                            <th class="text-left py-3 px-4 text-xs font-black uppercase tracking-widest text-muted-foreground">Result</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentActivities as $activity)
                        <tr class="border-b border-border/50 hover:bg-muted/30 transition-colors">
                            <td class="py-4 px-4 text-[10px] font-mono text-muted-foreground uppercase">{{ $activity['activityId'] }}</td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-6 w-6 rounded-full bg-primary/10 flex items-center justify-center overflow-hidden border border-primary/20">
                                        <span class="text-[10px] font-black text-primary">{{ substr($activity['user']['name'], 0, 1) }}</span>
                                    </div>
                                    <span class="text-xs font-bold text-foreground">{{ $activity['user']['name'] }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-medium">{{ $activity['module'] }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-xs font-medium max-w-[200px] truncate" title="{{ $activity['topic'] }}">{{ $activity['topic'] }}</td>
                            <td class="py-4 px-4 text-[10px] font-bold text-muted-foreground uppercase tracking-tight">{{ $activity['date'] }}</td>
                            <td class="py-4 px-4">
                                <span class="text-[10px] font-black uppercase tracking-widest text-primary/70">IA-Protocol</span>
                            </td>
                            <td class="py-4 px-4">
                                @php
                                    $statusClass = match($activity['status']) {
                                        'Success' => 'bg-green-100 text-green-700',
                                        'Failure' => 'bg-red-100 text-red-700',
                                        'Denied' => 'bg-orange-100 text-orange-700',
                                        default => 'bg-slate-100 text-slate-700'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full border px-2 py-0.5 text-[9px] font-black uppercase tracking-widest {{ $statusClass }}">
                                    {{ $activity['status'] }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="module">
    document.addEventListener('DOMContentLoaded', function() {
        // Module Usage Chart (Pie)
        const moduleCtx = document.getElementById('moduleUsageChart').getContext('2d');
        const moduleData = @js($moduleUsageData);
        window.createChart(moduleCtx, {
            type: 'doughnut',
            data: {
                labels: moduleData.map(m => m.name),
                datasets: [{
                    data: moduleData.map(m => m.value),
                    backgroundColor: moduleData.map(m => m.color),
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                cutout: '75%',
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Content Trends Chart (Line)
        const trendsCtx = document.getElementById('contentTrendsChart').getContext('2d');
        const trendData = @js($contentTrendsData);
        const labels = trendData.map(d => d.month);
        const requests = trendData.map(d => d.requests);
        const generated = trendData.map(d => d.generated);

        window.createChart(trendsCtx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Requests',
                        data: requests,
                        borderColor: 'oklch(0.55 0.22 264)',
                        backgroundColor: 'oklch(0.55 0.22 264)',
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    },
                    {
                        label: 'Generated',
                        data: generated,
                        borderColor: 'oklch(0.75 0.15 264)',
                        backgroundColor: 'oklch(0.75 0.15 264)',
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }
                ]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'oklch(0.90 0.01 264)'
                        },
                        ticks: {
                            color: 'oklch(0.52 0.015 264)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: 'oklch(0.52 0.015 264)'
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
