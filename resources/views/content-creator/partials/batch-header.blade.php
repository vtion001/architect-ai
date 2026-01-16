{{-- Batch Header Partial --}}
<div class="mb-6 flex items-center justify-between">
    <div>
        <a href="{{ route('content-creator.index') }}" class="text-sm text-muted-foreground hover:text-foreground flex items-center gap-1 mb-2">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Creator
        </a>
        <h1 class="text-3xl font-bold">{{ $content->title }}</h1>
        <p class="text-muted-foreground mt-1">{{ ucwords(str_replace('-', ' ', $content->type)) }} • {{ $content->created_at->format('M d, Y') }}</p>
    </div>
    <div class="flex gap-2 relative">
        <button @click="showDeleteModal = true" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 h-10 px-4 py-2 uppercase tracking-wider font-bold text-xs transition-colors">
            <i data-lucide="trash-2" class="w-4 h-4 mr-2"></i>
            Delete Batch
        </button>
        <button @click="copyAllPosts()" class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 uppercase tracking-wider font-bold text-xs transition-all hover:border-primary">
            <i data-lucide="copy" class="w-4 h-4 mr-2"></i>
            Copy All
        </button>
        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 uppercase tracking-wider font-bold text-xs shadow-lg shadow-primary/20">
            <i data-lucide="send" class="w-4 h-4 mr-2"></i>
            Publish All
        </button>
        
        {{-- Copy All Success Toast --}}
        <div x-show="showCopyAllToast" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-4"
             x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-x-0"
             x-transition:leave-end="opacity-0 translate-x-4"
             class="absolute -bottom-12 right-0 flex items-center gap-2 px-4 py-2.5 bg-green-500 text-white text-xs font-bold uppercase tracking-wider rounded-xl shadow-lg z-50">
            <i data-lucide="check-circle" class="w-4 h-4"></i>
            All posts copied to clipboard!
        </div>
    </div>
</div>
