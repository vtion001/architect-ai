{{-- Voice Profile Form Section --}}
{{-- 
    @param $modelPrefix - Either 'newBrand' or 'selectedBrand' for x-model binding
    @param $showFullFields - Whether to show all voice profile fields (keywords, avoid words)
--}}
@props(['modelPrefix' => 'newBrand', 'showFullFields' => true])

<div class="space-y-6 pt-6 border-t border-border/50">
    <h3 class="text-[10px] font-black uppercase tracking-widest text-primary flex items-center gap-2">
        <i data-lucide="mic" class="w-3 h-3"></i> Voice & Tone
    </h3>
    
    <div class="grid grid-cols-2 gap-6">
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Tone</label>
            <select x-model="{{ $modelPrefix }}.voice_profile.tone" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                <option>Professional</option>
                <option>Casual</option>
                <option>Friendly</option>
                <option>Authoritative</option>
                <option>Playful</option>
                <option>Luxurious</option>
                <option>Empathetic</option>
                <option>Bold</option>
            </select>
        </div>
        <div class="space-y-3">
            <label class="text-[10px] font-black uppercase text-slate-500 italic">Writing Style</label>
            <select x-model="{{ $modelPrefix }}.voice_profile.writing_style" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm font-bold">
                <option>Balanced</option>
                <option>Concise</option>
                <option>Detailed</option>
                <option>Conversational</option>
                <option>Technical</option>
                <option>Storytelling</option>
            </select>
        </div>
    </div>
    
    <div class="space-y-3">
        <label class="text-[10px] font-black uppercase text-slate-500 italic">Brand Personality</label>
        <input x-model="{{ $modelPrefix }}.voice_profile.personality" type="text" placeholder="e.g. Innovative, Trustworthy, Approachable" class="w-full h-12 bg-muted/20 border border-border rounded-xl px-4 text-sm">
    </div>
    
    @if($showFullFields)
        <div class="grid grid-cols-2 gap-6">
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase text-slate-500 italic">Key Phrases to Use</label>
                <textarea x-model="{{ $modelPrefix }}.voice_profile.keywords" rows="2" placeholder="Phrases that represent your brand..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
            </div>
            <div class="space-y-3">
                <label class="text-[10px] font-black uppercase text-slate-500 italic">Words to Avoid</label>
                <textarea x-model="{{ $modelPrefix }}.voice_profile.avoid_words" rows="2" placeholder="Words that don't fit your brand..." class="w-full bg-muted/20 border border-border rounded-xl p-4 text-sm"></textarea>
            </div>
        </div>
    @endif
</div>
