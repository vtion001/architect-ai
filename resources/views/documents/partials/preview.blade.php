{{-- Document Viewer - Content Preview --}}
<div class="flex flex-col items-center justify-start min-h-[900px] relative">
    <div 
        class="shadow-2xl bg-white ring-1 ring-slate-900/10 overflow-hidden origin-top transition-all duration-300 rounded-sm relative" 
        :class="isEditing ? 'ring-primary/40 ring-4 ring-offset-8' : ''"
        :style="`width: 210mm; min-height: 297mm; transform: scale(${zoomLevel});`"
    >
        <iframe 
            x-ref="previewFrame"
            :srcdoc="content" 
            class="w-full border-none" 
            style="height: 297mm;" 
            sandbox="allow-same-origin allow-scripts"
        ></iframe>

        {{-- Empty/Loading State --}}
        <div x-show="!content || content.trim() === ''" class="absolute inset-0 flex items-center justify-center bg-white z-10">
            <div class="text-center p-10">
                <i data-lucide="file-clock" class="w-12 h-12 text-slate-300 mx-auto mb-4"></i>
                <h3 class="text-lg font-bold text-slate-700">Document Content Unavailable</h3>
                <p class="text-sm text-slate-500 mt-2 max-w-xs mx-auto">This document is either still generating or has no content. Please check back later.</p>
            </div>
        </div>
    </div>

    <template x-if="isEditing">
        <div class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-black/80 backdrop-blur-md text-white px-6 py-3 rounded-full text-[10px] font-black uppercase tracking-widest z-[100] animate-bounce shadow-2xl border border-white/10">
            Editing Active: Type directly into the document preview above
        </div>
    </template>
</div>
