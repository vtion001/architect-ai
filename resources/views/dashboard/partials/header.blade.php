{{-- 
    Dashboard Welcome Header
    
    LCP Optimization: This header contains the LCP element (h1).
    Content is rendered immediately without waiting for Alpine.js.
--}}
<div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8">
    <div>
        {{-- LCP Element - Rendered immediately, no JS dependency --}}
        <h1 class="text-4xl font-black uppercase tracking-tighter text-foreground mb-2">
            Command Center
        </h1>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">System Online</span>
            </div>
            {{-- Brand Context Indicator --}}
            <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-muted/50 border border-border">
                {{-- Use inline SVG for faster rendering (no Lucide dependency) --}}
                <svg class="w-3 h-3 text-primary" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4"/><path d="M14 13.12c0 2.38 0 6.38-1 8.88"/><path d="M17.29 21.02c.12-.6.43-2.3.5-3.02"/><path d="M2 12a10 10 0 0 1 18-6"/><path d="M2 16h.01"/><path d="M21.8 16c.2-2 .131-5.354 0-6"/><path d="M5 19.5C5.5 18 6 15 6 12a6 6 0 0 1 .34-2"/><path d="M8.65 22c.21-.66.45-1.32.57-2"/><path d="M9 6.8a6 6 0 0 1 9 5.2v2"/>
                </svg>
                <span class="text-[10px] font-mono text-foreground uppercase tracking-tight">
                    Active Workspace: <span class="text-primary font-bold">{{ app(\App\Models\Tenant::class)->slug }}</span>
                </span>
            </div>
        </div>
    </div>
    <div class="flex gap-4">
        {{-- Quick Action: New Brand --}}
        <a href="/settings/brands" 
           class="h-14 px-6 rounded-2xl border border-border bg-card font-bold uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all"
           rel="prefetch">
            {{-- Inline SVG for faster rendering --}}
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M5 12h14"/><path d="M12 5v14"/>
            </svg>
            Add Brand DNA
        </a>
        <a href="/content-creator" 
           class="h-14 px-8 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] flex items-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all"
           rel="prefetch">
            {{-- Inline SVG for faster rendering --}}
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 14a1 1 0 0 1-.78-1.63l9.9-10.2a.5.5 0 0 1 .86.46l-1.92 6.02A1 1 0 0 0 13 10h7a1 1 0 0 1 .78 1.63l-9.9 10.2a.5.5 0 0 1-.86-.46l1.92-6.02A1 1 0 0 0 11 14z"/>
            </svg>
            Create Content
        </a>
    </div>
</div>

