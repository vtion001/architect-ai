{{-- Recent Activity Feed Sidebar Partial --}}
<div x-show="generator !== 'video' && !generatedCalendar" 
     class="rounded-xl border border-border bg-card text-card-foreground shadow-sm overflow-hidden">
    <div class="flex items-center justify-between p-4 border-b border-border/50 bg-background/50 sticky top-0 z-10 gap-2">
        <h3 class="text-sm font-bold leading-none tracking-tight flex items-center gap-2">
            <i data-lucide="activity" class="w-4 h-4 text-primary"></i>
            Activity Feed
        </h3>
        <div class="flex gap-1">
            <button @click="contentFeedFilter = 'all'"
                    :class="contentFeedFilter === 'all' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80'"
                    class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                All
            </button>
            <button @click="contentFeedFilter = 'social-media'"
                    :class="contentFeedFilter === 'social-media' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80'"
                    class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                Social
            </button>
            <button @click="contentFeedFilter = 'blog'"
                    :class="contentFeedFilter === 'blog' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80'"
                    class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                Blog
            </button>
            <button @click="contentFeedFilter = 'video'"
                    :class="contentFeedFilter === 'video' ? 'bg-primary text-white' : 'bg-muted text-muted-foreground hover:bg-muted/80'"
                    class="px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">
                Video
            </button>
        </div>
    </div>
    
    <div class="bg-muted/5 p-4 min-h-[400px]">
        <div class="flex flex-col gap-2">
            @forelse($recentContents as $item)
                @php
                    $statusColors = [
                        'published' => 'text-green-700 bg-green-50/80 border-green-200',
                        'draft' => 'text-amber-700 bg-amber-50/80 border-amber-200',
                        'generating' => 'text-blue-700 bg-blue-50/80 border-blue-200',
                        'failed' => 'text-red-700 bg-red-50/80 border-red-200',
                        'scheduled' => 'text-purple-700 bg-purple-50/80 border-purple-200'
                    ];
                    $statusColor = $statusColors[$item->status] ?? 'text-slate-700 bg-slate-50 border-slate-200';
                    
                    $typeIcons = [
                        'social-media' => 'share-2',
                        'blog' => 'book-open',
                        'blog_batch' => 'book-open',
                        'video' => 'video',
                        'email' => 'mail'
                    ];
                    $icon = $typeIcons[$item->type] ?? 'file-text';
                @endphp
                
                <a href="{{ route('content-creator.show', $item) }}" 
                   x-data="{ itemType: '{{ $item->type }}' }"
                   x-show="contentFeedFilter === 'all' || itemType === contentFeedFilter"
                   class="group flex items-center gap-3 p-3 rounded-xl border border-border bg-card hover:bg-white hover:shadow-md hover:border-primary/30 transition-all duration-200">
                    
                    {{-- Icon / Indicator --}}
                    <div class="w-10 h-10 rounded-lg bg-muted/50 border border-border flex items-center justify-center shrink-0 group-hover:bg-primary/5 group-hover:border-primary/10 transition-colors">
                        <i data-lucide="{{ $icon }}" class="w-5 h-5 text-muted-foreground group-hover:text-primary transition-colors"></i>
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold text-foreground truncate group-hover:text-primary transition-colors">{{ $item->title }}</h4>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[9px] font-black uppercase tracking-widest text-muted-foreground">{{ $item->created_at->diffForHumans() }}</span>
                            <span class="text-muted-foreground/30 text-[10px]">•</span>
                            <span class="text-[9px] font-bold uppercase tracking-widest {{ $statusColor }} px-1.5 py-0.5 rounded-md border">
                                {{ $item->status }}
                            </span>
                        </div>
                    </div>

                    {{-- Chevron --}}
                    <div class="shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                        <i data-lucide="chevron-right" class="w-4 h-4 text-primary"></i>
                    </div>
                </a>
            @empty
                {{-- Empty State --}}
                <div class="text-center py-12 text-muted-foreground opacity-50 bg-card/50 rounded-xl border border-border border-dashed">
                    <i data-lucide="rss" class="w-10 h-10 mx-auto mb-3"></i>
                    <p class="text-sm font-medium">Activity feed is empty.</p>
                </div>
            @endforelse
        </div>
        </div>
    </div>
</div>
