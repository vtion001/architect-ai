{{-- Preview Tab Content --}}
{{-- Expects parent x-data with: activeTab, htmlPreview, isGenerating, isLoadingPreview, zoomLevel --}}
<div x-show="activeTab === 'preview'" class="w-full flex flex-col items-center">
    {{-- Document Frame --}}
    <div x-show="htmlPreview" 
         class="shadow-2xl bg-white ring-1 ring-slate-900/10 overflow-hidden origin-top transition-transform duration-300" 
         :style="`width: 210mm; min-height: 297mm; transform: scale(${zoomLevel});`" x-transition>
        <iframe :srcdoc="htmlPreview" class="w-full border-none" style="height: 297mm;" sandbox="allow-same-origin allow-scripts"></iframe>
    </div>

    {{-- Empty State --}}
    <div x-show="!htmlPreview && !isGenerating && !isLoadingPreview" class="flex-1 min-h-[600px] flex flex-col items-center justify-center text-center opacity-30 italic">
        <i data-lucide="file-text" class="w-16 h-16 mb-6"></i>
        <p class="text-sm font-bold uppercase tracking-widest">Protocol Registry Awaiting Build</p>
    </div>
</div>
