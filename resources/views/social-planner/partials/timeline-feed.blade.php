{{-- Scheduled Posts Timeline --}}
@php
    $platformColors = [
        'facebook' => 'blue-600',
        'instagram' => 'pink-600',
        'linkedin' => 'blue-800',
        'twitter' => 'sky-400',
        'generic' => 'slate-500'
    ];
@endphp

<div class="lg:col-span-2 space-y-6">
    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Active Deployment Timeline</h3>
    <div class="bg-card border border-border rounded-[40px] overflow-hidden shadow-sm">
        <div class="p-8 space-y-6 max-h-[700px] overflow-y-auto custom-scrollbar">
            @forelse($scheduledPosts as $post)
                @php
                    $options = $post->options ?? [];
                    $platform = $options['platform'] ?? 'generic';
                    $date = \Carbon\Carbon::parse($options['scheduled_at'] ?? $post->created_at);
                    $color = $platformColors[$platform] ?? 'slate-500';
                @endphp
                <div class="p-6 rounded-3xl border border-border bg-muted/5 group hover:border-primary/30 transition-all flex items-start gap-6 relative">
                    {{-- Platform Node --}}
                    <div class="w-12 h-12 rounded-2xl bg-{{ $color }}/10 flex items-center justify-center text-{{ $color }} shrink-0 border border-{{ $color }}/20">
                        <i data-lucide="{{ $platform === 'generic' ? 'share-2' : $platform }}" class="w-6 h-6"></i>
                    </div>

                    {{-- Content Core --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-3">
                                <h4 class="font-black text-sm uppercase tracking-tight text-foreground">{{ ucfirst($platform) }} Node</h4>
                                <span class="px-2 py-0.5 rounded-lg text-[8px] font-black uppercase tracking-[0.2em] border {{ $post->status === 'published' ? 'text-green-500 bg-green-500/5 border-green-500/20' : 'text-primary bg-primary/5 border-primary/20' }}">
                                    {{ $post->status }}
                                </span>
                            </div>
                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">{{ $date->format('M d • H:i') }}</span>
                        </div>
                        <p class="text-xs text-muted-foreground font-medium italic line-clamp-2 leading-relaxed mb-4">{{ $post->result }}</p>
                        
                        @if(!empty($options['image_url']))
                            <div class="w-32 aspect-video rounded-xl overflow-hidden border border-border shadow-sm group-hover:scale-[1.02] transition-transform">
                                <img src="{{ $options['image_url'] }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                    </div>

                    {{-- Industrial Actions --}}
                    <div class="flex flex-col gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        @if(!empty($options['original_content_id']))
                            <a href="{{ route('content-creator.show', $options['original_content_id']) }}" 
                               class="w-10 h-10 rounded-xl bg-white border border-border flex items-center justify-center text-primary shadow-sm hover:scale-110 transition-all"
                               title="View Source Content">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                        @endif
                        
                        <button @click="editPost('{{ $post->id }}')" 
                                class="w-10 h-10 rounded-xl bg-white border border-border flex items-center justify-center text-amber-500 shadow-sm hover:bg-amber-50 hover:text-amber-600 transition-all"
                                title="Edit Protocol">
                            <i data-lucide="pencil" class="w-4 h-4"></i>
                        </button>

                        <button @click="deletePost('{{ $post->id }}')" 
                                class="w-10 h-10 rounded-xl bg-red-50 border border-red-100 flex items-center justify-center text-red-500 shadow-sm hover:bg-red-600 hover:text-white transition-all"
                                title="Delete Protocol">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-20 text-center opacity-30 italic">
                    <i data-lucide="calendar-x" class="w-12 h-12 mx-auto mb-4"></i>
                    <p class="text-sm font-bold uppercase tracking-widest">Protocol timeline empty</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
