{{-- Document Viewer - Footer Watermark --}}
<div class="mt-20 p-10 border-t border-border bg-muted/10 flex justify-between items-center opacity-30 mono text-[8px] font-black uppercase tracking-[0.4em]">
    <span>ArchitGrid Registry v1.0.4</span>
    <span>Integrity Verified: {{ hash('sha256', $document->content) }}</span>
</div>
