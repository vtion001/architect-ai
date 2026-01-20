{{-- 
    Note/Task Widget Component (Modularized)

    ARCHITECTURE:
    ┌─────────────────────────────────────────────────────────────┐
    │                    UI LAYER (Blade)                         │
    │                                                             │
    │  ┌─────────────────┐    ┌────────────────────────────────┐ │
    │  │ floating-button │    │ popup-container                │ │
    │  │ (FIXED POSITION)│    │ (FIXED POSITION)               │ │
    │  │ bottom: 96px    │    │ bottom: 24px, right: 94px      │ │
    │  │ right: 24px     │    │                                │ │
    │  └─────────────────┘    │  ┌──────────────────────────┐  │ │
    │                         │  │ popup-header             │  │ │
    │                         │  │ - Title                  │  │ │
    │                         │  │ - tab-navigation         │  │ │
    │                         │  └──────────────────────────┘  │ │
    │                         │                                │ │
    │                         │  ┌──────────────────────────┐  │ │
    │                         │  │ Tab Content (Partials)   │  │ │
    │                         │  │ - tasks-tab              │  │ │
    │                         │  │ - notes-tab              │  │ │
    │                         │  │ - voice-tab              │  │ │
    │                         │  │ - studio-tab             │  │ │
    │                         │  │ - history-tab            │  │ │
    │                         │  └──────────────────────────┘  │ │
    │                         └────────────────────────────────┘ │
    └─────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
    ┌─────────────────────────────────────────────────────────────┐
    │                   LOGIC LAYER (JavaScript)                  │
    │                                                             │
    │  /resources/js/components/note-task-widget.js               │
    │  - State management                                         │
    │  - API calls                                                │
    │  - Audio recording                                          │
    │  - Ghost recording                                          │
    └─────────────────────────────────────────────────────────────┘

    ISOLATION GUARANTEE:
    - Changes to JavaScript logic will NOT affect UI positions
    - Changes to tab content will NOT affect header or floating button
    - The floating button is ALWAYS at bottom: 96px, right: 24px
    - The popup container is ALWAYS at bottom: 24px, right: 94px
--}}

<div x-data="noteTaskWidget()" 
     x-cloak
     @open-notes.window="isOpen = true; activeTab = 'notes'">

    {{-- Popup Container (Position: FIXED) --}}
    @include('components.widget.popup-container')

    {{-- Floating Toggle Button (Position: FIXED) --}}
    @include('components.widget.floating-button')
</div>

{{-- 
    Widget JavaScript Logic
    All Alpine.js logic is inline below for simplicity and reliability.
    The logic is separated from the UI partials above.
--}}
<script>
// Prevent duplicate registration
if (typeof window._noteTaskWidgetLoaded === 'undefined') {
    window._noteTaskWidgetLoaded = true;
    
    // Global Audio Context Handler
    if (typeof window.taskAudioContext === 'undefined') {
        window.taskAudioContext = null;
        window.initTaskAudio = function() { if (!window.taskAudioContext) window.taskAudioContext = new (window.AudioContext || window.webkitAudioContext)(); if (window.taskAudioContext.state === 'suspended') window.taskAudioContext.resume(); };
        window.playTaskSound = function(type) { window.initTaskAudio(); const ctx = window.taskAudioContext; if (!ctx) return; if (ctx.state === 'suspended') ctx.resume(); try { const osc = ctx.createOscillator(), gn = ctx.createGain(); osc.connect(gn); gn.connect(ctx.destination); const now = ctx.currentTime; if (type === 'add') { osc.type = 'square'; osc.frequency.setValueAtTime(600, now); osc.frequency.exponentialRampToValueAtTime(100, now + 0.1); gn.gain.setValueAtTime(0.1, now); gn.gain.exponentialRampToValueAtTime(0.001, now + 0.1); osc.start(now); osc.stop(now + 0.1); } else if (type === 'success') { osc.type = 'sine'; osc.frequency.setValueAtTime(1200, now); gn.gain.setValueAtTime(0.2, now); gn.gain.exponentialRampToValueAtTime(0.001, now + 0.5); osc.start(now); osc.stop(now + 0.5); } else if (type === 'alarm') { osc.type = 'sawtooth'; osc.frequency.setValueAtTime(800, now); gn.gain.setValueAtTime(0.2, now); gn.gain.setValueAtTime(0.2, now + 0.1); gn.gain.linearRampToValueAtTime(0, now + 0.4); osc.start(now); osc.stop(now + 0.4); } } catch (e) {} };
        document.addEventListener('click', window.initTaskAudio);
        document.addEventListener('keydown', window.initTaskAudio);
    }

    document.addEventListener('alpine:init', () => {
        // Register the noteTaskWidget Alpine component
        if (!Alpine.Components?.has('noteTaskWidget')) {
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
                async processAudio(type) { if (!this.audioBlob) return; this.isProcessing = true; const fd = new FormData(); fd.append('audio', this.audioBlob, 'recording.webm'); fd.append('type', type); try { const r = await fetch('/tasks/voice-to-intelligence', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }, body: fd }); const d = await r.json(); if (d.success) { window.playTaskSound('success'); this.discardRecording(); this.fetchData(); } } catch (e) { console.error('Voice processing error:', e); } finally { this.isProcessing = false; } },
                async saveAudio() { if (!this.audioBlob) return; this.isProcessing = true; const fd = new FormData(); fd.append('audio', this.audioBlob, 'recording.webm'); fd.append('title', 'Voice Note'); try { const r = await fetch('/tasks/voice-save', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }, body: fd }); const d = await r.json(); if (d.success) { window.playTaskSound('success'); this.discardRecording(); } } catch (e) {} finally { this.isProcessing = false; } },
                // Ghost Studio - Feature Disabled (Coming Soon)
                startGhostRecording() { console.log('Ghost Studio: Feature coming soon'); },
                _initRecorder() {},
                stopGhostRecording() {},
                saveGhostRecording() {},
                playDemo(id) {},
                formatDate(d) { if (!d) return ''; return new Date(d).toLocaleDateString(); },
                formatDateTimeShort(d) { if (!d) return ''; return new Date(d).toLocaleString([], { month: 'numeric', day: 'numeric', hour: '2-digit', minute: '2-digit' }); },
                isOverdue(d) { if (!d) return false; return new Date(d) < new Date(); }
            }));
        }
    });
}
</script>