{{-- Research Report - Main Content Display --}}
<div class="lg:col-span-9">
    <div class="bg-card border border-border rounded-[40px] shadow-sm relative overflow-hidden">
        <!-- Decorative Blueprint Header -->
        <div class="h-2 bg-gradient-to-r from-primary/40 via-primary to-primary/40"></div>
        
        <div class="p-12 md:p-20">
            <article class="prose-architect max-w-none">
                {!! Str::markdown($research->result ?? 'No intelligence data retrieved for this protocol session.') !!}
            </article>

            <!-- Dynamic Sources Footer -->
            <div id="sources-section" class="mt-16 pt-10 border-t-2 border-border/50" style="display: none;">
                <h3 class="text-xl font-black text-foreground uppercase tracking-tight mb-6 flex items-center gap-2">
                    <i data-lucide="link" class="w-5 h-5 text-primary"></i>
                    Verified Source Index
                </h3>
                <ul id="extracted-sources" class="grid grid-cols-1 gap-3 text-xs font-mono text-muted-foreground">
                    <!-- Populated by JS -->
                </ul>
            </div>
        </div>

        <!-- Footer Watermark -->
        <div class="p-10 border-t border-border bg-muted/10 flex justify-between items-center opacity-30 mono text-[8px] font-black uppercase tracking-[0.4em]">
            <span>ArchitGrid Intelligence Node v1.0.4</span>
            <span>System Encrypted: SHA-256</span>
        </div>
    </div>
</div>
