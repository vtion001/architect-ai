@extends('layouts.app')

@section('content')
<div class="p-8 max-w-7xl mx-auto" x-data="socialPlanner()">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold mb-2">Daily Social Planner</h1>
            <p class="text-muted-foreground">Schedule and manage your social media content across platforms</p>
        </div>
        <button @click="showConnectModal = true" class="flex items-center gap-2 px-4 py-2 bg-white border border-border rounded-lg text-sm font-bold shadow-sm hover:bg-muted/50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="settings" class="lucide lucide-settings w-4 h-4 text-muted-foreground"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.38a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.51a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            Connect Channels
        </button>
    </div>

    <!-- Platform Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- ... (Stats cards remain unchanged) ... -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mb-3">
                    <span class="text-white font-semibold text-sm">Li</span>
                </div>
                <h3 class="font-semibold mb-1">LinkedIn</h3>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-muted-foreground">0 posts</span>
                    <span class="text-green-600 font-medium">0.0%</span>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="w-10 h-10 bg-sky-400 rounded-lg flex items-center justify-center mb-3">
                    <span class="text-white font-semibold text-sm">Tw</span>
                </div>
                <h3 class="font-semibold mb-1">Twitter</h3>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-muted-foreground">0 posts</span>
                    <span class="text-green-600 font-medium">0.0%</span>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="w-10 h-10 bg-pink-500 rounded-lg flex items-center justify-center mb-3">
                    <span class="text-white font-semibold text-sm">In</span>
                </div>
                <h3 class="font-semibold mb-1">Instagram</h3>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-muted-foreground">0 posts</span>
                    <span class="text-green-600 font-medium">0.0%</span>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center mb-3">
                    <span class="text-white font-semibold text-sm">Fa</span>
                </div>
                <h3 class="font-semibold mb-1">Facebook</h3>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-muted-foreground">0 posts</span>
                    <span class="text-green-600 font-medium">0.0%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Calendar -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="calendar" class="lucide lucide-calendar w-5 h-5"><path d="M8 2v4"></path><path d="M16 2v4"></path><rect width="18" height="18" x="3" y="4" rx="2"></rect><path d="M3 10h18"></path></svg>
                        Content Calendar
                    </h3>
                    <button @click="showCalendarModal = true" class="text-xs text-primary hover:underline">View Full</button>
                </div>
            </div>
            <div class="p-6 pt-0">
                <!-- Data-driven Mini Calendar via Alpine -->
                <div class="w-full text-center mb-4 font-semibold" x-text="monthName()"></div>
                <div class="grid grid-cols-7 gap-1 text-center text-xs mb-2 text-muted-foreground">
                    <div>Su</div><div>Mo</div><div>Tu</div><div>We</div><div>Th</div><div>Fr</div><div>Sa</div>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center text-sm">
                    <template x-for="i in startDayOfWeek">
                         <div class="text-muted-foreground p-2 opacity-20"></div>
                    </template>
                    <template x-for="day in daysInMonth">
                        <div class="p-2 rounded-md hover:bg-muted cursor-pointer" 
                             :class="{ 'bg-primary text-primary-foreground hover:bg-primary': day === new Date().getDate() && currentDate.getMonth() === new Date().getMonth() }"
                             x-text="day">
                        </div>
                    </template>
                </div>
                
                <button @click="showCreatePostModal = true" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 w-full mt-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="plus" class="lucide lucide-plus w-4 h-4 mr-2"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
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
            <div class="p-6 pt-0 max-h-[600px] overflow-y-auto">
                <div class="space-y-4">
                    @forelse($scheduledPosts as $post)
                        @php
                            $options = $post->options ?? [];
                            $platform = $options['platform'] ?? 'generic';
                            $date = \Carbon\Carbon::parse($options['scheduled_at'] ?? $post->created_at);
                            $bgColors = [
                                'linkedin' => 'bg-blue-600',
                                'twitter' => 'bg-sky-400',
                                'facebook' => 'bg-blue-700',
                                'instagram' => 'bg-pink-600',
                                'generic' => 'bg-gray-500'
                            ];
                            $bgColor = $bgColors[$platform] ?? 'bg-gray-500';
                            $initials = substr(ucfirst($platform), 0, 2);
                        @endphp
                        <div class="flex items-start gap-4 p-4 rounded-lg border border-border bg-muted/20 hover:bg-muted/30 transition-colors group">
                            <!-- Icon -->
                            <div class="w-10 h-10 {{ $bgColor }} rounded-lg flex items-center justify-center text-white font-bold shrink-0 shadow-sm">
                                {{ $initials }}
                            </div>
                            
                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h4 class="font-semibold text-sm truncate">{{ ucfirst($platform) }} Post</h4>
                                    <span class="text-xs text-muted-foreground">{{ $date->format('M d, g:i A') }}</span>
                                </div>
                                <p class="text-sm text-muted-foreground line-clamp-2 mb-2">{{ $post->result }}</p>
                                
                                @if(!empty($options['image_url']))
                                    <div class="w-24 h-16 rounded overflow-hidden shadow-sm border border-border">
                                        <img src="{{ $options['image_url'] }}" class="w-full h-full object-cover">
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-muted-foreground">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-calendar-x mb-4 opacity-50"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/><line x1="10" x2="14" y1="14" y2="18"/><line x1="14" x2="10" y1="14" y2="18"/></svg>
                            <p>No posts scheduled yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <!-- Full Calendar Modal -->
    <div x-show="showCalendarModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div @click.away="showCalendarModal = false" class="bg-card text-card-foreground w-full max-w-6xl h-[85vh] rounded-xl shadow-2xl flex flex-col border border-border">
            <div class="p-6 border-b border-border flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold" x-text="monthName()"></h2>
                    <p class="text-sm text-muted-foreground">Full content schedule</p>
                </div>
                <div class="flex items-center gap-2">
                    <button class="p-2 hover:bg-muted rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-left w-5 h-5"><path d="m15 18-6-6 6-6"/></svg>
                    </button>
                    <button class="p-2 hover:bg-muted rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-5 h-5"><path d="m9 18 6-6-6-6"/></svg>
                    </button>
                    <button @click="showCalendarModal = false" class="ml-4 p-2 hover:bg-red-100 text-red-500 rounded-full">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-5 h-5"><path d="M18 6 6 18"/><path d="m6 6 18 18"/></svg>
                    </button>
                </div>
            </div>
            
            <div class="flex-1 p-6 overflow-hidden flex flex-col">
                <div class="grid grid-cols-7 gap-px bg-border flex-1 border border-border rounded-lg overflow-hidden">
                    <!-- Days Header -->
                    <div class="bg-muted/50 p-3 text-center text-sm font-semibold">Sun</div>
                    <div class="bg-muted/50 p-3 text-center text-sm font-semibold">Mon</div>
                    <div class="bg-muted/50 p-3 text-center text-sm font-semibold">Tue</div>
                    <div class="bg-muted/50 p-3 text-center text-sm font-semibold">Wed</div>
                    <div class="bg-muted/50 p-3 text-center text-sm font-semibold">Thu</div>
                    <div class="bg-muted/50 p-3 text-center text-sm font-semibold">Fri</div>
                    <div class="bg-muted/50 p-3 text-center text-sm font-semibold">Sat</div>

                    <!-- Calendar Grid -->
                    <template x-for="i in startDayOfWeek">
                         <div class="bg-card p-2 opacity-50 min-h-[100px]"></div>
                    </template>
                    
                    <template x-for="day in daysInMonth">
                        <div class="bg-card p-2 min-h-[120px] relative hover:bg-accent/5 transition-colors group border-t border-l border-border/20">
                            <span class="text-sm font-medium w-7 h-7 flex items-center justify-center rounded-full" 
                                  :class="{ 'bg-primary text-primary-foreground': day === new Date().getDate() && currentDate.getMonth() === new Date().getMonth() }"
                                  x-text="day"></span>
                            
                            <!-- Sample Mock Post Visual (Demonstration) -->
                            <div x-show="day === 16" class="mt-2 text-xs">
                                <div class="bg-card border border-border rounded overflow-hidden shadow-sm group-hover:shadow-md transition-all cursor-pointer">
                                    <div class="h-16 bg-muted relative">
                                        <!-- Thumbnail Placeholder -->
                                        <img src="https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=300&h=150&fit=crop" class="w-full h-full object-cover opacity-80" alt="Post thumbnail">
                                        <div class="absolute top-1 right-1 w-5 h-5 bg-blue-500 rounded flex items-center justify-center">
                                             <span class="text-[10px] text-white font-bold">Li</span>
                                        </div>
                                    </div>
                                    <div class="p-1 px-2 truncate font-medium">Product Launch</div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Post Modal -->
    <div x-show="showCreatePostModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div @click.away="showCreatePostModal = false" class="bg-card text-card-foreground w-full max-w-2xl rounded-xl shadow-2xl flex flex-col border border-border max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-border flex items-center justify-between">
                <h2 class="text-xl font-bold">Schedule New Post</h2>
                <button @click="showCreatePostModal = false" class="p-2 hover:bg-red-100 text-red-500 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x w-5 h-5"><path d="M18 6 6 18"/><path d="m6 6 18 18"/></svg>
                </button>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Topic Selection & AI Suggestions -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Post Topic / Theme <span class="text-red-500">*</span></label>
                        <button @click="fetchSuggestions()" :disabled="isLoadingSuggestions || !topic" class="bg-muted px-3 py-1 rounded border border-border text-[10px] font-bold flex items-center gap-1.5 hover:bg-muted/80 disabled:opacity-50">
                            <span x-show="!isLoadingSuggestions" class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="sparkles" class="lucide lucide-sparkles w-3 h-3 text-primary"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>
                                GET SUGGESTIONS
                            </span>
                            <span x-show="isLoadingSuggestions">Running Gemini...</span>
                        </button>
                    </div>
                    <input x-model="topic" type="text" placeholder="e.g., 'Modern Architecture Trends 2026'" class="w-full h-14 bg-muted/20 border border-border rounded-xl px-5 text-sm font-medium focus:ring-1 focus:ring-primary">
                    
                    <!-- Suggestions Results -->
                    <div x-show="suggestions" x-transition class="p-4 bg-muted/30 border border-border rounded-lg">
                        <h4 class="text-xs font-bold uppercase mb-2 text-primary">Gemini Ideas:</h4>
                        <div class="prose prose-sm max-w-none text-muted-foreground whitespace-pre-wrap text-sm" x-text="suggestions"></div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Post Content</label>
                    <textarea x-model="newPostContent" class="w-full h-32 bg-muted/20 border border-border rounded-xl p-4 text-sm font-medium focus:ring-1 focus:ring-primary resize-none" placeholder="Write your caption here..."></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                     <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Date</label>
                        <input x-model="newPostDate" type="date" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-foreground italic">Time</label>
                        <input x-model="newPostTime" type="time" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-medium">
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button @click="schedulePost" :disabled="isScheduling" class="bg-primary text-primary-foreground px-6 py-2 rounded-lg text-sm font-bold hover:bg-primary/90 flex items-center gap-2 disabled:opacity-50">
                        <span x-show="isScheduling" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <span x-text="isScheduling ? 'Scheduling...' : 'Schedule Post'"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Connect Channels Modal -->
    <div x-show="showConnectModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;">
        
        <div @click.away="showConnectModal = false" class="bg-card text-card-foreground w-full max-w-lg rounded-xl shadow-2xl flex flex-col border border-border">
            <div class="p-6 border-b border-border flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold">Connect Channels</h2>
                    <p class="text-sm text-muted-foreground">Manage your social media integrations</p>
                </div>
                <button @click="showConnectModal = false" class="p-2 hover:bg-muted rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-x"><path d="M18 6 6 18"/><path d="m6 6 18 18"/></svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <!-- Facebook -->
                <div class="flex items-center justify-between p-4 rounded-xl border border-border bg-card hover:bg-muted/10 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-lg">Fb</div>
                        <div>
                            <h4 class="font-bold text-sm">Facebook</h4>
                            <p class="text-xs text-muted-foreground" x-text="connectedAccounts.facebook ? 'Connected as User' : 'Not connected'"></p>
                        </div>
                    </div>
                    <button @click="connectAccount('facebook')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase transition-all"
                            :class="connectedAccounts.facebook ? 'bg-green-500/10 text-green-600 hover:bg-green-500/20' : 'bg-primary text-primary-foreground hover:bg-primary/90'">
                        <span x-text="connectedAccounts.facebook ? 'Manage' : 'Connect'"></span>
                    </button>
                </div>

                <!-- Instagram -->
                <div class="flex items-center justify-between p-4 rounded-xl border border-border bg-card hover:bg-muted/10 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-500 flex items-center justify-center text-white font-bold text-lg">In</div>
                        <div>
                            <h4 class="font-bold text-sm">Instagram</h4>
                             <p class="text-xs text-muted-foreground" x-text="connectedAccounts.instagram ? 'Connected as User' : 'Not connected'"></p>
                        </div>
                    </div>
                     <button @click="connectAccount('instagram')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase transition-all"
                            :class="connectedAccounts.instagram ? 'bg-green-500/10 text-green-600 hover:bg-green-500/20' : 'bg-primary text-primary-foreground hover:bg-primary/90'">
                        <span x-text="connectedAccounts.instagram ? 'Manage' : 'Connect'"></span>
                    </button>
                </div>

                <!-- LinkedIn -->
                <div class="flex items-center justify-between p-4 rounded-xl border border-border bg-card hover:bg-muted/10 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-blue-700 flex items-center justify-center text-white font-bold text-lg">Li</div>
                        <div>
                            <h4 class="font-bold text-sm">LinkedIn</h4>
                             <p class="text-xs text-muted-foreground" x-text="connectedAccounts.linkedin ? 'Connected as User' : 'Not connected'"></p>
                        </div>
                    </div>
                     <button @click="connectAccount('linkedin')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase transition-all"
                            :class="connectedAccounts.linkedin ? 'bg-green-500/10 text-green-600 hover:bg-green-500/20' : 'bg-primary text-primary-foreground hover:bg-primary/90'">
                        <span x-text="connectedAccounts.linkedin ? 'Manage' : 'Connect'"></span>
                    </button>
                </div>

                <!-- Twitter -->
                <div class="flex items-center justify-between p-4 rounded-xl border border-border bg-card hover:bg-muted/10 transition-colors">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-sky-500 flex items-center justify-center text-white font-bold text-lg">Tw</div>
                        <div>
                            <h4 class="font-bold text-sm">Twitter / X</h4>
                             <p class="text-xs text-muted-foreground" x-text="connectedAccounts.twitter ? 'Connected as User' : 'Not connected'"></p>
                        </div>
                    </div>
                     <button @click="connectAccount('twitter')" 
                            class="px-3 py-1.5 rounded-lg text-xs font-bold uppercase transition-all"
                            :class="connectedAccounts.twitter ? 'bg-green-500/10 text-green-600 hover:bg-green-500/20' : 'bg-primary text-primary-foreground hover:bg-primary/90'">
                        <span x-text="connectedAccounts.twitter ? 'Manage' : 'Connect'"></span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>
<script>
    window.socialPlannerConfig = @js($socialConfig);
    
    document.addEventListener('alpine:init', () => {
        Alpine.data('socialPlanner', () => ({
            showCalendarModal: false,
            showCreatePostModal: false,
            showConnectModal: false,
            topic: '',
            suggestions: '',
            isLoadingSuggestions: false,
            
            newPostContent: '',
            newPostDate: '',
            newPostTime: '',
            isScheduling: false,

            connectedAccounts: {
                linkedin: false,
                twitter: false,
                facebook: false,
                instagram: false
            },

            socialConfig: window.socialPlannerConfig || {},

            currentDate: new Date(),
            get daysInMonth() {
                return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth() + 1, 0).getDate();
            },
            get startDayOfWeek() {
                return new Date(this.currentDate.getFullYear(), this.currentDate.getMonth(), 1).getDay();
            },
            monthName() {
                return this.currentDate.toLocaleString('default', { month: 'long' }) + ' ' + this.currentDate.getFullYear();
            },
            async fetchSuggestions() {
                if (!this.topic || this.isLoadingSuggestions) return;
                this.isLoadingSuggestions = true;
                this.suggestions = '';
                try {
                    const response = await fetch('/social-planner/suggestions', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ topic: this.topic })
                    });
                    const data = await response.json();
                    this.suggestions = data.suggestions;
                } catch (e) {
                    console.error(e);
                    this.suggestions = 'Error fetching suggestions.';
                } finally {
                    this.isLoadingSuggestions = false;
                }
            },
            
            async schedulePost() {
                if (!this.newPostContent || !this.newPostDate || !this.newPostTime) {
                    alert('Please fill in all fields (Content, Date, Time)');
                    return;
                }
                
                this.isScheduling = true;
                const scheduledAt = `${this.newPostDate}T${this.newPostTime}:00`;

                try {
                    const response = await fetch('/social-planner/store', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                        body: JSON.stringify({ 
                            content: this.newPostContent,
                            scheduled_at: scheduledAt,
                            platform: 'generic'
                        })
                    });
                    const data = await response.json();
                    
                    if(data.success) {
                        alert('Post scheduled successfully!');
                        this.showCreatePostModal = false;
                        this.newPostContent = '';
                        this.topic = '';
                        this.suggestions = '';
                        window.location.reload();
                    } else {
                        alert('Failed to schedule post.');
                    }
                } catch (e) {
                    console.error(e);
                    alert('Error scheduling post.');
                } finally {
                    this.isScheduling = false;
                }
            },

            connectAccount(platform) {
                let url = '';
                let clientId = '';
                let redirectUri = '';
                let scope = '';
                let state = 'random_state_string';

                const config = this.socialConfig[platform];
                
                if (!config || !config.clientId) {
                    alert(`Please configure the ${platform.toUpperCase()}_CLIENT_ID in your .env file to connect.`);
                    return;
                }

                clientId = config.clientId;
                redirectUri = config.redirectUri || window.location.origin + '/social/callback/' + platform;

                switch(platform) {
                    case 'facebook':
                        scope = 'public_profile,email,pages_manage_posts,pages_read_engagement';
                        url = `https://www.facebook.com/v18.0/dialog/oauth?client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&scope=${scope}`;
                        break;

                    case 'instagram':
                        scope = 'user_profile,user_media';
                        url = `https://api.instagram.com/oauth/authorize?client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&scope=${scope}&response_type=code&state=${state}`;
                        break;

                    case 'linkedin':
                        scope = 'w_member_social,r_liteprofile';
                        url = `https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&state=${state}&scope=${scope}`;
                        break;

                    case 'twitter':
                        scope = 'tweet.read,tweet.write,users.read';
                        url = `https://twitter.com/i/oauth2/authorize?response_type=code&client_id=${clientId}&redirect_uri=${encodeURIComponent(redirectUri)}&scope=${scope}&state=${state}&code_challenge=challenge&code_challenge_method=plain`;
                        break;
                }

                if (url) {
                    const width = 600;
                    const height = 700;
                    const left = (screen.width / 2) - (width / 2);
                    const top = (screen.height / 2) - (height / 2);
                    window.open(url, 'ConnectChannel', `width=${width},height=${height},top=${top},left=${left}`);
                }
            },
            
            init() {
                window.addEventListener('message', (event) => {
                    if (event.data.type === 'connected') {
                        this.connectedAccounts[event.data.platform] = true;
                        alert(`Successfully connected to ${event.data.platform}!`);
                    }
                });
            }
        }));
    });
</script>
@endsection
