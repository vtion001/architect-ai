{{-- AI Tailoring Insight (Dynamic) --}}
{{-- Expects parent x-data with: tailoringReport --}}
<div x-show="tailoringReport" x-transition class="bg-blue-50 border border-blue-200 rounded-2xl p-5 shadow-sm relative overflow-hidden">
    <div class="flex items-start gap-4">
        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
            <i data-lucide="sparkles" class="w-5 h-5"></i>
        </div>
        <div class="flex-1">
            <h4 class="text-sm font-black uppercase tracking-wide text-blue-800 mb-2">Resume Tailoring Active</h4>
            <div class="prose prose-sm text-xs text-blue-900/80 leading-relaxed" x-html="tailoringReport"></div>
        </div>
        <button @click="tailoringReport = ''" class="text-blue-400 hover:text-blue-600">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
</div>
