{{-- Publish Modal - Platform Selection & Scheduling --}}
{{-- 
    Expects parent x-data context with:
    showPublishModal, selectedPlatforms, postNow, scheduleDate,
    isFacebookConnected, selectedFacebookPage, 
    fetchFacebookPages(), fetchInstagramPages(), fetchLinkedinPages(), fetchTwitterPages(),
    confirmPublish()
--}}
<div x-show="showPublishModal" 
     x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm p-4" 
     x-transition>
    <div @click.away="showPublishModal = false" class="bg-card w-full max-w-sm rounded-xl shadow-2xl border border-border p-5 space-y-4 relative">
        <div class="text-center">
            <h3 class="text-lg font-bold">Publish to Social Planner</h3>
            <p class="text-xs text-muted-foreground">Where should this content go?</p>
        </div>

        <div class="grid grid-cols-2 gap-3">
            {{-- LinkedIn --}}
            <div class="relative">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-border bg-muted/20 cursor-pointer hover:bg-muted/40 transition-colors w-full" :class="{'border-blue-500 bg-blue-500/10': selectedPlatforms.includes('linkedin')}">
                    <input type="checkbox" value="linkedin" x-model="selectedPlatforms" class="hidden">
                    <div class="w-8 h-8 rounded bg-blue-600 flex items-center justify-center text-white">Li</div>
                    <span class="text-sm font-medium">LinkedIn</span>
                </label>
                <button @click.stop="fetchLinkedinPages" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 hover:bg-blue-600/20 rounded-full transition-colors z-10" title="Select Page">
                    <i data-lucide="settings" class="w-4 h-4 text-muted-foreground"></i>
                </button>
            </div>

            {{-- Twitter --}}
            <div class="relative">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-border bg-muted/20 cursor-pointer hover:bg-muted/40 transition-colors w-full" :class="{'border-sky-500 bg-sky-500/10': selectedPlatforms.includes('twitter')}">
                    <input type="checkbox" value="twitter" x-model="selectedPlatforms" class="hidden">
                    <div class="w-8 h-8 rounded bg-sky-400 flex items-center justify-center text-white">Tw</div>
                    <span class="text-sm font-medium">Twitter</span>
                </label>
                <button @click.stop="fetchTwitterPages" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 hover:bg-sky-400/20 rounded-full transition-colors z-10" title="Select Account">
                    <i data-lucide="settings" class="w-4 h-4 text-muted-foreground"></i>
                </button>
            </div>

            {{-- Facebook --}}
            <div class="relative" :class="!isFacebookConnected && 'opacity-60 cursor-not-allowed'">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-border bg-muted/20 cursor-pointer hover:bg-muted/40 transition-colors w-full" 
                       :class="{'border-blue-700 bg-blue-700/10': selectedPlatforms.includes('facebook')}"
                       @click="if(!isFacebookConnected) { alert('Connect Facebook in Social Planner first'); return false; }">
                    <input type="checkbox" value="facebook" x-model="selectedPlatforms" class="hidden" :disabled="!isFacebookConnected">
                    <div class="w-8 h-8 rounded bg-blue-700 flex items-center justify-center text-white">Fb</div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium">Facebook</span>
                        <span x-show="!isFacebookConnected" class="text-[9px] text-red-500 font-bold uppercase tracking-tighter">Not Connected</span>
                        <span x-show="isFacebookConnected && selectedFacebookPage" class="text-[9px] text-blue-600 font-bold uppercase tracking-tighter truncate max-w-[100px]" x-text="selectedFacebookPage?.name"></span>
                    </div>
                </label>
                <button @click.stop="fetchFacebookPages" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 hover:bg-blue-200/20 rounded-full transition-colors z-10" title="Select Page">
                    <i data-lucide="settings" class="w-4 h-4 text-muted-foreground"></i>
                </button>
            </div>
             
            {{-- Selected Page Indicator (Shared/FB) --}}
            <div x-show="selectedFacebookPage" class="col-span-2 text-xs text-center p-2 bg-blue-50 text-blue-800 rounded-lg flex items-center justify-center gap-2">
                <i data-lucide="check-circle" class="w-3 h-3"></i>
                Posting to: <strong x-text="selectedFacebookPage?.name"></strong>
            </div>

            {{-- Instagram --}}
            <div class="relative" :class="!isFacebookConnected && 'opacity-60 cursor-not-allowed'">
                <label class="flex items-center gap-3 p-3 rounded-lg border border-border bg-muted/20 cursor-pointer hover:bg-muted/40 transition-colors w-full" :class="{'border-pink-500 bg-pink-500/10': selectedPlatforms.includes('instagram')}">
                    <input type="checkbox" value="instagram" x-model="selectedPlatforms" class="hidden" :disabled="!isFacebookConnected">
                    <div class="w-8 h-8 rounded bg-pink-600 flex items-center justify-center text-white">In</div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium">Instagram</span>
                        <span x-show="isFacebookConnected && selectedFacebookPage" class="text-[9px] text-pink-600 font-bold uppercase tracking-tighter truncate max-w-[100px]" x-text="selectedFacebookPage?.name"></span>
                    </div>
                </label>
                <button @click.stop="fetchInstagramPages" class="absolute right-2 top-1/2 -translate-y-1/2 p-2 hover:bg-pink-500/20 rounded-full transition-colors z-10" title="Select Account">
                    <i data-lucide="settings" class="w-4 h-4 text-muted-foreground"></i>
                </button>
            </div>
        </div>

        {{-- Timing Options --}}
        <div class="space-y-2 py-1">
            <div class="flex items-center justify-between">
                <label class="text-[10px] font-bold uppercase text-muted-foreground">Timing</label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <span class="text-[10px] font-bold uppercase transition-colors" :class="postNow ? 'text-primary' : 'text-muted-foreground'">Post Now</span>
                    <div class="relative w-8 h-4 bg-muted rounded-full transition-colors" :class="postNow && 'bg-primary/20'">
                        <input type="checkbox" x-model="postNow" class="sr-only">
                        <div class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full transition-transform shadow-sm" :class="postNow && 'translate-x-4 bg-primary'"></div>
                    </div>
                </label>
            </div>
            
            <div x-show="!postNow" x-transition>
                <input type="datetime-local" x-model="scheduleDate" class="w-full bg-muted/30 border border-border rounded-lg text-sm px-3 py-2 focus:ring-1 focus:ring-primary focus:border-primary outline-none transition-all">
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-2 pt-2">
            <button @click="showPublishModal = false" class="flex-1 py-2 text-xs font-bold uppercase text-muted-foreground hover:bg-muted rounded-lg">Cancel</button>
            <button @click="confirmPublish" class="flex-1 py-2 text-xs font-bold uppercase bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 shadow-md">Confirm</button>
        </div>

        {{-- Page Selection Modal (Nested) --}}
        @include('content-creator.partials.post-card.modals.page-selection-modal')
    </div>
</div>
