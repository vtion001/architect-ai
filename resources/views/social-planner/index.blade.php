@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Daily Social Planner</h1>
        <p class="text-muted-foreground">Schedule and manage your social media content across platforms</p>
    </div>

    <!-- Platform Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
        $stats = [
            ['platform' => 'LinkedIn', 'posts' => 45, 'engagement' => '12.4%', 'color' => 'bg-blue-500'],
            ['platform' => 'Twitter', 'posts' => 127, 'engagement' => '8.2%', 'color' => 'bg-sky-400'],
            ['platform' => 'Instagram', 'posts' => 89, 'engagement' => '15.7%', 'color' => 'bg-pink-500'],
            ['platform' => 'Facebook', 'posts' => 34, 'engagement' => '6.9%', 'color' => 'bg-blue-600'],
        ];
        @endphp
        @foreach($stats as $stat)
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="w-10 h-10 {{ $stat['color'] }} rounded-lg flex items-center justify-center mb-3">
                    <span class="text-white font-semibold text-sm">{{ substr($stat['platform'], 0, 2) }}</span>
                </div>
                <h3 class="font-semibold mb-1">{{ $stat['platform'] }}</h3>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-muted-foreground">{{ $stat['posts'] }} posts</span>
                    <span class="text-green-600 font-medium">{{ $stat['engagement'] }}</span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Calendar -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center gap-2">
                    <i data-lucide="calendar" class="w-5 h-5"></i>
                    Content Calendar
                </h3>
            </div>
            <div class="p-6 pt-0">
                <!-- Simple Static Calendar Visual -->
                <div class="w-full text-center mb-4 font-semibold">January 2025</div>
                <div class="grid grid-cols-7 gap-1 text-center text-xs mb-2 text-muted-foreground">
                    <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center text-sm">
                    @for ($i = 0; $i < 3; $i++) <div class="text-muted-foreground p-2 opacity-50">{{ 29 + $i }}</div> @endfor
                    @for ($i = 1; $i <= 31; $i++)
                        <div class="p-2 rounded-md hover:bg-muted cursor-pointer {{ $i == 16 ? 'bg-primary text-primary-foreground hover:bg-primary' : '' }}">
                            {{ $i }}
                        </div>
                    @endfor
                </div>
                
                <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full mt-4">
                    <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                    Schedule New Post
                </button>
            </div>
        </div>

        <!-- Scheduled Posts -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm lg:col-span-2">
            <div class="flex flex-col space-y-1.5 p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-semibold leading-none tracking-tight">Scheduled Posts</h3>
                    <button class="inline-flex items-center justify-center rounded-md text-xs font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 px-3">
                        View All
                    </button>
                </div>
            </div>
            <div class="p-6 pt-0">
                <div class="space-y-4">
                    @php
                    $posts = [
                        [
                            'id' => 1, 'platform' => 'LinkedIn', 'content' => "Exciting product launch next week! Stay tuned...", 
                            'time' => "09:00 AM", 'date' => "2025-01-16", 'status' => "Scheduled", 'icon' => 'linkedin'
                        ],
                        [
                            'id' => 2, 'platform' => 'Twitter', 'content' => "Check out our latest blog post on AI trends", 
                            'time' => "12:30 PM", 'date' => "2025-01-16", 'status' => "Scheduled", 'icon' => 'twitter'
                        ],
                        [
                            'id' => 3, 'platform' => 'Instagram', 'content' => "Behind the scenes at our office", 
                            'time' => "03:00 PM", 'date' => "2025-01-16", 'status' => "Scheduled", 'icon' => 'instagram'
                        ],
                        [
                            'id' => 4, 'platform' => 'Facebook', 'content' => "Join our webinar on digital transformation", 
                            'time' => "05:00 PM", 'date' => "2025-01-17", 'status' => "Scheduled", 'icon' => 'facebook'
                        ],
                    ];
                    @endphp
                    @foreach($posts as $post)
                    <div class="flex items-start gap-4 p-4 border border-border rounded-lg hover:bg-muted/50 transition-colors">
                        <div class="w-10 h-10 bg-muted rounded-lg flex items-center justify-center">
                            <i data-lucide="{{ $post['icon'] }}" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-semibold text-sm">{{ $post['platform'] }}</h3>
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-green-100 text-green-700">
                                    {{ $post['status'] }}
                                </span>
                            </div>
                            <p class="text-sm text-muted-foreground mb-2">{{ $post['content'] }}</p>
                            <div class="flex items-center gap-4 text-xs text-muted-foreground">
                                <span class="flex items-center gap-1">
                                    <i data-lucide="calendar" class="w-3 h-3"></i>
                                    {{ $post['date'] }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    {{ $post['time'] }}
                                </span>
                            </div>
                        </div>
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                            Edit
                        </button>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
