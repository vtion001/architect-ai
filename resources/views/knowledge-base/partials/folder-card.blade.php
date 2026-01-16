{{-- Knowledge Hub Folder Card --}}
@props(['asset'])

<a href="{{ route('knowledge-base.index', ['folder' => $asset->id]) }}" class="bg-card border border-border rounded-[32px] p-6 shadow-sm hover:border-primary/50 transition-all group flex items-center gap-4 relative overflow-hidden">
    <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity"></div>
    <div class="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-500 shrink-0">
        <i data-lucide="folder" class="w-7 h-7 fill-current"></i>
    </div>
    <div>
        <h3 class="text-lg font-black text-foreground uppercase tracking-tight group-hover:text-primary transition-colors">{{ $asset->title }}</h3>
        <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">Directory</p>
    </div>
    <div class="ml-auto flex items-center gap-2 relative z-10">
        <button @click.prevent="deleteAsset('{{ $asset->id }}')" class="opacity-0 group-hover:opacity-100 p-2 rounded-lg hover:bg-red-500/10 text-red-500 transition-all">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
        </button>
        <i data-lucide="chevron-right" class="w-5 h-5 text-muted-foreground group-hover:text-primary transition-colors"></i>
    </div>
</a>
