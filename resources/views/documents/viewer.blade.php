@extends('layouts.app')

@section('content')
<div class="p-10 max-w-[1200px] mx-auto animate-in fade-in duration-700" 
     x-data="{ 
        isEditing: false, 
        content: @js($document->content), 
        isSaving: false,
        zoomLevel: 0.8,
        
        init() {
            this.$watch('isEditing', (value) => {
                const frame = this.$refs.previewFrame;
                if (frame && frame.contentDocument) {
                    frame.contentDocument.designMode = value ? 'on' : 'off';
                    if (value) {
                        // Add a visual indicator inside the iframe if possible, or just rely on parent
                        frame.contentDocument.body.style.transition = 'background-color 0.3s';
                        frame.contentDocument.body.style.backgroundColor = '#fcfcfc';
                    } else {
                        frame.contentDocument.body.style.backgroundColor = '';
                    }
                }
            });
        },

        saveDocument() {
            this.isSaving = true;
            const frame = this.$refs.previewFrame;
            if (frame && frame.contentDocument) {
                // Grab the entire HTML including the doctype
                const updatedContent = '<!DOCTYPE html>\n' + frame.contentDocument.documentElement.outerHTML;
                this.content = updatedContent;
            }

            fetch('{{ route('documents.update', $document) }}', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ content: this.content })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    this.isEditing = false;
                    // No reload needed if we just updated the internal state, 
                    // but reload ensures Lucide icons and other server-side things reset.
                    window.location.reload();
                } else {
                    alert('Failed to save.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Error saving document.');
            })
            .finally(() => { this.isSaving = false; });
        },

        downloadPdf() {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(this.content);
            printWindow.document.close();
            setTimeout(() => {
                printWindow.print();
            }, 500);
        }
     }">
    <!-- Archive Header -->
    <div class="mb-12 flex flex-col md:flex-row items-start md:items-center justify-between gap-8 border-b border-border pb-10">
        <div>
            <div class="flex items-center gap-3 mb-4">
                <a href="{{ route('documents.index') }}" class="w-10 h-10 rounded-xl bg-muted/50 border border-border flex items-center justify-center hover:bg-primary/10 hover:border-primary/30 transition-all group">
                    <i data-lucide="arrow-left" class="w-4 h-4 text-muted-foreground group-hover:text-primary"></i>
                </a>
                <div class="flex flex-col">
                    <span class="mono text-[10px] font-black uppercase tracking-[0.3em] text-primary">Protocol: Archived Intelligence</span>
                    <h1 class="text-4xl font-black uppercase tracking-tighter text-foreground">{{ $document->name }}</h1>
                </div>
            </div>
            <div class="flex items-center gap-6 px-1">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <span class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest">{{ $document->category ?? 'General' }} Archive</span>
                </div>
                <span class="text-[10px] text-muted-foreground font-mono uppercase tracking-tighter">Asset UUID: {{ substr($document->id, 0, 13) }}...</span>
                <span class="text-[10px] text-muted-foreground font-mono uppercase tracking-tighter">Stored: {{ $document->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <div class="flex items-center gap-4 mr-4 bg-muted/30 p-1 rounded-xl px-3 h-10">
                <button @click="zoomLevel = Math.max(0.3, zoomLevel - 0.1)" class="text-slate-500 hover:text-primary transition-colors"><i data-lucide="minus-circle" class="w-4 h-4"></i></button>
                <span class="mono text-[10px] font-black text-slate-400 w-8 text-center" x-text="Math.round(zoomLevel * 100) + '%'"></span>
                <button @click="zoomLevel = Math.min(1.2, zoomLevel + 0.1)" class="text-slate-500 hover:text-primary transition-colors"><i data-lucide="plus-circle" class="w-4 h-4"></i></button>
            </div>

            <!-- Edit Controls -->
            <button @click="isEditing = true" x-show="!isEditing" class="h-14 px-8 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                Edit Document
            </button>

            <div x-show="isEditing" class="flex gap-2" style="display: none;">
                <button @click="isEditing = false; window.location.reload()" class="h-14 px-6 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest hover:bg-red-50 hover:text-red-600 transition-all">
                    Cancel
                </button>
                <button @click="saveDocument" :disabled="isSaving" class="h-14 px-8 rounded-2xl bg-primary text-primary-foreground font-black uppercase text-[10px] tracking-[0.2em] flex items-center gap-3 shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all disabled:opacity-50">
                    <i x-show="!isSaving" data-lucide="save" class="w-4 h-4"></i>
                    <i x-show="isSaving" data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                    <span x-text="isSaving ? 'Saving...' : 'Save Changes'"></span>
                </button>
            </div>

            <!-- Standard Actions (Hidden when editing) -->
            <button x-show="!isEditing" @click="downloadPdf" class="h-14 px-8 rounded-2xl border border-border bg-card font-black uppercase text-[10px] tracking-widest flex items-center gap-3 hover:bg-muted transition-all">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export Archive
            </button>
        </div>
    </div>

    <!-- Document Content Node -->
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

    <!-- Footer Watermark -->
    <div class="mt-20 p-10 border-t border-border bg-muted/10 flex justify-between items-center opacity-30 mono text-[8px] font-black uppercase tracking-[0.4em]">
        <span>ArchitGrid Registry v1.0.4</span>
        <span>Integrity Verified: {{ hash('sha256', $document->content) }}</span>
    </div>
</div>

<style>
    /* Architectural Document Styling */
    .prose-architect {
        font-family: 'Inter', sans-serif;
        color: #1e293b;
        line-height: 1.8;
    }
    /* Handle both Markdown-style and HTML content within the viewer */
    .prose-architect h1 { font-family: 'Montserrat', sans-serif; font-weight: 900; color: #0f172a; font-size: 2.25rem; margin-bottom: 2rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 1rem; }
    .prose-architect h2 { font-family: 'Montserrat', sans-serif; font-weight: 800; color: #1e293b; font-size: 1.5rem; margin-top: 3rem; margin-bottom: 1rem; }
    .prose-architect p { margin-bottom: 1.5rem; font-size: 1.05rem; }
    .prose-architect table { width: 100%; border-collapse: collapse; margin: 2rem 0; }
    .prose-architect th { background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px; text-align: left; font-size: 0.8rem; font-weight: 900; text-transform: uppercase; }
    .prose-architect td { border: 1px solid #e2e8f0; padding: 12px; }
</style>
@endsection
