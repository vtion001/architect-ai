/**
 * Note/Task Widget Alpine.js Component
 * 
 * ISOLATED: This file contains ONLY the widget's business logic.
 * The UI layout and positioning are defined in Blade partials.
 * 
 * Changes to this file will NOT affect:
 * - Floating button position (fixed at bottom: 96px, right: 24px)
 * - Popup container position (fixed at bottom: 24px, right: 94px)
 * - Widget sizing (fixed at 380px x 580px)
 * - Tab navigation layout
 * 
 * Architecture:
 * ┌─────────────────────────────────────────────────────────────┐
 * │                    UI LAYER (Blade)                         │
 * │  ┌─────────────────┐  ┌─────────────────┐                  │
 * │  │floating-button  │  │popup-container  │                  │
 * │  │(position: fixed)│  │(position: fixed)│                  │
 * │  └─────────────────┘  └─────────────────┘                  │
 * │                              │                              │
 * │  ┌──────────────────────────┴───────────────────────────┐  │
 * │  │ popup-header │ tab-navigation │ *-tab.blade.php       │  │
 * │  └──────────────────────────────────────────────────────┘  │
 * └─────────────────────────────────────────────────────────────┘
 *                              │
 *                              ▼
 * ┌─────────────────────────────────────────────────────────────┐
 * │                   LOGIC LAYER (This File)                   │
 * │                                                             │
 * │  - State management (tasks, notes, recordings)              │
 * │  - API calls (fetch, save, delete)                          │
 * │  - Audio recording                                          │
 * │  - Ghost recording (rrweb)                                  │
 * │  - AI breakdown generation                                  │
 * └─────────────────────────────────────────────────────────────┘
 */

// =============================================================================
// Global Audio Context (Singleton)
// =============================================================================
if (typeof window.taskAudioContext === 'undefined') {
    window.taskAudioContext = null;

    window.initTaskAudio = function () {
        if (!window.taskAudioContext) {
            window.taskAudioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (window.taskAudioContext.state === 'suspended') {
            window.taskAudioContext.resume();
        }
    };

    window.playTaskSound = function (type) {
        window.initTaskAudio();
        const ctx = window.taskAudioContext;
        if (!ctx) return;
        if (ctx.state === 'suspended') ctx.resume();

        try {
            const osc = ctx.createOscillator();
            const gn = ctx.createGain();
            osc.connect(gn);
            gn.connect(ctx.destination);
            const now = ctx.currentTime;

            if (type === 'add') {
                osc.type = 'square';
                osc.frequency.setValueAtTime(600, now);
                osc.frequency.exponentialRampToValueAtTime(100, now + 0.1);
                gn.gain.setValueAtTime(0.1, now);
                gn.gain.exponentialRampToValueAtTime(0.001, now + 0.1);
                osc.start(now);
                osc.stop(now + 0.1);
            } else if (type === 'success') {
                osc.type = 'sine';
                osc.frequency.setValueAtTime(1200, now);
                gn.gain.setValueAtTime(0.2, now);
                gn.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                osc.start(now);
                osc.stop(now + 0.5);
            } else if (type === 'alarm') {
                osc.type = 'sawtooth';
                osc.frequency.setValueAtTime(800, now);
                gn.gain.setValueAtTime(0.2, now);
                gn.gain.setValueAtTime(0.2, now + 0.1);
                gn.gain.linearRampToValueAtTime(0, now + 0.4);
                osc.start(now);
                osc.stop(now + 0.4);
            }
        } catch (e) {
            console.warn('Audio playback failed:', e);
        }
    };

    // Initialize on user interaction
    document.addEventListener('click', window.initTaskAudio);
    document.addEventListener('keydown', window.initTaskAudio);
}

// =============================================================================
// Alpine Component Registration
// =============================================================================
document.addEventListener('alpine:init', () => {
    Alpine.data('noteTaskWidget', () => ({
        // =====================================================================
        // State
        // =====================================================================
        isOpen: false,
        activeTab: 'tasks',
        showSearch: false,
        searchQuery: '',

        // Data
        tasks: [],
        notes: [],
        history: [],
        categories: [],

        // Task Form
        newTaskTitle: '',
        newTaskDate: '',
        selectedCategory: null,
        alarmEnabled: false,

        // Category Form
        newCategoryName: '',
        newCategoryColor: '#3b82f6',

        // AI Breakdown
        openBreakdownModal: false,
        breakdownPrompt: '',
        breakdownSteps: [],
        generatedTitle: '',
        isGenerating: false,

        // Task Details
        viewingTask: null,

        // Voice Recording
        isRecording: false,
        recordingTime: 0,
        audioBlob: null,
        isProcessing: false,
        mediaRecorder: null,
        audioChunks: [],
        timerInterval: null,
        recordingTitle: '',
        recordingDescription: '',
        isPlayingAudio: false,
        audioPlayer: null,
        availableMicrophones: [],
        selectedMicrophoneId: 'default',

        // Ghost Studio
        isGhostRecording: false,
        ghostEvents: [],
        ghostDemos: [],
        stopFn: null,

        // =====================================================================
        // Computed Properties
        // =====================================================================
        get pendingCount() {
            return this.tasks.filter(t => t.status !== 'completed').length;
        },

        get filteredTasks() {
            if (!this.searchQuery.trim()) return this.tasks;
            const q = this.searchQuery.toLowerCase();
            return this.tasks.filter(t => t.title.toLowerCase().includes(q));
        },

        get filteredNotes() {
            if (!this.searchQuery.trim()) return this.notes;
            const q = this.searchQuery.toLowerCase();
            return this.notes.filter(n =>
                n.title.toLowerCase().includes(q) ||
                (n.description && n.description.toLowerCase().includes(q))
            );
        },

        // =====================================================================
        // Lifecycle
        // =====================================================================
        init() {
            this.fetchData();
            this.getMicrophones();

            // Refresh icons when opened
            if (window.lucide) window.lucide.createIcons();

            this.$watch('isOpen', v => {
                if (v && window.lucide) {
                    setTimeout(() => lucide.createIcons(), 100);
                }
            });

            this.$watch('activeTab', v => {
                if (v === 'history') this.fetchHistory();
                if (v === 'voice') this.getMicrophones();
                if (window.lucide) setTimeout(() => lucide.createIcons(), 50);
            });

            // Check alarms every 30 seconds
            setInterval(() => this.checkAlarms(), 30000);
        },

        // =====================================================================
        // Alarm System
        // =====================================================================
        checkAlarms() {
            const now = new Date();
            this.tasks.forEach(t => {
                if (t.alarm_enabled && t.due_date && t.status !== 'completed') {
                    const d = new Date(t.due_date);
                    if (now - d >= 0 && now - d < 60000) {
                        this.triggerAlarm(t);
                    }
                }
            });
        },

        triggerAlarm(t) {
            window.playTaskSound('alarm');
            if (Notification.permission === "granted") {
                new Notification("Task Due: " + t.title);
            }
        },

        // =====================================================================
        // Data Fetching
        // =====================================================================
        async fetchData() {
            try {
                const r = await fetch('/tasks');
                const d = await r.json();
                if (d.categories) this.categories = d.categories;
                if (d.tasks) {
                    this.tasks = d.tasks.filter(t => t.type === 'task');
                    this.notes = d.tasks.filter(t => t.type === 'note');
                }
            } catch (e) {
                console.error('Failed to fetch tasks:', e);
            }
        },

        async fetchHistory() {
            try {
                const r = await fetch('/tasks?trashed=1');
                const d = await r.json();
                if (d.tasks) this.history = d.tasks;
            } catch (e) {
                console.error('Failed to fetch history:', e);
            }
        },

        // =====================================================================
        // Task Operations
        // =====================================================================
        async addTask(title = null, parentId = null, skipRefresh = false) {
            const t = title || this.newTaskTitle;
            if (!t?.trim()) return;

            const p = {
                title: t.trim(),
                type: 'task',
                parent_id: parentId,
                category_id: this.selectedCategory?.id,
                due_date: this.newTaskDate || null,
                alarm_enabled: this.alarmEnabled
            };

            try {
                const r = await fetch('/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(p)
                });
                const d = await r.json();

                if (d.success) {
                    window.playTaskSound('add');
                    if (!parentId) {
                        this.tasks.unshift(d.task);
                        this.newTaskTitle = '';
                        this.alarmEnabled = false;
                    } else if (!skipRefresh) {
                        await this.fetchData();
                        if (this.viewingTask?.id === parentId) {
                            this.viewingTask = this.tasks.find(x => x.id === parentId);
                        }
                    }
                }
            } catch (e) {
                console.error('Failed to add task:', e);
            }
        },

        async toggleTask(t) {
            const s = t.status === 'completed' ? 'pending' : 'completed';
            t.status = s;
            if (s === 'completed') window.playTaskSound('success');

            try {
                await fetch(`/tasks/${t.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: s })
                });
            } catch (e) {
                // Revert on failure
                t.status = t.status === 'completed' ? 'pending' : 'completed';
            }
        },

        async deleteTask(id) {
            if (!confirm('Move to history?')) return;

            try {
                await fetch(`/tasks/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                this.fetchData();
                if (this.viewingTask?.id === id) this.closeTaskDetails();
            } catch (e) {
                console.error('Failed to delete task:', e);
            }
        },

        async updateTaskTitle(t, n) {
            if (!n.trim()) return;

            try {
                await fetch(`/tasks/${t.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title: n })
                });
            } catch (e) {
                console.error('Failed to update task title:', e);
            }
        },

        async updateTaskField(t, f, v) {
            try {
                await fetch(`/tasks/${t.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ [f]: v })
                });
            } catch (e) {
                console.error('Failed to update task field:', e);
            }
        },

        openTaskDetails(t) {
            this.viewingTask = t;
        },

        closeTaskDetails() {
            this.viewingTask = null;
        },

        // =====================================================================
        // Note Operations
        // =====================================================================
        async addNote() {
            try {
                const r = await fetch('/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title: 'Untitled Note', type: 'note' })
                });
                const d = await r.json();
                if (d.success) this.notes.unshift(d.task);
            } catch (e) {
                console.error('Failed to add note:', e);
            }
        },

        async updateNote(n) {
            try {
                await fetch(`/tasks/${n.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title: n.title, description: n.description })
                });
            } catch (e) {
                console.error('Failed to update note:', e);
            }
        },

        // =====================================================================
        // Category Operations
        // =====================================================================
        async addCategory() {
            if (!this.newCategoryName.trim()) return;

            try {
                const r = await fetch('/task-categories', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        name: this.newCategoryName,
                        color: this.newCategoryColor
                    })
                });
                const d = await r.json();
                if (d.success) {
                    this.categories.push(d.category);
                    this.newCategoryName = '';
                    this.selectedCategory = d.category;
                }
            } catch (e) {
                console.error('Failed to add category:', e);
            }
        },

        async deleteCategory(id) {
            try {
                await fetch(`/task-categories/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                this.categories = this.categories.filter(c => c.id !== id);
                this.fetchData();
            } catch (e) {
                console.error('Failed to delete category:', e);
            }
        },

        // =====================================================================
        // History Operations
        // =====================================================================
        async restoreItem(id) {
            try {
                await fetch(`/tasks/${id}/restore`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                this.fetchHistory();
                this.fetchData();
            } catch (e) {
                console.error('Failed to restore item:', e);
            }
        },

        async forceDeleteItem(id) {
            if (!confirm('Permanently delete?')) return;

            try {
                await fetch(`/tasks/${id}/force`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                this.fetchHistory();
            } catch (e) {
                console.error('Failed to force delete:', e);
            }
        },

        // =====================================================================
        // AI Breakdown
        // =====================================================================
        async generateBreakdown() {
            if (this.breakdownSteps.length > 0) {
                // Save the breakdown
                const pt = this.generatedTitle ||
                    (this.breakdownPrompt.length > 50
                        ? this.breakdownPrompt.substring(0, 47) + '...'
                        : this.breakdownPrompt);

                try {
                    const r = await fetch('/tasks', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            title: pt,
                            description: this.breakdownPrompt,
                            type: 'task',
                            category_id: this.selectedCategory?.id,
                            due_date: this.newTaskDate || null,
                            alarm_enabled: this.alarmEnabled
                        })
                    });
                    const d = await r.json();

                    if (d.success) {
                        window.playTaskSound('add');
                        for (const s of this.breakdownSteps) {
                            await this.addTask(s, d.task.id, true);
                        }
                        this.breakdownSteps = [];
                        this.breakdownPrompt = '';
                        this.openBreakdownModal = false;
                        this.fetchData();
                    }
                } catch (e) {
                    console.error('Failed to save breakdown:', e);
                }
                return;
            }

            // Generate breakdown via AI
            this.isGenerating = true;
            try {
                const r = await fetch('/tasks/breakdown', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ content: this.breakdownPrompt })
                });
                const d = await r.json();
                if (d.success) {
                    this.breakdownSteps = d.steps;
                    this.generatedTitle = d.title;
                }
            } catch (e) {
                console.error('Failed to generate breakdown:', e);
            } finally {
                this.isGenerating = false;
            }
        },

        // =====================================================================
        // Voice Recording
        // =====================================================================
        async getMicrophones() {
            if (!navigator.mediaDevices) return;

            try {
                let d = await navigator.mediaDevices.enumerateDevices();
                this.availableMicrophones = d.filter(x => x.kind === 'audioinput');
            } catch (e) {
                console.error('Failed to get microphones:', e);
            }
        },

        async startRecording() {
            try {
                const s = await navigator.mediaDevices.getUserMedia({
                    audio: {
                        deviceId: this.selectedMicrophoneId !== 'default'
                            ? { exact: this.selectedMicrophoneId }
                            : undefined
                    }
                });

                this.mediaRecorder = new MediaRecorder(s);
                this.audioChunks = [];

                this.mediaRecorder.ondataavailable = (e) => {
                    if (e.data.size > 0) this.audioChunks.push(e.data);
                };

                this.mediaRecorder.onstop = () => {
                    this.audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                    this.isRecording = false;
                    clearInterval(this.timerInterval);
                    s.getTracks().forEach(t => t.stop());
                };

                this.mediaRecorder.start();
                this.isRecording = true;
                this.recordingTime = 0;
                this.timerInterval = setInterval(() => this.recordingTime++, 1000);
            } catch (e) {
                console.error('Failed to start recording:', e);
            }
        },

        stopRecording() {
            if (this.mediaRecorder?.state !== 'inactive') {
                this.mediaRecorder.stop();
            }
        },

        discardRecording() {
            this.audioBlob = null;
            this.isRecording = false;
            clearInterval(this.timerInterval);
        },

        toggleAudioPlayback() {
            if (this.isPlayingAudio && this.audioPlayer) {
                this.audioPlayer.pause();
                this.isPlayingAudio = false;
                return;
            }

            if (!this.audioPlayer && this.audioBlob) {
                this.audioPlayer = new Audio(URL.createObjectURL(this.audioBlob));
                this.audioPlayer.onended = () => this.isPlayingAudio = false;
            }

            if (this.audioPlayer) {
                this.audioPlayer.play();
                this.isPlayingAudio = true;
            }
        },

        formatTime(s) {
            const m = Math.floor(s / 60);
            const ss = s % 60;
            return `${m.toString().padStart(2, '0')}:${ss.toString().padStart(2, '0')}`;
        },

        async processAudio(type) {
            if (!this.audioBlob) return;

            this.isProcessing = true;
            const fd = new FormData();
            fd.append('audio', this.audioBlob, 'recording.webm');
            fd.append('type', type);

            try {
                const r = await fetch('/tasks/voice-to-intelligence', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: fd
                });
                const d = await r.json();
                if (d.success) {
                    window.playTaskSound('success');
                    this.discardRecording();
                    this.fetchData();
                }
            } catch (e) {
                console.error('Failed to process audio:', e);
            } finally {
                this.isProcessing = false;
            }
        },

        async saveAudio() {
            if (!this.audioBlob) return;

            this.isProcessing = true;
            const fd = new FormData();
            fd.append('audio', this.audioBlob, 'recording.webm');
            fd.append('title', 'Voice Note');

            try {
                const r = await fetch('/tasks/voice-save', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: fd
                });
                const d = await r.json();
                if (d.success) {
                    window.playTaskSound('success');
                    this.discardRecording();
                }
            } catch (e) {
                console.error('Failed to save audio:', e);
            } finally {
                this.isProcessing = false;
            }
        },

        // =====================================================================
        // Ghost Studio
        // =====================================================================
        startGhostRecording() {
            this.ghostEvents = [];
            this.isGhostRecording = true;
            this.isOpen = false;
            this.stopFn = rrweb.record({
                emit: (e) => this.ghostEvents.push(e),
                recordCanvas: true
            });
        },

        stopGhostRecording() {
            if (this.stopFn) this.stopFn();
            this.isGhostRecording = false;
            this.isOpen = true;
            this.saveGhostRecording();
        },

        async saveGhostRecording() {
            if (this.ghostEvents.length < 2) return;

            try {
                const r = await fetch('/tasks/ghost-demo', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title: 'Demo', events: this.ghostEvents })
                });
                const d = await r.json();
                if (d.success) {
                    this.ghostDemos.unshift(d.demo);
                    window.playTaskSound('success');
                }
            } catch (e) {
                console.error('Failed to save ghost recording:', e);
            }
        },

        playDemo(id) {
            window.open('/tasks/ghost-demo/' + id, '_blank');
        },

        // =====================================================================
        // Utilities
        // =====================================================================
        formatDate(d) {
            if (!d) return '';
            return new Date(d).toLocaleDateString();
        },

        formatDateTimeShort(d) {
            if (!d) return '';
            return new Date(d).toLocaleString([], {
                month: 'numeric',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },

        isOverdue(d) {
            if (!d) return false;
            return new Date(d) < new Date();
        }
    }));
});
