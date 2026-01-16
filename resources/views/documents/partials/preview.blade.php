{{-- Document Viewer - Content Preview --}}
<div class="flex flex-col items-center justify-start min-h-[900px] relative">
    <div 
        class="shadow-2xl bg-white ring-1 ring-slate-900/10 overflow-hidden origin-top transition-all duration-300 rounded-sm" 
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
    </div>

    <template x-if="isEditing">
        <div class="fixed bottom-10 left-1/2 -translate-x-1/2 bg-black/80 backdrop-blur-md text-white px-6 py-3 rounded-full text-[10px] font-black uppercase tracking-widest z-[100] animate-bounce shadow-2xl border border-white/10">
            Editing Active: Type directly into the document preview above
        </div>
    </template>
</div>
