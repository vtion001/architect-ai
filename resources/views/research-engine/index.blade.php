@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Deep Research & Report Engine</h1>
        <p class="text-muted-foreground">
            AI-powered comprehensive research tool that gathers information from multiple sources
        </p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Total Reports</p>
                        <p class="text-2xl font-bold">847</p>
                    </div>
                    <i data-lucide="file-text" class="w-8 h-8 text-blue-500"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Active Research</p>
                        <p class="text-2xl font-bold">12</p>
                    </div>
                    <i data-lucide="clock" class="w-8 h-8 text-amber-500"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Sources Analyzed</p>
                        <p class="text-2xl font-bold">15.2K</p>
                    </div>
                    <i data-lucide="globe" class="w-8 h-8 text-green-500"></i>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Success Rate</p>
                        <p class="text-2xl font-bold">98.5%</p>
                    </div>
                    <i data-lucide="trending-up" class="w-8 h-8 text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- New Research Form -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center gap-2">
                    <i data-lucide="brain" class="w-5 h-5"></i>
                    Start New Research
                </h3>
                <p class="text-sm text-muted-foreground">Enter your research topic and let AI gather comprehensive insights</p>
            </div>
            <div class="p-6 pt-0 space-y-4">
                <div>
                    <label for="research-title" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Research Title</label>
                    <input type="text" id="research-title" placeholder="e.g., AI Trends in Healthcare 2025" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1.5" />
                </div>
                <div>
                    <label for="research-query" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Research Query</label>
                    <textarea id="research-query" placeholder="Describe what you want to research in detail..." rows="6" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1.5"></textarea>
                </div>
                <div class="flex gap-2">
                    <button class="flex-1 inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                        <i data-lucide="search" class="w-4 h-4 mr-2"></i>
                        Start Research
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Research -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-row items-center justify-between p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Recent Research</h3>
                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 px-3">
                    <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                    Filter
                </button>
            </div>
            <div class="p-6 pt-0">
                <div class="space-y-4">
                    @php
                    $recentResearches = [
                        [
                            'id' => 1,
                            'title' => "Q4 Market Analysis - Tech Industry",
                            'status' => "Completed",
                            'date' => "2025-01-15",
                            'sources' => 47,
                            'pages' => 23,
                        ],
                        [
                            'id' => 2,
                            'title' => "Competitor Analysis - AI SaaS Platforms",
                            'status' => "In Progress",
                            'date' => "2025-01-14",
                            'sources' => 32,
                            'pages' => 15,
                        ],
                        [
                            'id' => 3,
                            'title' => "Consumer Behavior Trends 2025",
                            'status' => "Completed",
                            'date' => "2025-01-13",
                            'sources' => 56,
                            'pages' => 31,
                        ],
                    ];
                    @endphp

                    @foreach($recentResearches as $research)
                    <div class="p-4 border border-border rounded-lg hover:bg-muted/50 transition-colors">
                        <div class="flex items-start justify-between mb-2">
                            <h3 class="font-semibold text-sm">{{ $research['title'] }}</h3>
                            @php
                                $statusClass = $research['status'] === "Completed" ? "bg-green-100 text-green-700 hover:bg-green-100" : "bg-amber-100 text-amber-700 hover:bg-amber-100";
                            @endphp
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 {{ $statusClass }}">
                                {{ $research['status'] }}
                            </span>
                        </div>
                        <div class="flex items-center gap-4 text-xs text-muted-foreground mb-3">
                            <span class="flex items-center gap-1">
                                <i data-lucide="globe" class="w-3 h-3"></i>
                                {{ $research['sources'] }} sources
                            </span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="file-text" class="w-3 h-3"></i>
                                {{ $research['pages'] }} pages
                            </span>
                            <span>{{ $research['date'] }}</span>
                        </div>
                        @if($research['status'] === "Completed")
                        <button class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-3">
                            <i data-lucide="download" class="w-3 h-3 mr-2"></i>
                            Download Report
                        </button>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
