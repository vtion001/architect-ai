{{-- 
    Note/Task Widget Component

    Multi-tab widget for managing:
    - Tasks with categories, due dates, and breakdowns
    - Notes
    - Voice recordings and transcriptions
    - Ghost Studio screen recordings
    - History/archive

    Already modularized - uses @include for tab content.
    Alpine.js logic available in /resources/js/components/note-task-widget.js
--}}

<div x-data="noteTaskWidget()" 
     x-cloak
     @open-notes.window="isOpen = true; activeTab = 'notes'">

    {{-- Chat Popup Window - Fixed size, aligned with AI Chat --}}
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-x-4"
         x-transition:enter-end="opacity-100 scale-100 translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-x-0"
         x-transition:leave-end="opacity-0 scale-95 translate-x-4"
         class="fixed bg-card border border-border rounded-2xl shadow-2xl overflow-hidden flex flex-col z-[99998]"
         style="bottom: 24px; right: 94px; width: 380px; max-width: calc(100vw - 48px); height: 580px; max-height: calc(100vh - 48px);">
         
         {{-- Header --}}
         <div class="px-4 py-3 border-b border-border shrink-0" 
              style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(99, 102, 241, 0.02));">
             
             <div class="flex items-center justify-between mb-3">
                 <h3 class="text-xs font-black uppercase tracking-widest text-foreground/80 flex items-center gap-2">
                     <i data-lucide="layout-grid" class="w-3.5 h-3.5 text-indigo-500"></i>
                     Command Center
                 </h3>
                 <div class="flex items-center gap-1">
                     <button @click="showSearch = !showSearch" 
                             :class="showSearch ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:text-foreground'"
                             class="p-1.5 rounded-md transition-colors" title="Search">
                         <i data-lucide="search" class="w-4 h-4"></i>
                     </button>
                     <button @click="isOpen = false" class="p-1.5 hover:bg-muted rounded-md transition-colors text-muted-foreground hover:text-foreground">
                         <i data-lucide="x" class="w-4 h-4"></i>
                     </button>
                 </div>
             </div>

             {{-- Segmented Tab Control --}}
             <div class="flex bg-muted/50 rounded-xl p-1 border border-border/50">
                 <button @click="activeTab = 'tasks'" 
                         :class="activeTab === 'tasks' ? 'bg-card text-indigo-600 shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                         class="flex-1 flex items-center justify-center py-2 rounded-lg transition-all" title="Tasks">
                     <i data-lucide="check-square" class="w-4 h-4"></i>
                 </button>
                 <button @click="activeTab = 'notes'" 
                         :class="activeTab === 'notes' ? 'bg-card text-indigo-600 shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                         class="flex-1 flex items-center justify-center py-2 rounded-lg transition-all" title="Notes">
                     <i data-lucide="sticky-note" class="w-4 h-4"></i>
                 </button>
                 <button @click="activeTab = 'voice'" 
                         :class="activeTab === 'voice' ? 'bg-card text-indigo-600 shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                         class="flex-1 flex items-center justify-center py-2 rounded-lg transition-all" title="Meeting Scribe">
                     <i data-lucide="mic" class="w-4 h-4"></i>
                 </button>
                 <button @click="activeTab = 'studio'" 
                         :class="activeTab === 'studio' ? 'bg-card text-indigo-600 shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                         class="flex-1 flex items-center justify-center py-2 rounded-lg transition-all" title="Ghost Studio">
                     <i data-lucide="clapperboard" class="w-4 h-4"></i>
                 </button>
                 <button @click="activeTab = 'history'" 
                         :class="activeTab === 'history' ? 'bg-card text-indigo-600 shadow-sm' : 'text-muted-foreground hover:text-foreground'"
                         class="flex-1 flex items-center justify-center py-2 rounded-lg transition-all" title="Archive">
                     <i data-lucide="history" class="w-4 h-4"></i>
                 </button>
             </div>
         </div>

         {{-- Search Bar --}}
         <div x-show="showSearch" 
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 -translate-y-2"
              x-transition:enter-end="opacity-100 translate-y-0"
              class="px-4 py-2 border-b border-border bg-card">
             <input type="text" x-model="searchQuery" placeholder="Search across all modules..." 
                    class="w-full bg-muted/30 border border-border rounded-lg px-3 py-2 text-xs outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500/50 transition-all">
         </div>

         {{-- Content Area --}}
         <div class="flex-1 overflow-y-auto p-4 custom-scrollbar bg-background/30">
             <div x-show="activeTab === 'tasks'">
                 @include('components.widget.tasks-tab')
             </div>
             <div x-show="activeTab === 'notes'">
                 @include('components.widget.notes-tab')
             </div>
             <div x-show="activeTab === 'voice'" class="h-full">
                 @include('components.widget.voice-tab')
             </div>
             <div x-show="activeTab === 'studio'">
                 @include('components.widget.studio-tab')
             </div>
             <div x-show="activeTab === 'history'">
                 @include('components.widget.history-tab')
             </div>
         </div>
         
         {{-- Global Overlays (Modals) - Outside scrollable area to cover entire panel --}}
         @include('components.widget.widget-modals')
    </div>

    {{-- Floating Toggle Button --}}
    <button @click="isOpen = !isOpen" 
            style="position: fixed; bottom: 96px; right: 24px; z-index: 99999; background-color: #6366f1; box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);"
            class="w-14 h-14 rounded-full shadow-xl flex items-center justify-center text-white transition-all hover:scale-110 active:scale-95 group overflow-hidden">
        
        <svg x-show="!isOpen" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
            <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        <svg x-show="isOpen" x-cloak xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
            <path d="m18 8-6 6-6-6"/>
        </svg>

        <template x-if="pendingCount > 0 && !isOpen">
            <span class="absolute top-3 right-3 flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500 border-2 border-indigo-500"></span>
            </span>
        </template>
    </button>
</div>

{{-- Alpine.js Component Registration (inline for backward compatibility) --}}
<script>
// Global Audio Context Handler
if (typeof window.taskAudioContext === 'undefined') {
    window.taskAudioContext = null;
    window.initTaskAudio = function() { if (!window.taskAudioContext) window.taskAudioContext = new (window.AudioContext || window.webkitAudioContext)(); if (window.taskAudioContext.state === 'suspended') window.taskAudioContext.resume(); };
    window.playTaskSound = function(type) { window.initTaskAudio(); const ctx = window.taskAudioContext; if (!ctx) return; if (ctx.state === 'suspended') ctx.resume(); try { const osc = ctx.createOscillator(), gn = ctx.createGain(); osc.connect(gn); gn.connect(ctx.destination); const now = ctx.currentTime; if (type === 'add') { osc.type = 'square'; osc.frequency.setValueAtTime(600, now); osc.frequency.exponentialRampToValueAtTime(100, now + 0.1); gn.gain.setValueAtTime(0.1, now); gn.gain.exponentialRampToValueAtTime(0.001, now + 0.1); osc.start(now); osc.stop(now + 0.1); } else if (type === 'success') { osc.type = 'sine'; osc.frequency.setValueAtTime(1200, now); gn.gain.setValueAtTime(0.2, now); gn.gain.exponentialRampToValueAtTime(0.001, now + 0.5); osc.start(now); osc.stop(now + 0.5); } else if (type === 'alarm') { osc.type = 'sawtooth'; osc.frequency.setValueAtTime(800, now); gn.gain.setValueAtTime(0.2, now); gn.gain.setValueAtTime(0.2, now + 0.1); gn.gain.linearRampToValueAtTime(0, now + 0.4); osc.start(now); osc.stop(now + 0.4); } } catch (e) {} };
    document.addEventListener('click', window.initTaskAudio);
    document.addEventListener('keydown', window.initTaskAudio);
}

document.addEventListener('alpine:init', () => {
    Alpine.data('noteTaskWidget', () => ({
        isOpen: false, activeTab: 'tasks', tasks: [], notes: [], history: [], categories: [], newTaskTitle: '', newTaskDate: '', selectedCategory: null, alarmEnabled: false, newCategoryName: '', newCategoryColor: '#3b82f6', openBreakdownModal: false, breakdownPrompt: '', breakdownSteps: [], generatedTitle: '', isGenerating: false, viewingTask: null, showSearch: false, searchQuery: '', isRecording: false, recordingTime: 0, audioBlob: null, isProcessing: false, mediaRecorder: null, audioChunks: [], timerInterval: null, recordingTitle: '', recordingDescription: '', isPlayingAudio: false, audioPlayer: null, availableMicrophones: [], selectedMicrophoneId: 'default', isGhostRecording: false, ghostEvents: [], ghostDemos: [], stopFn: null,
        get pendingCount() { return this.tasks.filter(t => t.status !== 'completed').length; },
        get filteredTasks() { if (!this.searchQuery.trim()) return this.tasks; const q = this.searchQuery.toLowerCase(); return this.tasks.filter(t => t.title.toLowerCase().includes(q)); },
        get filteredNotes() { if (!this.searchQuery.trim()) return this.notes; const q = this.searchQuery.toLowerCase(); return this.notes.filter(n => n.title.toLowerCase().includes(q) || (n.description && n.description.toLowerCase().includes(q))); },
        init() { this.fetchData(); this.getMicrophones(); if (window.lucide) window.lucide.createIcons(); this.$watch('isOpen', v => { if (v && window.lucide) setTimeout(() => lucide.createIcons(), 100); }); this.$watch('activeTab', v => { if (v === 'history') this.fetchHistory(); if (v === 'voice') this.getMicrophones(); if (window.lucide) setTimeout(() => lucide.createIcons(), 50); }); setInterval(() => this.checkAlarms(), 30000); },
        checkAlarms() { const now = new Date(); this.tasks.forEach(t => { if (t.alarm_enabled && t.due_date && t.status !== 'completed') { const d = new Date(t.due_date); if (now - d >= 0 && now - d < 60000) this.triggerAlarm(t); } }); },
        triggerAlarm(t) { window.playTaskSound('alarm'); if (Notification.permission === "granted") new Notification("Task Due: " + t.title); },
        async fetchData() { try { const r = await fetch('/tasks'); const d = await r.json(); if (d.categories) this.categories = d.categories; if (d.tasks) { this.tasks = d.tasks.filter(t => t.type === 'task'); this.notes = d.tasks.filter(t => t.type === 'note'); } } catch (e) {} },
        async fetchHistory() { try { const r = await fetch('/tasks?trashed=1'); const d = await r.json(); if (d.tasks) this.history = d.tasks; } catch (e) {} },
        async restoreItem(id) { try { await fetch(`/tasks/${id}/restore`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }); this.fetchHistory(); this.fetchData(); } catch (e) {} },
        async forceDeleteItem(id) { if (!confirm('Permanently delete?')) return; try { await fetch(`/tasks/${id}/force`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }); this.fetchHistory(); } catch (e) {} },
        async addTask(title = null, parentId = null, skipRefresh = false) { const t = title || this.newTaskTitle; if (!t?.trim()) return; const p = { title: t.trim(), type: 'task', parent_id: parentId, category_id: this.selectedCategory?.id, due_date: this.newTaskDate || null, alarm_enabled: this.alarmEnabled }; try { const r = await fetch('/tasks', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify(p) }); const d = await r.json(); if (d.success) { window.playTaskSound('add'); if (!parentId) { this.tasks.unshift(d.task); this.newTaskTitle = ''; this.alarmEnabled = false; } else if (!skipRefresh) { await this.fetchData(); if (this.viewingTask?.id === parentId) this.viewingTask = this.tasks.find(x => x.id === parentId); } } } catch (e) {} },
        openTaskDetails(t) { this.viewingTask = t; }, closeTaskDetails() { this.viewingTask = null; },
        async updateTaskTitle(t, n) { if (!n.trim()) return; try { await fetch(`/tasks/${t.id}`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ title: n }) }); } catch (e) {} },
        async updateTaskField(t, f, v) { try { await fetch(`/tasks/${t.id}`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ [f]: v }) }); } catch (e) {} },
        async addNote() { try { const r = await fetch('/tasks', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ title: 'Untitled Note', type: 'note' }) }); const d = await r.json(); if (d.success) this.notes.unshift(d.task); } catch (e) {} },
        async addCategory() { if (!this.newCategoryName.trim()) return; try { const r = await fetch('/task-categories', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ name: this.newCategoryName, color: this.newCategoryColor }) }); const d = await r.json(); if (d.success) { this.categories.push(d.category); this.newCategoryName = ''; this.selectedCategory = d.category; } } catch (e) {} },
        async deleteCategory(id) { try { await fetch(`/task-categories/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }); this.categories = this.categories.filter(c => c.id !== id); this.fetchData(); } catch (e) {} },
        async updateNote(n) { try { await fetch(`/tasks/${n.id}`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ title: n.title, description: n.description }) }); } catch (e) {} },
        async toggleTask(t) { const s = t.status === 'completed' ? 'pending' : 'completed'; t.status = s; if (s === 'completed') window.playTaskSound('success'); try { await fetch(`/tasks/${t.id}`, { method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ status: s }) }); } catch (e) { t.status = t.status === 'completed' ? 'pending' : 'completed'; } },
        async deleteTask(id) { if (!confirm('Move to history?')) return; try { await fetch(`/tasks/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }); this.fetchData(); if (this.viewingTask?.id === id) this.closeTaskDetails(); } catch (e) {} },
        async generateBreakdown() { if (this.breakdownSteps.length > 0) { const pt = this.generatedTitle || (this.breakdownPrompt.length > 50 ? this.breakdownPrompt.substring(0, 47) + '...' : this.breakdownPrompt); try { const r = await fetch('/tasks', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ title: pt, description: this.breakdownPrompt, type: 'task', category_id: this.selectedCategory?.id, due_date: this.newTaskDate || null, alarm_enabled: this.alarmEnabled }) }); const d = await r.json(); if (d.success) { window.playTaskSound('add'); for (const s of this.breakdownSteps) await this.addTask(s, d.task.id, true); this.breakdownSteps = []; this.breakdownPrompt = ''; this.openBreakdownModal = false; this.fetchData(); } } catch (e) {} return; } this.isGenerating = true; try { const r = await fetch('/tasks/breakdown', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ content: this.breakdownPrompt }) }); const d = await r.json(); if (d.success) { this.breakdownSteps = d.steps; this.generatedTitle = d.title; } } catch (e) {} finally { this.isGenerating = false; } },
        async getMicrophones() { if (!navigator.mediaDevices) return; try { let d = await navigator.mediaDevices.enumerateDevices(); this.availableMicrophones = d.filter(x => x.kind === 'audioinput'); } catch (e) {} },
        async startRecording() { try { const s = await navigator.mediaDevices.getUserMedia({ audio: { deviceId: this.selectedMicrophoneId !== 'default' ? { exact: this.selectedMicrophoneId } : undefined } }); this.mediaRecorder = new MediaRecorder(s); this.audioChunks = []; this.mediaRecorder.ondataavailable = (e) => { if (e.data.size > 0) this.audioChunks.push(e.data); }; this.mediaRecorder.onstop = () => { this.audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' }); this.isRecording = false; clearInterval(this.timerInterval); s.getTracks().forEach(t => t.stop()); }; this.mediaRecorder.start(); this.isRecording = true; this.recordingTime = 0; this.timerInterval = setInterval(() => this.recordingTime++, 1000); } catch (e) {} },
        stopRecording() { if (this.mediaRecorder?.state !== 'inactive') this.mediaRecorder.stop(); },
        discardRecording() { this.audioBlob = null; this.isRecording = false; clearInterval(this.timerInterval); },
        toggleAudioPlayback() { if (this.isPlayingAudio && this.audioPlayer) { this.audioPlayer.pause(); this.isPlayingAudio = false; return; } if (!this.audioPlayer && this.audioBlob) { this.audioPlayer = new Audio(URL.createObjectURL(this.audioBlob)); this.audioPlayer.onended = () => this.isPlayingAudio = false; } if (this.audioPlayer) { this.audioPlayer.play(); this.isPlayingAudio = true; } },
        formatTime(s) { const m = Math.floor(s / 60); const ss = s % 60; return `${m.toString().padStart(2, '0')}:${ss.toString().padStart(2, '0')}`; },
        async processAudio(type) { if (!this.audioBlob) return; this.isProcessing = true; const fd = new FormData(); fd.append('audio', this.audioBlob, 'recording.webm'); fd.append('type', type); try { const r = await fetch('/tasks/voice-to-intelligence', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: fd }); const d = await r.json(); if (d.success) { window.playTaskSound('success'); this.discardRecording(); this.fetchData(); } } catch (e) {} finally { this.isProcessing = false; } },
        async saveAudio() { if (!this.audioBlob) return; this.isProcessing = true; const fd = new FormData(); fd.append('audio', this.audioBlob, 'recording.webm'); fd.append('title', 'Voice Note'); try { const r = await fetch('/tasks/voice-save', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: fd }); const d = await r.json(); if (d.success) { window.playTaskSound('success'); this.discardRecording(); } } catch (e) {} finally { this.isProcessing = false; } },
        startGhostRecording() { this.ghostEvents = []; this.isGhostRecording = true; this.isOpen = false; this.stopFn = rrweb.record({ emit: (e) => this.ghostEvents.push(e), recordCanvas: true }); },
        stopGhostRecording() { if (this.stopFn) this.stopFn(); this.isGhostRecording = false; this.isOpen = true; this.saveGhostRecording(); },
        async saveGhostRecording() { if (this.ghostEvents.length < 2) return; try { const r = await fetch('/tasks/ghost-demo', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: JSON.stringify({ title: 'Demo', events: this.ghostEvents }) }); const d = await r.json(); if(d.success) { this.ghostDemos.unshift(d.demo); window.playTaskSound('success'); } } catch(e) {} },
        playDemo(id) { window.open('/tasks/ghost-demo/' + id, '_blank'); },
        formatDate(d) { if (!d) return ''; return new Date(d).toLocaleDateString(); },
        formatDateTimeShort(d) { if (!d) return ''; return new Date(d).toLocaleString([], { month: 'numeric', day: 'numeric', hour: '2-digit', minute: '2-digit' }); },
        isOverdue(d) { if (!d) return false; return new Date(d) < new Date(); }
    }));
});
</script>