{{-- Video Mode Sidebar: How It Works & Tokens --}}
<div x-show="generator === 'video'" 
     x-transition:enter="transition duration-500" 
     class="space-y-6" style="display: none;">
    
    {{-- How It Works --}}
    <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm overflow-hidden">
        <div class="p-6 bg-muted/30 border-b border-border flex items-center gap-2">
            <i data-lucide="help-circle" class="w-4 h-4 text-primary"></i>
            <h3 class="font-bold text-sm uppercase tracking-tighter">How It Works</h3>
        </div>
        <div class="p-6 space-y-6 text-xs">
            <div class="flex gap-4">
                <i data-lucide="layers" class="w-5 h-5 text-purple-500 shrink-0"></i>
                <div>
                    <p class="font-bold mb-1">Queue System</p>
                    <p class="text-muted-foreground leading-relaxed italic">Videos generate in the background. You can leave the page and come back later.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <i data-lucide="save" class="w-5 h-5 text-blue-500 shrink-0"></i>
                <div>
                    <p class="font-bold mb-1">Auto-Save</p>
                    <p class="text-muted-foreground leading-relaxed italic">All generated videos are automatically saved to your media gallery.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <i data-lucide="share-2" class="w-5 h-5 text-green-500 shrink-0"></i>
                <div>
                    <p class="font-bold mb-1">Social Media Scheduling</p>
                    <p class="text-muted-foreground leading-relaxed italic">On the completed video page, you can schedule your videos to your social media accounts.</p>
                </div>
            </div>
            <div class="flex gap-4">
                <i data-lucide="user-plus" class="w-5 h-5 text-amber-500 shrink-0"></i>
                <div>
                    <p class="font-bold mb-1">Sora Cameo Tagging</p>
                    <p class="text-muted-foreground leading-relaxed italic">Tag your Sora 2 Cameo handle in your description to include yourself in the video.</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Token Cost --}}
    <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm overflow-hidden">
        <div class="p-6 bg-muted/30 border-b border-border flex items-center gap-2">
            <i data-lucide="coins" class="w-4 h-4 text-amber-500"></i>
            <h3 class="font-bold text-sm uppercase tracking-tighter">Token Cost</h3>
        </div>
        <div class="p-6 space-y-4">
            <div class="bg-primary/5 border border-primary/20 rounded-lg p-3 flex justify-between items-center">
                <div>
                    <p class="text-[10px] uppercase font-bold text-primary">Cost</p>
                    <p class="text-[10px] text-muted-foreground italic">10-second video</p>
                </div>
                <p class="text-lg font-black text-primary">7 tokens</p>
            </div>

            {{-- Comparison Table --}}
            <div class="border border-border rounded-lg overflow-hidden bg-muted/10">
                <div class="bg-green-50/50 p-2 border-b border-border flex items-center justify-center gap-2">
                    <i data-lucide="zap" class="w-3 h-3 text-green-600"></i>
                    <span class="text-[10px] font-bold text-green-700 uppercase tracking-widest">Pricing Analysis</span>
                </div>
                <table class="w-full text-[10px]">
                    <thead class="bg-muted/50">
                        <tr class="text-left border-b border-border">
                            <th class="p-2 font-bold opacity-60">Duration</th>
                            <th class="p-2 font-bold text-primary">Architect</th>
                            <th class="p-2 font-bold opacity-60 text-right">OpenAI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-border/10">
                            <td class="p-2 opacity-80">10 seconds</td>
                            <td class="p-2 font-black text-green-600">$0.34</td>
                            <td class="p-2 opacity-80 text-right">$1.00</td>
                        </tr>
                        <tr>
                            <td class="p-2 opacity-80">15 seconds</td>
                            <td class="p-2 font-black text-green-600">$0.58</td>
                            <td class="p-2 opacity-80 text-right">$1.50</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center px-1">
                    <p class="text-[10px] font-bold uppercase text-muted-foreground">Your Balance</p>
                    <p class="text-[10px] font-black uppercase text-foreground">0 Tokens</p>
                </div>
                <button class="w-full h-10 border border-border bg-card hover:bg-muted text-[10px] font-bold uppercase tracking-widest rounded-lg transition-all">
                    Get More Tokens
                </button>
            </div>
        </div>
    </div>
</div>
