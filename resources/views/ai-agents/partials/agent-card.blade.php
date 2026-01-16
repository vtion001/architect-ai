{{-- AI Agent Card --}}
@props(['agent', 'knowledgeAssets'])

<div class="bg-card border border-border rounded-[40px] p-8 shadow-sm hover:border-primary/30 transition-all group relative overflow-hidden flex flex-col">
    {{-- Status Badge --}}
    <div class="absolute top-6 right-6">
        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest {{ $agent->is_active ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-100 text-slate-500' }}">
            {{ $agent->is_active ? 'Active' : 'Inactive' }}
        </span>
    </div>
    
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="w-14 h-14 rounded-2xl border border-primary/10 flex items-center justify-center shadow-inner overflow-hidden"
             style="background: linear-gradient(135deg, {{ $agent->primary_color ?? '#00F2FF' }}30, {{ $agent->primary_color ?? '#00F2FF' }}10);">
            @if($agent->avatar_url)
                <img src="{{ $agent->avatar_url }}" alt="{{ $agent->name }}" class="w-14 h-14 object-cover">
            @else
                <i data-lucide="cpu" class="w-7 h-7" style="color: {{ $agent->primary_color ?? '#00F2FF' }}"></i>
            @endif
        </div>
        <div>
            <h3 class="text-lg font-black text-foreground uppercase tracking-tight">{{ $agent->name }}</h3>
            <p class="text-[10px] font-bold uppercase tracking-widest" style="color: {{ $agent->primary_color ?? '#00F2FF' }}">{{ $agent->role }}</p>
        </div>
    </div>

    {{-- Goal --}}
    <div class="bg-muted/30 rounded-2xl p-5 mb-6 flex-1 border border-border/50">
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Primary Objective</p>
        <p class="text-xs text-muted-foreground font-medium leading-relaxed italic">
            "{{ Str::limit($agent->goal, 120) }}"
        </p>
    </div>

    {{-- Knowledge Sources --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 mb-2">
            <i data-lucide="database" class="w-3 h-3 text-slate-400"></i>
            <span class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Grounding Sources</span>
        </div>
        <div class="flex flex-wrap gap-1">
            @forelse(collect($agent->knowledge_sources)->take(3) as $sourceId)
                @php $source = $knowledgeAssets->find($sourceId); @endphp
                @if($source)
                    <span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-[8px] font-bold text-slate-600 uppercase tracking-tight truncate max-w-[100px]">
                        {{ $source->title }}
                    </span>
                @endif
            @empty
                <span class="text-[9px] text-slate-400 italic">No specific sources linked</span>
            @endforelse
            @if(count($agent->knowledge_sources ?? []) > 3)
                <span class="px-2 py-0.5 text-[8px] text-slate-400 font-bold">+{{ count($agent->knowledge_sources) - 3 }} more</span>
            @endif
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex gap-3 pt-6 border-t border-border/50">
        <button @click="$store.aiChat.openWithAgent(@js($agent))" 
                class="flex-1 h-12 rounded-xl text-white font-black uppercase text-[10px] tracking-widest shadow-lg hover:scale-[1.02] transition-all flex items-center justify-center gap-2"
                style="background-color: {{ $agent->primary_color ?? '#00F2FF' }}; box-shadow: 0 8px 20px {{ $agent->primary_color ?? '#00F2FF' }}40;">
            <i data-lucide="message-square" class="w-4 h-4"></i>
            Open Console
        </button>
        <div class="flex gap-2">
            <button @click="editAgent(@js($agent))" class="w-12 h-12 rounded-xl border border-border bg-card flex items-center justify-center hover:bg-muted transition-all text-slate-500" title="Reconfigure">
                <i data-lucide="settings" class="w-4 h-4"></i>
            </button>
            <button @click="deleteAgent('{{ $agent->id }}')" class="w-12 h-12 rounded-xl border border-red-200 bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all" title="Decommission">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
</div>
