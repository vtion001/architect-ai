{{-- Blog Article Layout - Dedicated single-post layout for long-form blog content --}}
{{--
    Expects parent x-data context from postCard():
    isEditing, rawContent, htmlContent, showCopyToast, copyContent(),
    toggleEdit(), regenerateText(), openPublishModal(),
    imageUrl, showMediaOptions, openImageCreator(),
    isRegenerating, isPublishing, isUploading, isGenerating,
    triggerUpload(), handleUpload(), isVideo(),
    showPublishModal, selectedPlatforms, confirmPublish(),
    showImageCreatorModal, imageFormat, imagePrompt, posterText,
    selectedAssetUrl, selectedBrandId, mediaAssets, isLoadingAssets,
    loadMediaAssets(), generateAdvancedImage(),
    fetchFacebookPages(), fetchLinkedinPages(), fetchTwitterPages()
--}}

{{-- Reading Progress Bar --}}
<div x-data="{ progress: 0 }"
     x-init="window.addEventListener('scroll', () => {
         let scrollHeight = document.documentElement.scrollHeight - window.innerHeight;
         let scrolled = window.scrollY;
         $el.style.width = (scrolled / (scrollHeight || 1) * 100) + '%';
     }, { passive: true })"
     class="sticky top-0 z-50 h-1 bg-border/30">
    <div class="h-full bg-gradient-to-r from-primary to-purple-500 transition-all duration-150 ease-out"
         style="width: 0%"></div>
</div>

<div class="max-w-4xl mx-auto" x-data="postCard(0)">
    {{-- Sticky Action Toolbar --}}
    <div class="sticky top-1 z-40 flex items-center justify-between px-4 py-2 mt-4 mb-6 rounded-xl bg-background/90 backdrop-blur-md border border-border/50 shadow-sm">
        <div class="flex items-center gap-2">
            {{-- Back to Content --}}
            <a href="{{ route('content-creator.index') }}" class="flex items-center gap-2 py-2 px-3 rounded-lg bg-white border border-border text-muted-foreground hover:text-foreground hover:border-foreground/30 transition-all text-xs font-bold uppercase tracking-wider">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span class="hidden sm:inline">Back</span>
            </a>

            {{-- Content Type Badge --}}
            <span class="hidden sm:inline-flex items-center gap-1.5 py-2 px-3 rounded-lg bg-primary/10 text-primary border border-primary/20 text-xs font-bold uppercase tracking-wider">
                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                Blog Article
            </span>
        </div>

        <div class="flex items-center gap-2">
            {{-- Copy Button --}}
            <button @click="copyContent()"
                    class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-white border border-border text-muted-foreground hover:text-primary hover:border-primary/30 transition-all text-xs font-bold uppercase tracking-wider"
                    title="Copy to clipboard">
                <i x-show="!showCopyToast" data-lucide="copy" class="w-4 h-4"></i>
                <i x-show="showCopyToast" data-lucide="check" class="w-4 h-4 text-green-500"></i>
                <span class="hidden sm:inline" x-text="showCopyToast ? 'Copied!' : 'Copy'"></span>
            </button>

            {{-- Edit Button --}}
            <button @click="toggleEdit()"
                    class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-white border border-border text-muted-foreground hover:text-primary hover:border-primary/30 transition-all text-xs font-bold uppercase tracking-wider"
                    title="Edit Content">
                <i x-show="!isEditing" data-lucide="pencil" class="w-4 h-4"></i>
                <i x-show="isEditing" data-lucide="check" class="w-4 h-4"></i>
                <span class="hidden sm:inline" x-text="isEditing ? 'Done' : 'Edit'"></span>
            </button>

            {{-- Redo Button --}}
            <button @click="regenerateText()"
                    :disabled="isRegenerating"
                    class="flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-white border border-border text-muted-foreground hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-all text-xs font-bold uppercase tracking-wider disabled:opacity-50 disabled:cursor-not-allowed"
                    title="Regenerate Text">
                <i x-show="!isRegenerating" data-lucide="refresh-cw" class="w-4 h-4"></i>
                <i x-show="isRegenerating" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                <span class="hidden sm:inline" x-text="isRegenerating ? 'Redoing...' : 'Redo'"></span>
            </button>

            {{-- Publish Button --}}
            <button @click="openPublishModal()"
                    :disabled="isPublishing"
                    class="flex items-center justify-center gap-2 py-2 px-4 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-all text-xs font-bold uppercase tracking-wider shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                <i x-show="!isPublishing" data-lucide="send" class="w-4 h-4"></i>
                <i x-show="isPublishing" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                <span x-text="isPublishing ? 'Publishing...' : 'Publish'"></span>
            </button>
        </div>
    </div>

    {{-- Success Published Banner --}}
    @include('content-creator.partials.post-card.success-overlay')

    {{-- Hidden File Input --}}
    <input type="file" x-ref="fileInput" class="hidden" accept="image/*,video/*" @change="handleUpload">

    {{-- Copy Success Toast (centered) --}}
    <div x-show="showCopyToast"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="fixed bottom-8 left-1/2 -translate-x-1/2 flex items-center gap-2 px-4 py-2.5 bg-green-500 text-white text-sm font-bold uppercase tracking-wider rounded-full shadow-2xl z-50">
        <i data-lucide="check" class="w-4 h-4"></i>
        Copied to clipboard!
    </div>

    {{-- Edit Mode --}}
    <div x-show="isEditing" class="max-w-4xl mx-auto px-4 mb-12">
        <div class="flex items-center justify-between mb-4">
            <span class="text-sm font-bold text-muted-foreground uppercase tracking-wider">Editing Article</span>
            <span class="text-xs text-muted-foreground">Changes auto-save to preview</span>
        </div>
        <textarea x-model="rawContent"
                  class="w-full p-6 bg-muted/20 border-2 border-primary/20 rounded-xl text-sm focus:ring-2 focus:ring-primary focus:border-primary outline-none resize-y font-mono min-h-[600px] text-foreground leading-relaxed"
                  placeholder="Edit your article content..."></textarea>
    </div>

    {{-- Article Display Mode --}}
    <article x-show="!isEditing" class="max-w-4xl mx-auto px-4 pb-20">

        {{-- Hero Image --}}
        <div x-show="imageUrl" class="relative w-full mb-10 -mx-4 rounded-2xl overflow-hidden border border-border shadow-xl">
            <template x-if="isVideo(imageUrl)">
                <video :src="imageUrl" controls class="w-full max-h-[520px] object-cover" x-ref="postVideo"></video>
            </template>
            <template x-if="!isVideo(imageUrl)">
                <img :src="imageUrl" class="w-full max-h-[520px] object-cover" alt="Article Featured Image" x-ref="postImage" x-on:error="$el.style.display='none'; $refs.imageErrorFallback.style.display='flex'">
            </template>
            <div x-ref="imageErrorFallback" class="hidden w-full h-48 bg-gradient-to-br from-slate-800 to-slate-900 items-center justify-center flex-col gap-3 rounded-lg border border-red-500/20">
                <i data-lucide="image-off" class="w-10 h-10 text-red-400/50"></i>
                <p class="text-[10px] font-black uppercase text-red-400/70 tracking-widest">Image Expired</p>
                <button @click="showMediaOptions = true" class="px-4 py-2 bg-primary/10 hover:bg-primary/20 text-primary text-xs font-bold rounded-lg transition-colors">Regenerate Visual</button>
            </div>
            <div class="absolute inset-0 bg-black/30 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                <button @click="imageUrl = null" class="p-3 bg-white/10 hover:bg-white/20 text-white rounded-full backdrop-blur-md transition-colors" title="Remove Image"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                <button @click="showMediaOptions = true" class="p-3 bg-white/10 hover:bg-white/20 text-white rounded-full backdrop-blur-md transition-colors" title="Replace Image"><i data-lucide="refresh-cw" class="w-5 h-5"></i></button>
            </div>
        </div>

        {{-- No Image Placeholder --}}
        <div x-show="!imageUrl" class="relative w-full h-48 mb-10 rounded-2xl bg-gradient-to-br from-muted/30 to-muted/10 border-2 border-dashed border-border/40 overflow-hidden group">
            <div @click="showMediaOptions = true" x-show="!showMediaOptions && !isGenerating && !isUploading" class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-muted-foreground/40 transition-colors hover:bg-muted/20 hover:text-primary/70 cursor-pointer">
                <div class="w-14 h-14 rounded-full bg-background/50 flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm"><i data-lucide="image-plus" class="w-7 h-7"></i></div>
                <span class="text-sm font-bold uppercase tracking-widest">Add Featured Image</span>
                <span class="text-xs opacity-70">Recommended for blog articles</span>
            </div>
            <div x-show="isGenerating || isUploading" class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-muted/10 z-20" style="display: none;">
                <i data-lucide="loader-2" class="w-8 h-8 text-primary animate-spin"></i>
                <span class="text-sm font-bold uppercase tracking-widest text-primary" x-text="isGenerating ? 'Designing...' : 'Uploading...'"></span>
            </div>
            <div x-show="showMediaOptions && !isGenerating && !isUploading" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute inset-0 bg-background/95 backdrop-blur-sm z-10 flex flex-col items-center justify-center p-6 gap-4" style="display: none;">
                <h5 class="text-base font-semibold text-foreground mb-1">Add Featured Image</h5>
                <div class="flex items-center gap-4 w-full max-w-md">
                    <button @click="triggerUpload" class="flex-1 flex flex-col items-center justify-center gap-3 p-6 rounded-xl border border-border bg-card hover:border-primary/50 hover:bg-primary/5 transition-all group/btn">
                        <i data-lucide="upload" class="w-7 h-7 text-muted-foreground group-hover/btn:text-primary"></i>
                        <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground group-hover/btn:text-primary">Upload Photo</span>
                    </button>
                    <div class="relative flex-1">
                        <div class="absolute inset-0 border-l border-border"></div>
                        <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-background px-2 text-xs text-muted-foreground font-bold">or</span>
                    </div>
                    <button @click="openImageCreator" class="flex-1 flex flex-col items-center justify-center gap-3 p-6 rounded-xl border border-border bg-card hover:border-purple-500/50 hover:bg-purple-500/5 transition-all group/btn">
                        <i data-lucide="sparkles" class="w-7 h-7 text-purple-500"></i>
                        <span class="text-xs font-bold uppercase tracking-wider text-muted-foreground group-hover/btn:text-purple-600">AI Generate</span>
                    </button>
                </div>
                <button @click="showMediaOptions = false" class="absolute top-3 right-3 p-2 text-muted-foreground hover:text-foreground"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
        </div>

        {{-- Article Header --}}
        <header class="mb-10">
            {{-- Meta Description --}}
            @if($blogMetaDescription)
            <p class="text-base text-muted-foreground italic mb-4 leading-relaxed max-w-3xl">{{ $blogMetaDescription }}</p>
            @endif

            {{-- H1 Title --}}
            <h1 class="text-4xl md:text-5xl font-black text-foreground leading-tight tracking-tight mb-4">{{ $blogTitle ?: 'Untitled Article' }}</h1>

            {{-- Article Meta --}}
            <div class="flex items-center gap-4 text-sm text-muted-foreground flex-wrap">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center border border-primary/10">
                        <i data-lucide="bot" class="w-4 h-4 text-primary"></i>
                    </div>
                    <span class="font-semibold text-foreground">ArchitGrid</span>
                </div>
                <span class="text-muted-foreground/40">•</span>
                <span>{{ $content->created_at->format('F j, Y') }}</span>
                <span class="text-muted-foreground/40">•</span>
                <div class="flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                    <span x-text="(() => {
                        let text = document.createElement('div');
                        text.innerHTML = window.__contentViewerConfig?.blog?.bodyHtml ?? '';
                        let words = text.textContent?.split(/\s+/).filter(w => w.length > 0).length ?? 0;
                        let mins = Math.ceil(words / 200);
                        return mins + ' min read';
                    })()"></span>
                </div>
                <span class="text-muted-foreground/40">•</span>
                <div :class="isPublished ? 'text-green-600' : 'text-amber-600'" class="flex items-center gap-1.5">
                    <div :class="isPublished ? 'bg-green-600' : 'bg-amber-600'" class="w-2 h-2 rounded-full"></div>
                    <span x-text="isPublished ? 'Published' : 'Draft'"></span>
                </div>
            </div>
        </header>

        {{-- Article Body - Enhanced Typography --}}
        <div class="prose-custom-blog" x-html="window.__contentViewerConfig?.blog?.bodyHtml ?? ''"></div>

        {{-- Article Footer - Compact Image Controls --}}
        <div class="mt-16 pt-8 border-t border-border/50">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <i data-lucide="image" class="w-5 h-5 text-muted-foreground/50"></i>
                    <span class="text-sm text-muted-foreground font-medium" x-text="imageUrl ? 'Featured image attached' : 'No featured image'"></span>
                </div>
                <button @click="showMediaOptions = true" class="flex items-center gap-2 py-2 px-4 rounded-lg border border-border bg-white text-muted-foreground hover:text-primary hover:border-primary/30 hover:bg-primary/5 transition-all text-xs font-bold uppercase tracking-wider">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span x-text="imageUrl ? 'Change Image' : 'Add Featured Image'"></span>
                </button>
            </div>
        </div>
    </article>

    {{-- Publish Modal --}}
    @include('content-creator.partials.post-card.modals.publish-modal')

    {{-- Image Creator Modal --}}
    @include('content-creator.partials.post-card.modals.image-creator-modal')
</div>
