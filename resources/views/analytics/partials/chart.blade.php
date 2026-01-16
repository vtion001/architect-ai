{{-- Analytics - Consumption Chart --}}
<div class="lg:col-span-2 space-y-6">
    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Consumption Intensity (Last 7 Days)</h3>
    <div class="bg-card border border-border rounded-[40px] p-10 h-[400px] flex flex-col justify-between">
        <div class="flex-1 flex items-end gap-4">
            @foreach($intensityTrend as $idx => $val)
                <div class="flex-1 flex flex-col items-center gap-4 group">
                    <div class="w-full bg-muted/20 rounded-xl relative overflow-hidden transition-all group-hover:bg-primary/5 border border-transparent group-hover:border-primary/20" 
                         style="height: {{ max(10, ($val / max(1, max($intensityTrend))) * 100) }}%">
                        <div class="absolute inset-0 bg-gradient-to-t from-primary/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                    <span class="mono text-[9px] font-black text-slate-500 uppercase tracking-widest group-hover:text-primary transition-colors">{{ $labels[$idx] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
