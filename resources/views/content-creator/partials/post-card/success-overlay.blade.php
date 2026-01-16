{{-- Post Card Success Overlay - Shown After Publishing --}}
{{-- 
    Expects parent x-data context with:
    isPublished, publishResult
--}}
<div x-show="isPublished" x-transition.opacity class="absolute inset-0 z-40 bg-white/90 backdrop-blur-[2px] flex flex-col items-center justify-center p-6 text-center" style="display: none;">
    <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mb-4 scale-110">
        <i data-lucide="check-circle-2" class="w-10 h-10 text-green-600"></i>
    </div>
    <h3 class="text-xl font-bold text-foreground">Done!</h3>
    <p class="text-sm text-green-700 font-medium mb-6" x-text="publishResult"></p>
    <div class="flex gap-2">
        <button @click="isPublished = false" class="px-4 py-2 text-xs font-bold uppercase tracking-widest text-muted-foreground border border-border rounded-lg hover:bg-muted/50 transition-all">
            Edit / Re-publish
        </button>
        <a href="{{ route('social-planner.index') }}" class="px-4 py-2 text-xs font-bold uppercase tracking-widest bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-all shadow-md">
            View in Planner
        </a>
    </div>
</div>
