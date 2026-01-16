{{-- HTML Tab Content --}}
{{-- Expects parent x-data with: activeTab, htmlPreview --}}
<div x-show="activeTab === 'html'" class="w-full h-full flex flex-col pt-10 px-4 relative z-10">
    <div x-show="htmlPreview" class="bg-slate-950 rounded-3xl p-8 overflow-auto border border-slate-800 shadow-2xl max-h-[800px] custom-scrollbar">
        <pre class="mono text-[11px] text-emerald-400 whitespace-pre-wrap"><code x-text="htmlPreview"></code></pre>
    </div>
    <div x-show="!htmlPreview" class="flex-1 min-h-[600px] flex flex-col items-center justify-center text-center opacity-30 italic">
        <i data-lucide="code" class="w-16 h-16 mb-6"></i>
        <p class="text-sm font-bold uppercase tracking-widest">No Source Code Available</p>
    </div>
</div>
