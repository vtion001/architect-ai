{{-- Platform Connection Nodes Grid --}}
@php
    $platformColors = [
        'facebook' => 'blue-600',
        'instagram' => 'pink-600',
        'linkedin' => 'blue-800',
        'twitter' => 'sky-400'
    ];
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    @foreach($socialConfig as $platform => $config)
        @php
            $color = $platformColors[$platform] ?? 'slate-500';
        @endphp
        <div class="bg-card border border-border rounded-[32px] p-6 shadow-sm relative overflow-hidden group hover:border-primary/30 transition-all">
            <div class="flex items-center justify-between mb-6">
                <div class="w-12 h-12 rounded-2xl bg-{{ $color }}/10 flex items-center justify-center text-{{ $color }}">
                    <i data-lucide="{{ $platform }}" class="w-6 h-6"></i>
                </div>
                @if($config['connected'])
                    <div class="flex items-center gap-1.5 px-2 py-1 rounded-lg bg-green-500/10 text-green-500 border border-green-500/20 animate-in fade-in zoom-in-95">
                        <span class="w-1 h-1 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-[8px] font-black uppercase tracking-widest">Active</span>
                    </div>
                @else
                    <span class="text-[8px] font-black uppercase tracking-widest text-slate-500">Offline</span>
                @endif
            </div>

            <h3 class="text-xl font-black text-foreground uppercase tracking-tight">{{ ucfirst($platform) }}</h3>
            <div class="flex items-center justify-between mt-2">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ $config['count'] }} Registry Hits</p>
                <span class="text-[10px] font-black text-primary">{{ $config['percentage'] }}%</span>
            </div>

            <div class="absolute -right-4 -bottom-4 opacity-5 group-hover:scale-110 transition-transform">
                <i data-lucide="{{ $platform }}" class="w-24 h-24 text-{{ $color }}"></i>
            </div>
        </div>
    @endforeach
</div>
