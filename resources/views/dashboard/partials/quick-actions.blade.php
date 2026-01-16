{{-- Dashboard Quick Actions --}}
<div class="lg:col-span-8 space-y-6">
    <div class="flex items-center justify-between px-1">
        <h3 class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Quick Actions</h3>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Action 1: Define Brand --}}
        <a href="/settings/brands" class="group relative bg-card hover:bg-muted/50 border border-border rounded-[32px] p-8 transition-all overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-500">
                <i data-lucide="fingerprint" class="w-32 h-32 text-primary"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-6">
                    <i data-lucide="scan-face" class="w-6 h-6"></i>
                </div>
                <h4 class="text-xl font-black uppercase tracking-tight mb-2">Define Brand DNA</h4>
                <p class="text-xs text-muted-foreground font-medium leading-relaxed mb-6">
                    Upload a website or PDF to extract tone, values, and audience profiles.
                </p>
                <span class="text-[10px] font-bold uppercase tracking-widest text-primary flex items-center gap-2">
                    Start Analysis <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </span>
            </div>
        </a>

        {{-- Action 2: Research --}}
        <a href="/research-engine" class="group relative bg-card hover:bg-muted/50 border border-border rounded-[32px] p-8 transition-all overflow-hidden">
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity transform group-hover:scale-110 duration-500">
                <i data-lucide="globe" class="w-32 h-32 text-blue-500"></i>
            </div>
            <div class="relative z-10">
                <div class="w-12 h-12 rounded-2xl bg-blue-500/10 flex items-center justify-center text-blue-500 mb-6">
                    <i data-lucide="search" class="w-6 h-6"></i>
                </div>
                <h4 class="text-xl font-black uppercase tracking-tight mb-2">Deep Research</h4>
                <p class="text-xs text-muted-foreground font-medium leading-relaxed mb-6">
                    Analyze competitors or topics to find trending angles for your content.
                </p>
                <span class="text-[10px] font-bold uppercase tracking-widest text-blue-500 flex items-center gap-2">
                    Begin Research <i data-lucide="arrow-right" class="w-3 h-3"></i>
                </span>
            </div>
        </a>
    </div>
</div>
