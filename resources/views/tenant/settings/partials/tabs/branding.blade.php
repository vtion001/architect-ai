{{-- Visual DNA (Branding) Tab --}}
@props(['tenant'])

<div x-show="activeTab === 'branding'" class="bg-card border border-border rounded-[40px] p-10 shadow-sm animate-in fade-in duration-300">
    <div class="flex items-center justify-between mb-10">
        <h2 class="text-2xl font-black uppercase tracking-tighter">Visual DNA</h2>
        <div class="flex items-center gap-3 px-4 py-2 rounded-xl border border-border bg-muted/30">
            <div class="w-4 h-4 rounded-full" :style="'background-color: ' + tempColor"></div>
            <span class="text-[10px] font-black uppercase text-slate-500 tracking-widest">Active Preview</span>
        </div>
    </div>

    <form action="{{ route('settings.branding') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
        @csrf
        <div class="space-y-8">
            <div class="space-y-2">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Workspace Label</label>
                <input type="text" name="name" value="{{ $tenant->name }}" required
                       class="w-full h-14 bg-muted/20 border border-border rounded-2xl px-5 text-lg font-black uppercase tracking-tight focus:ring-2 focus:ring-primary/20 outline-none">
            </div>

            {{-- Logo Node --}}
            <div class="p-8 rounded-[32px] border border-border bg-muted/5">
                <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic mb-6 block px-1">Corporate Identity Asset (Logo)</label>
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="w-32 h-32 rounded-3xl bg-white border border-border flex items-center justify-center p-4 shadow-inner relative group">
                        @if($tenant->metadata['logo_url'] ?? false)
                            <img src="{{ $tenant->metadata['logo_url'] }}" class="max-w-full max-h-full object-contain">
                        @else
                            <img src="https://res.cloudinary.com/dbviya1rj/image/upload/v1767554289/xe54y8zsvhursjrpbnvm.png" class="max-w-full max-h-full object-contain opacity-20 grayscale">
                        @endif
                    </div>
                    <div class="flex-1 space-y-4">
                        <p class="text-xs font-medium text-muted-foreground italic leading-relaxed">
                            Upload your agency's master logo. This will replace the default grid branding across all personnel nodes and sub-account sidebars.
                        </p>
                        <input type="file" name="logo" id="logo-upload" class="hidden" accept="image/*">
                        <button type="button" onclick="document.getElementById('logo-upload').click()" class="h-12 px-8 bg-muted border border-border rounded-xl font-black uppercase text-[9px] tracking-widest hover:bg-white hover:text-black transition-all">Select Node Asset</button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Primary Grid Color</label>
                    <div class="flex gap-4">
                        <input type="color" x-model="tempColor" name="metadata[primary_color]"
                               class="w-20 h-14 bg-muted/20 border border-border rounded-2xl p-1 cursor-pointer">
                        <input type="text" x-model="tempColor" class="flex-1 h-14 bg-muted/20 border border-border rounded-2xl px-5 mono text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                    <p class="text-[9px] text-muted-foreground mt-2 italic">This color will drive the primary accents, sidebars, and active states across your grid nodes.</p>
                </div>
                
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 italic px-1">Custom Domain</label>
                    <div class="relative">
                        <i data-lucide="globe" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                        <input type="text" name="metadata[custom_domain]" value="{{ $tenant->metadata['custom_domain'] ?? '' }}" placeholder="grid.youragency.com"
                               class="w-full h-14 bg-muted/20 border border-border rounded-2xl pl-11 pr-5 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                    <p class="text-[9px] text-muted-foreground mt-2 italic">Whitelist your agency domain for a seamless white-label experience.</p>
                </div>
            </div>
        </div>

        <div class="pt-6 border-t border-border">
            <button type="submit" class="h-14 px-10 bg-primary text-primary-foreground rounded-2xl font-black uppercase text-[10px] tracking-widest shadow-lg shadow-primary/20 transition-all hover:scale-[1.02]">Persist Visual DNA</button>
        </div>
    </form>
</div>
