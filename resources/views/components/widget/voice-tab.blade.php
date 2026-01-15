<!-- Voice Tab -->
<div x-show="activeTab === 'voice'" class="h-full flex flex-col items-center justify-center text-center space-y-6 relative">
    
    <!-- Recording State -->
    <div x-show="isRecording" class="absolute inset-0 flex flex-col items-center justify-center bg-background/50 backdrop-blur-sm z-10 rounded-xl transition-all">
        <div class="w-24 h-24 rounded-full bg-red-500/20 flex items-center justify-center animate-pulse mb-4 relative">
            <div class="w-16 h-16 rounded-full bg-red-500 flex items-center justify-center shadow-lg shadow-red-500/40">
                <i data-lucide="mic" class="w-8 h-8 text-white"></i>
            </div>
            <!-- Ripple Effect -->
            <div class="absolute inset-0 rounded-full border-4 border-red-500/30 animate-ping"></div>
        </div>
        <h3 class="text-xl font-mono font-bold text-foreground" x-text="formatTime(recordingTime)">00:00</h3>
        <p class="text-[10px] uppercase tracking-widest text-red-500 font-bold mt-2 animate-pulse">Recording Live Audio...</p>
        
        <button @click="stopRecording()" class="mt-8 px-8 py-3 bg-foreground text-background rounded-full font-bold text-xs uppercase tracking-widest hover:scale-105 transition-transform shadow-xl">
            Stop Session
        </button>
    </div>

    <!-- Idle State -->
    <div x-show="!isRecording && !audioBlob" class="space-y-6">
        <div class="space-y-2">
            <div class="w-20 h-20 mx-auto rounded-3xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center border border-primary/20 shadow-inner">
                <i data-lucide="mic" class="w-10 h-10 text-primary"></i>
            </div>
            <h3 class="text-lg font-bold text-foreground">Meeting Scribe</h3>
            <p class="text-xs text-muted-foreground max-w-[200px] mx-auto leading-relaxed">
                Record meetings or voice memos. AI will extract tasks and summaries automatically.
            </p>
        </div>

        <button @click="startRecording()" class="w-16 h-16 rounded-full bg-primary text-primary-foreground flex items-center justify-center hover:scale-110 active:scale-95 transition-all shadow-xl shadow-primary/30 mx-auto group">
            <i data-lucide="mic" class="w-8 h-8 group-hover:animate-bounce"></i>
        </button>
        
        <!-- Microphone Selector -->
        <div class="max-w-[220px] mx-auto flex items-center gap-2">
            <div class="relative flex-1">
                <select x-model="selectedMicrophoneId" 
                        class="w-full bg-muted/30 border border-border/50 text-[10px] rounded-lg py-1.5 pl-2 pr-6 appearance-none focus:ring-1 focus:ring-primary/30 outline-none text-muted-foreground hover:text-foreground transition-colors cursor-pointer truncate">
                    <option value="default">Default System Microphone</option>
                    <template x-for="mic in availableMicrophones" :key="mic.deviceId">
                        <option :value="mic.deviceId" x-text="mic.label || 'Microphone ' + (availableMicrophones.indexOf(mic) + 1)"></option>
                    </template>
                </select>
                <i data-lucide="chevron-down" class="absolute right-2 top-1/2 -translate-y-1/2 w-3 h-3 text-muted-foreground pointer-events-none"></i>
            </div>
            
            <button @click="getMicrophones()" class="p-1.5 rounded-lg hover:bg-muted text-muted-foreground hover:text-foreground transition-colors" title="Refresh Devices">
                <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
            </button>
        </div>
        
        <div class="bg-muted/30 p-3 rounded-lg border border-border/50 text-left max-w-[260px] mx-auto">
            <div class="flex items-start gap-2">
                <i data-lucide="info" class="w-3 h-3 text-primary mt-0.5 shrink-0"></i>
                <p class="text-[10px] text-muted-foreground leading-snug">
                    <strong>Pro Tip:</strong> To capture Zoom/Teams audio, ensure your output volume is audible or use "Stereo Mix" if available on your OS.
                </p>
            </div>
        </div>
    </div>

    <!-- Review State (Post-Recording) -->
    <div x-show="!isRecording && audioBlob" class="w-full px-4 space-y-4 animate-in fade-in slide-in-from-bottom-4">
        <div class="bg-card border border-border rounded-xl p-4 shadow-sm relative overflow-hidden space-y-3">
            <!-- Playback Controls -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button @click="toggleAudioPlayback()" class="w-10 h-10 rounded-full bg-primary text-primary-foreground flex items-center justify-center hover:scale-105 transition-all shadow-md">
                        <template x-if="!isPlayingAudio">
                            <i data-lucide="play" class="w-4 h-4 ml-0.5"></i>
                        </template>
                        <template x-if="isPlayingAudio">
                            <i data-lucide="pause" class="w-4 h-4"></i>
                        </template>
                    </button>
                    <div class="text-left">
                        <p class="text-xs font-bold text-foreground">Voice Memo</p>
                        <p class="text-[10px] text-muted-foreground" x-text="formatTime(recordingTime)"></p>
                    </div>
                </div>
                <button @click="discardRecording()" class="text-muted-foreground hover:text-red-500 transition-colors p-2 bg-muted/50 rounded-lg" title="Discard">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </div>
            
            <!-- Metadata Form -->
            <div class="space-y-2 pt-2 border-t border-border/50">
                <input type="text" x-model="recordingTitle" placeholder="Recording Title..." 
                       class="w-full bg-muted/30 border border-border rounded-lg px-3 py-2 text-xs font-bold focus:ring-1 focus:ring-primary/30 outline-none">
                <textarea x-model="recordingDescription" placeholder="Add context or notes..." 
                          class="w-full bg-muted/30 border border-border rounded-lg px-3 py-2 text-xs resize-none h-16 focus:ring-1 focus:ring-primary/30 outline-none custom-scrollbar"></textarea>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-2">
            <button @click="saveAudio()" :disabled="isProcessing" class="w-full py-3 bg-indigo-600 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-indigo-500 disabled:opacity-50 flex items-center justify-center gap-2 transition-all">
                <template x-if="isProcessing">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                </template>
                <template x-if="!isProcessing">
                    <i data-lucide="save" class="w-4 h-4"></i>
                </template>
                <span>Save to Media Hub</span>
            </button>
            <button @click="processAudio('tasks')" :disabled="isProcessing" class="w-full py-3 bg-primary text-primary-foreground rounded-xl text-xs font-black uppercase tracking-widest hover:opacity-90 disabled:opacity-50 flex items-center justify-center gap-2 transition-all">
                <template x-if="isProcessing">
                    <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                </template>
                <template x-if="!isProcessing">
                    <i data-lucide="check-square" class="w-4 h-4"></i>
                </template>
                <span>Extract Action Items</span>
            </button>
            <button @click="processAudio('note')" :disabled="isProcessing" class="w-full py-3 bg-card border border-border text-foreground rounded-xl text-xs font-black uppercase tracking-widest hover:bg-muted disabled:opacity-50 flex items-center justify-center gap-2 transition-all">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>Transcribe to Note</span>
            </button>
        </div>
    </div>
</div>
