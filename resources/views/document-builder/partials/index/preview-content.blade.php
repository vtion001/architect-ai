{{-- Document Builder - Preview Content --}}
<div class="w-full flex flex-col">
    <!-- Tabs and Zoom Controls -->
    <div class="flex items-center gap-4 mb-4">
        <div class="bg-muted p-1 rounded-md flex w-fit">
            <button @click="activeTab = 'preview'" 
                :class="activeTab === 'preview' ? 'bg-background text-foreground shadow-sm' : 'text-muted-foreground hover:bg-background/50'"
                class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium transition-all">
                Preview
            </button>
        </div>
        <div class="flex items-center gap-2 ml-auto">
            <button @click="zoomLevel = Math.max(0.3, zoomLevel - 0.1)" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-muted text-muted-foreground transition-colors" title="Zoom Out">
                <i data-lucide="minus" class="w-4 h-4"></i>
            </button>
            <span class="text-xs text-muted-foreground font-medium w-12 text-center" x-text="Math.round(zoomLevel * 100) + '%'"></span>
            <button @click="zoomLevel = Math.min(1.5, zoomLevel + 0.1)" class="w-8 h-8 flex items-center justify-center rounded-md hover:bg-muted text-muted-foreground transition-colors" title="Zoom In">
                <i data-lucide="plus" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    {{-- Preview Tab --}}
    <div x-show="activeTab === 'preview'" class="border rounded-lg min-h-[700px] bg-muted/30 relative flex flex-col items-center justify-start p-6 overflow-y-auto overflow-x-hidden">
        <!-- Loading Overlay -->
        <div x-show="isGenerating || isLoadingPreview" class="absolute inset-0 bg-background/60 backdrop-blur-[2px] z-30 flex items-center justify-center rounded-xl" style="display: none;">
            <div class="flex flex-col items-center gap-4 bg-white p-8 rounded-2xl shadow-2xl border ring-1 ring-slate-900/5">
                <div class="relative">
                    <div class="w-16 h-16 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" data-lucide="sparkles" class="lucide lucide-sparkles w-6 h-6 text-primary absolute inset-0 m-auto animate-pulse"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>
                </div>
                <div class="text-center">
                    <span class="text-lg font-bold block text-slate-900" x-text="isGenerating ? 'Generating Report' : 'Loading Preview'"></span>
                    <span class="text-sm text-slate-500" x-text="isGenerating ? 'AI is crafting your document...' : 'Fetching template preview...'"></span>
                </div>
            </div>
        </div>
        
        <!-- Template Preview via iframe -->
        <div x-show="!isLoadingPreview && htmlPreview" 
              class="shadow-2xl bg-white ring-1 ring-slate-900/5 overflow-hidden origin-top transition-transform duration-200" 
              :style="`width: 210mm; min-height: 297mm; transform: scale(${zoomLevel})`">
            <iframe :srcdoc="htmlPreview" class="w-full border-none" style="height: 297mm;" sandbox="allow-same-origin allow-scripts"></iframe>
        </div>

        <!-- Placeholder when no preview is available -->
        <div x-show="!isLoadingPreview && !htmlPreview" 
             class="flex-shrink-0 shadow-lg bg-white rounded-sm relative origin-top transition-transform duration-200 flex flex-col items-center justify-center"
             :style="`width: 210mm; height: 297mm; transform: scale(${zoomLevel})`">
            <i data-lucide="file-text" class="w-16 h-16 text-muted-foreground/30 mb-4"></i>
            <h3 class="text-lg font-medium text-muted-foreground">No Preview Available</h3>
            <p class="text-sm text-muted-foreground/70">Select a template to see the preview</p>
        </div>
    </div>

    {{-- HTML Tab --}}
    <div x-show="activeTab === 'html'" class="border rounded-lg p-4 min-h-[400px] bg-muted/30 overflow-auto font-mono text-xs">
         <div x-show="!htmlPreview" class="flex items-center justify-center h-[400px] text-muted-foreground">
            <p>No HTML code available yet.</p>
        </div>
        <div x-show="htmlPreview">
            <pre class="bg-slate-100 p-2 rounded text-[10px] whitespace-pre-wrap"><code x-text="htmlPreview"></code></pre>
        </div>
    </div>
</div>
