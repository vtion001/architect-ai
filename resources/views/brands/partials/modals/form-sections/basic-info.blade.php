{{-- Basic Information Form Section --}}
{{-- 
    @param $modelPrefix - Either 'newBrand' or 'selectedBrand' for x-model binding
    @param $showScrapeButton - Whether to show the website DNA scrape button (only for create)
--}}
@props(['modelPrefix' => 'newBrand', 'showScrapeButton' => false])

<div class="space-y-6">
    <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
        <i data-lucide="info" class="w-3 h-3"></i> Basic Information
    </h3>
    
    <div class="grid grid-cols-2 gap-6">
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Brand Name *</label>
            <input x-model="{{ $modelPrefix }}.name" type="text" required placeholder="My Brand" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold focus:ring-2 focus:ring-primary/20 outline-none">
        </div>
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Tagline</label>
            <input x-model="{{ $modelPrefix }}.tagline" type="text" placeholder="Your catchy tagline..." class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none">
        </div>
    </div>
    
    <div class="grid grid-cols-2 gap-6">
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Industry</label>
            <select x-model="{{ $modelPrefix }}.industry" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                <option value="">Select Industry...</option>
                <option>Technology</option>
                <option>Healthcare</option>
                <option>Finance</option>
                <option>E-commerce</option>
                <option>Real Estate</option>
                <option>Food & Beverage</option>
                <option>Fashion</option>
                <option>Beauty & Wellness</option>
                <option>Education</option>
                <option>Entertainment</option>
                <option>Travel & Hospitality</option>
                <option>Automotive</option>
                <option>Construction</option>
                <option>Other</option>
            </select>
        </div>
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Website</label>
            @if($showScrapeButton)
                <div class="flex gap-2">
                    <input x-model="{{ $modelPrefix }}.contact_info.website" type="text" placeholder="https://example.com" class="flex-1 h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
                    <button @click="scrapeWebsite" :disabled="isScraping" type="button" class="h-12 px-4 rounded-xl bg-primary/10 text-primary font-black text-[10px] uppercase tracking-widest hover:bg-primary/20 transition-all flex items-center gap-2 disabled:opacity-50">
                        <template x-if="isScraping"><i data-lucide="loader-2" class="w-3 h-3 animate-spin"></i></template>
                        <span x-text="isScraping ? 'Scanning...' : 'Scan DNA'"></span>
                    </button>
                </div>
            @else
                <input x-model="{{ $modelPrefix }}.contact_info.website" type="text" placeholder="https://example.com" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
            @endif
        </div>
    </div>
    
    @if($showScrapeButton)
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Description</label>
            <textarea x-model="{{ $modelPrefix }}.description" rows="2" placeholder="Brief description of your brand..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/20 outline-none resize-none"></textarea>
        </div>
    @endif
</div>
