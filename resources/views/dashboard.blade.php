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
                        <p class="text-sm text-muted-foreground mb-1">Research Requests</p>
                        <p class="text-3xl font-bold mb-2">1,456</p>
                        <div class="flex items-center gap-1 text-xs text-green-600">
                            <i data-lucide="trending-up" class="w-3 h-3"></i>
                            <span>+6.8% Since last week</span>
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
                        <p class="text-sm text-muted-foreground mb-1">Content Generated</p>
                        <p class="text-3xl font-bold mb-2">3,345</p>
                        <div class="flex items-center gap-1 text-xs text-red-600">
                            <i data-lucide="trending-down" class="w-3 h-3"></i>
                            <span>-0.8% Since last week</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="pencil" class="w-5 h-5 text-cyan-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Uptime -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground mb-1">System Uptime</p>
                        <p class="text-3xl font-bold mb-2">99.8%</p>
                        <div class="flex items-center gap-1 text-xs text-red-600">
                            <i data-lucide="trending-down" class="w-3 h-3"></i>
                            <span>-0.2% Since last week</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="bar-chart-3" class="w-5 h-5 text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Modules -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground mb-1">Active Modules</p>
                        <p class="text-3xl font-bold mb-2">4/4</p>
                        <div class="flex items-center gap-1 text-xs text-green-600">
                            <i data-lucide="trending-up" class="w-3 h-3"></i>
                            <span>+100% Since last week</span>
                        </div>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="database" class="w-5 h-5 text-blue-600"></i>
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
                            <p class="text-3xl font-bold">2,228</p>
                            <p class="text-xs text-muted-foreground">Total</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-2 h-2 rounded-full bg-[oklch(0.55_0.22_264)]"></div>
                                <p class="text-sm font-medium">Research Engine</p>
                            </div>
                            <p class="text-2xl font-bold ml-4">1,135</p>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-2 h-2 rounded-full bg-[oklch(0.35_0.12_264)]"></div>
                                <p class="text-sm font-medium">Content Creator</p>
                            </div>
                            <p class="text-2xl font-bold ml-4">514</p>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-2 h-2 rounded-full bg-[oklch(0.75_0.15_264)]"></div>
                                <p class="text-sm font-medium">Social Planner</p>
                            </div>
                            <p class="text-2xl font-bold ml-4">345</p>
                        </div>
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
            <h3 class="text-base font-semibold">Recent Activities</h3>
            <div class="flex gap-2">
                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                    Filter
                </button>
                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 w-9">
                    <i data-lucide="more-vertical" class="w-4 h-4"></i>
                </button>
            </div>
        </div>
        <div class="p-6 pt-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border">
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">No</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">Activity ID</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">User / Agent</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">Module</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">Topic</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">Order Date</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">Status</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">Output</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $activities = [
                            [
                                'id' => 1,
                                'activityId' => '#065499',
                                'user' => ['name' => 'Sarah Chen', 'avatar' => null],
                                'module' => 'Research Engine',
                                'topic' => 'Market Analysis Q4',
                                'date' => '21/07/2025 05:21',
                                'status' => 'Completed',
                                'output' => 847,
                            ],
                            [
                                'id' => 2,
                                'activityId' => '#065498',
                                'user' => ['name' => 'Marcus Rodriguez', 'avatar' => null],
                                'module' => 'Content Creator',
                                'topic' => 'LinkedIn Campaign',
                                'date' => '21/07/2025 04:15',
                                'status' => 'In Progress',
                                'output' => 523,
                            ],
                            [
                                'id' => 3,
                                'activityId' => '#065497',
                                'user' => ['name' => 'Aisha Patel', 'avatar' => null],
                                'module' => 'Social Planner',
                                'topic' => 'Weekly Schedule',
                                'date' => '21/07/2025 03:42',
                                'status' => 'Completed',
                                'output' => 312,
                            ],
                            [
                                'id' => 4,
                                'activityId' => '#065496',
                                'user' => ['name' => 'James Kim', 'avatar' => null],
                                'module' => 'Knowledge Base',
                                'topic' => 'Brand Guidelines Update',
                                'date' => '21/07/2025 02:18',
                                'status' => 'Pending',
                                'output' => 1247,
                            ],
                        ];
                        @endphp
                        @foreach($activities as $activity)
                        <tr class="border-b border-border hover:bg-muted/50">
                            <td class="py-4 px-4 text-sm">{{ $activity['id'] }}</td>
                            <td class="py-4 px-4 text-sm font-medium">{{ $activity['activityId'] }}</td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="h-6 w-6 rounded-full bg-muted flex items-center justify-center overflow-hidden">
                                        @if($activity['user']['avatar'])
                                            <img src="{{ $activity['user']['avatar'] }}" alt="{{ $activity['user']['name'] }}" class="h-full w-full object-cover">
                                        @else
                                            <span class="text-xs font-medium">{{ substr($activity['user']['name'], 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <span class="text-sm">{{ $activity['user']['name'] }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    @if($activity['module'] === 'Research Engine')
                                        <i data-lucide="brain" class="w-4 h-4"></i>
                                    @elseif($activity['module'] === 'Content Creator')
                                        <i data-lucide="pencil" class="w-4 h-4"></i>
                                    @else
                                        <i data-lucide="database" class="w-4 h-4"></i>
                                    @endif
                                    <span class="text-sm">{{ $activity['module'] }}</span>
                                </div>
                            </td>
                            <td class="py-4 px-4 text-sm">{{ $activity['topic'] }}</td>
                            <td class="py-4 px-4 text-sm text-muted-foreground">{{ $activity['date'] }}</td>
                            <td class="py-4 px-4">
                                @php
                                    $statusClass = match($activity['status']) {
                                        'Completed' => 'bg-blue-100 text-blue-700 hover:bg-blue-100',
                                        'In Progress' => 'bg-amber-100 text-amber-700 hover:bg-amber-100',
                                        'Pending' => 'bg-red-100 text-red-700 hover:bg-red-100',
                                        default => 'bg-secondary text-secondary-foreground'
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 {{ $statusClass }}">
                                    {{ $activity['status'] }}
                                </span>
                            </td>
                            <td class="py-4 px-4 text-sm font-medium">${{ $activity['output'] }}</td>
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
        window.createChart(moduleCtx, {
            type: 'doughnut',
            data: {
                labels: ['Research Engine', 'Content Creator', 'Social Planner', 'Knowledge Base'],
                datasets: [{
                    data: [1135, 514, 345, 234],
                    backgroundColor: [
                        'oklch(0.55 0.22 264)',
                        'oklch(0.35 0.12 264)',
                        'oklch(0.75 0.15 264)',
                        'oklch(0.88 0.08 264)'
                    ],
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
        const labels = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        const requests = [45, 52, 61, 75, 68, 73, 82, 79, 88, 85, 92, 88];
        const generated = [38, 45, 55, 68, 62, 67, 76, 71, 82, 78, 86, 82];

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
