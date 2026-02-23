{{-- Incoming Content / External Drafts Sidebar Partial --}}
<div x-show="generator !== 'video' && !generatedCalendar" 
     class="rounded-xl border border-border bg-card text-card-foreground shadow-sm overflow-hidden mt-4">
    <div class="flex flex-col space-y-1.5 p-6 border-b border-border/50 bg-background/50 backdrop-blur-sm sticky top-0 z-10">
        <h3 class="text-xl font-bold leading-none tracking-tight flex items-center gap-2">
            <i data-lucide="inbox" class="w-5 h-5 text-primary"></i>
            Incoming Drafts
        </h3>
        <p class="text-xs text-muted-foreground">Content from n8n, OpenClaw & external sources</p>
    </div>
    
    <div class="bg-muted/5 p-4 min-h-[400px]">
        <div class="flex flex-col gap-2">
            @forelse($incomingContents as $item)
                @php
                    $sourceColors = [
                        'n8n' => 'text-blue-700 bg-blue-50/80 border-blue-200',
                        'openclaw' => 'text-purple-700 bg-purple-50/80 border-purple-200',
                        'external' => 'text-slate-700 bg-slate-50 border-slate-200',
                        'manual' => 'text-green-700 bg-green-50/80 border-green-200'
                    ];
                    $source = $item->options['source'] ?? 'manual';
                    $sourceColor = $sourceColors[$source] ?? $sourceColors['manual'];
                    
                    $typeIcons = [
                        'social-post' => 'share-2',
                        'blog-post' => 'book-open',
                        'video' => 'video',
                        'email' => 'mail'
                    ];
                    $icon = $typeIcons[$item->type] ?? 'file-text';
                    
                    $hasMedia = !empty($item->options['image_url']) || !empty($item->options['media_url']);
                @endphp
                
                <div class="group flex items-start gap-3 p-3 rounded-xl border border-border bg-card hover:bg-white hover:shadow-md hover:border-primary/30 transition-all duration-200"
                     x-data="{ 
                        showPublish: false,
                        publishing: false,
                        publishTo: {{ json_encode($item->options['platforms'] ?? []) }}
                     }">
                    
                    {{-- Icon / Indicator --}}
                    <div class="w-10 h-10 rounded-lg bg-muted/50 border border-border flex items-center justify-center shrink-0 group-hover:bg-primary/5 group-hover:border-primary/10 transition-colors">
                        @if($hasMedia)
                            <i data-lucide="image" class="w-5 h-5 text-muted-foreground group-hover:text-primary transition-colors"></i>
                        @else
                            <i data-lucide="{{ $icon }}" class="w-5 h-5 text-muted-foreground group-hover:text-primary transition-colors"></i>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold text-foreground truncate group-hover:text-primary transition-colors">{{ $item->title }}</h4>
                        <p class="text-[10px] text-muted-foreground truncate mt-0.5">{{ Str::limit($item->result, 80) }}</p>
                        <div class="flex items-center gap-2 mt-2 flex-wrap">
                            <span class="text-[9px] font-black uppercase tracking-widest text-muted-foreground">{{ $item->created_at->diffForHumans() }}</span>
                            <span class="text-muted-foreground/30 text-[10px]">•</span>
                            <span class="text-[9px] font-bold uppercase tracking-widest {{ $sourceColor }} px-1.5 py-0.5 rounded-md border">
                                {{ $source }}
                            </span>
                            @if(!empty($item->options['platforms']))
                                <span class="text-[9px] text-muted-foreground">
                                    @foreach($item->options['platforms'] as $platform)
                                        <span class="inline-flex items-center gap-0.5 mr-1">
                                            <i data-lucide="{{ $platform }}" class="w-2.5 h-2.5"></i>
                                        </span>
                                    @endforeach
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="shrink-0 flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button @click="showPublish = !showPublish" 
                                class="p-1.5 rounded-lg bg-primary/10 hover:bg-primary/20 text-primary transition-colors"
                                title="Publish">
                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                        </button>
                        <a href="{{ route('content-creator.show', $item) }}" 
                           class="p-1.5 rounded-lg bg-muted hover:bg-muted/80 text-muted-foreground transition-colors"
                           title="Edit">
                            <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>

                    {{-- Publish Dropdown --}}
                    <div x-show="showPublish" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-1"
                         class="absolute right-2 top-12 z-20 w-48 p-2 bg-card border border-border rounded-xl shadow-lg"
                         @click.outside="showPublish = false">
                        <p class="text-xs font-semibold text-foreground px-2 pb-2 border-b border-border mb-2">Publish to</p>
                        <form method="POST" action="{{ route('content-creator.publish') }}" x-on:submit="publishing = true; showPublish = false">
                            @csrf
                            <input type="hidden" name="content_id" value="{{ $item->id }}">
                            <input type="hidden" name="segment_index" value="0">
                            <input type="hidden" name="final_text" value="{{ $item->result }}">
                            <input type="hidden" name="image_url" value="{{ $item->options['image_url'] ?? '' }}">
                            <input type="hidden" name="scheduled_at" value="now">
                            
                            <div class="space-y-1.5 mb-2">
                                <label class="flex items-center gap-2 text-xs text-muted-foreground px-2">
                                    <input type="checkbox" name="platforms[]" value="facebook" class="rounded border-border">
                                    <i data-lucide="facebook" class="w-3 h-3"></i>
                                    Facebook
                                </label>
                                <label class="flex items-center gap-2 text-xs text-muted-foreground px-2">
                                    <input type="checkbox" name="platforms[]" value="instagram" class="rounded border-border">
                                    <i data-lucide="instagram" class="w-3 h-3"></i>
                                    Instagram
                                </label>
                                <label class="flex items-center gap-2 text-xs text-muted-foreground px-2">
                                    <input type="checkbox" name="platforms[]" value="linkedin" class="rounded border-border">
                                    <i data-lucide="linkedin" class="w-3 h-3"></i>
                                    LinkedIn
                                </label>
                            </div>
                            
                            <button type="submit" 
                                    :disabled="publishing"
                                    class="w-full py-1.5 px-3 bg-primary text-primary-foreground text-xs font-medium rounded-lg hover:bg-primary/90 transition-colors disabled:opacity-50">
                                <span x-show="!publishing">Publish Now</span>
                                <span x-show="publishing">Publishing...</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="text-center py-12 text-muted-foreground opacity-50 bg-card/50 rounded-xl border border-border border-dashed">
                    <i data-lucide="inbox" class="w-10 h-10 mx-auto mb-3"></i>
                    <p class="text-sm font-medium">No incoming drafts.</p>
                    <p class="text-xs mt-1">Send content via API to see it here.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
