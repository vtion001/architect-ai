{{-- Post Card Media Area - Image/Video Display & Upload Options --}}
{{-- 
    Expects parent x-data context with:
    imageUrl, isVideo(), showMediaOptions, isGenerating, isUploading,
    triggerUpload(), openImageCreator()
--}}
<div class="px-4 pb-4">
    {{-- Image Display State --}}
    <div x-show="imageUrl" class="relative w-full h-auto rounded-lg overflow-hidden border border-border group min-h-[200px]" x-transition>
        <template x-if="isVideo(imageUrl)">
            <video :src="imageUrl" controls class="w-full h-auto object-cover max-h-[500px]" x-ref="postVideo"></video>
        </template>
        <template x-if="!isVideo(imageUrl)">
            <img 
                :src="imageUrl" 
                class="w-full h-auto object-cover max-h-[500px]" 
                alt="Post Media"
                x-ref="postImage"
                @error="$el.style.display='none'; $refs.imageErrorFallback.style.display='flex'"
            >
        </template>
        {{-- Expired Image Fallback --}}
        <div x-ref="imageErrorFallback" class="hidden w-full h-48 bg-gradient-to-br from-slate-800 to-slate-900 items-center justify-center flex-col gap-3 rounded-lg border border-red-500/20">
            <i data-lucide="image-off" class="w-10 h-10 text-red-400/50"></i>
            <p class="text-[10px] font-black uppercase text-red-400/70 tracking-widest">Image Expired</p>
            <button @click="showMediaOptions = true" class="px-4 py-2 bg-primary/10 hover:bg-primary/20 text-primary text-xs font-bold rounded-lg transition-colors">
                Regenerate Visual
            </button>
        </div>
        {{-- Hover Overlay to Remove/Replace --}}
        <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
            <button @click="imageUrl = null" class="p-2 bg-white/10 hover:bg-white/20 text-white rounded-full backdrop-blur-md transition-colors" title="Remove Image">
                <i data-lucide="trash-2" class="w-5 h-5"></i>
            </button>
            <button @click="showMediaOptions = true" class="p-2 bg-white/10 hover:bg-white/20 text-white rounded-full backdrop-blur-md transition-colors" title="Replace Image">
                <i data-lucide="refresh-cw" class="w-5 h-5"></i>
            </button>
        </div>
    </div>

    {{-- Placeholder State --}}
    <div x-show="!imageUrl" class="relative w-full h-56 rounded-lg bg-muted/20 border-2 border-dashed border-border/50 overflow-hidden group">
        
        {{-- Default State: Add Visuals Prompt --}}
        <div @click="showMediaOptions = true" x-show="!showMediaOptions && !isGenerating && !isUploading" class="absolute inset-0 flex flex-col items-center justify-center gap-2 text-muted-foreground/50 transition-colors hover:bg-muted/30 hover:text-primary/70 cursor-pointer">
            <div class="w-12 h-12 rounded-full bg-background/50 flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm">
               <i data-lucide="image-plus" class="w-6 h-6"></i>
            </div>
            <span class="text-xs font-bold uppercase tracking-widest">Add Visuals</span>
        </div>
        
        {{-- Loading State --}}
        <div x-show="isGenerating || isUploading" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-muted/10 z-20" style="display: none;">
           <i data-lucide="loader-2" class="w-8 h-8 text-primary animate-spin"></i>
           <span class="text-xs font-bold uppercase tracking-widest text-primary" x-text="isGenerating ? 'Designing...' : 'Uploading...'"></span>
        </div>

        {{-- Active State: Media Options --}}
        <div x-show="showMediaOptions && !isGenerating && !isUploading" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             class="absolute inset-0 bg-background/95 backdrop-blur-sm z-10 flex flex-col items-center justify-center p-6 gap-3"
             style="display: none;">
           
           <h5 class="text-sm font-semibold text-foreground mb-1">Select Media Source</h5>
           
           <div class="flex items-center gap-3 w-full max-w-xs">
                <button @click="triggerUpload" class="flex-1 flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-border bg-card hover:border-primary/50 hover:bg-primary/5 transition-all group/btn">
                   <i data-lucide="paperclip" class="w-5 h-5 text-muted-foreground group-hover/btn:text-primary"></i>
                   <span class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground group-hover/btn:text-primary">Upload Photo / Video</span>
                </button>
                <button @click="openImageCreator" class="flex-1 flex flex-col items-center justify-center gap-2 p-4 rounded-xl border border-border bg-card hover:border-purple-500/50 hover:bg-purple-500/5 transition-all group/btn">
                   <i data-lucide="sparkles" class="w-5 h-5 text-purple-500"></i>
                   <span class="text-[10px] font-bold uppercase tracking-wider text-muted-foreground group-hover/btn:text-purple-600">Banana Pro Studio</span>
                </button>
           </div>

           <button @click="showMediaOptions = false" class="absolute top-2 right-2 p-2 text-muted-foreground hover:text-foreground">
               <i data-lucide="x" class="w-4 h-4"></i>
           </button>
        </div>
    </div>
</div>
