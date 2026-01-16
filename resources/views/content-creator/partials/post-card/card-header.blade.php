{{-- Post Card Header - Avatar, Metadata, Status --}}
{{-- 
    Expects parent x-data context with: isPublished
    Expects $content variable from parent scope
--}}
<div class="p-4 flex items-center justify-between border-b border-border/50">
    <div class="flex items-center gap-3">
        {{-- Avatar --}}
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center border border-primary/10 shadow-inner">
            <i data-lucide="bot" class="w-5 h-5 text-primary"></i>
        </div>
        <div>
            <h4 class="text-sm font-bold text-foreground leading-tight">ArchitGrid</h4>
            <div class="flex items-center gap-1.5 text-[10px] text-muted-foreground font-medium">
                <span class="text-primary font-bold">Recommended for you</span>
                <span class="text-muted-foreground/50">•</span>
                <span>{{ $content->created_at->diffForHumans() }}</span>
                <span class="text-muted-foreground/50">•</span>
                <i data-lucide="globe" class="w-3 h-3 opacity-70"></i>
            </div>
        </div>
    </div>

    {{-- Status Badge --}}
    <div class="flex items-center">
        <div :class="isPublished ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-50 text-red-600 border-red-100'" 
             class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider transition-colors">
            <div :class="isPublished ? 'bg-green-600' : 'bg-red-600'" class="w-1.5 h-1.5 rounded-full mr-1.5"></div>
            <span x-text="isPublished ? 'Published' : 'Unpublished'"></span>
        </div>
    </div>
</div>
