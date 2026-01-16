{{-- Dashboard Knowledge Base Sidebar --}}
@props(['kbCount'])

<div class="lg:col-span-4 space-y-6">
    <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Knowledge Base</h3>
    <div class="bg-card border border-border rounded-[32px] p-8 h-full flex flex-col relative overflow-hidden">
        <div class="flex-1 space-y-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-muted border border-border flex items-center justify-center text-foreground">
                    <i data-lucide="database" class="w-6 h-6"></i>
                </div>
                <div>
                    <div class="text-2xl font-black">{{ $kbCount }}</div>
                    <div class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Documents Indexed</div>
                </div>
            </div>
            
            <div class="space-y-3">
                @if($kbCount > 0)
                    <div class="p-3 bg-muted/30 rounded-xl border border-border flex items-center gap-3">
                        <i data-lucide="file-check" class="w-4 h-4 text-green-500"></i>
                        <span class="text-xs font-medium text-slate-400">Context Active</span>
                    </div>
                @else
                    <div class="p-3 bg-yellow-500/10 rounded-xl border border-yellow-500/20 flex items-center gap-3">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-yellow-500"></i>
                        <span class="text-xs font-medium text-yellow-500">No context found. Upload docs to improve AI.</span>
                    </div>
                @endif
            </div>
        </div>
        
        <a href="/knowledge-base" class="mt-8 w-full h-12 bg-muted hover:bg-muted/80 border border-border rounded-xl font-bold uppercase text-[10px] tracking-widest flex items-center justify-center gap-2 transition-all">
            Manage Documents
        </a>
    </div>
</div>
