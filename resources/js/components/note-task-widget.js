/**
 * Note/Task Widget Alpine.js Component
 * 
 * Handles multi-tab widget functionality:
 * - Tasks management (add, toggle, delete, breakdown)
 * - Notes management
 * - Voice recording and transcription
 * - Ghost Studio recording
 * - History/archive management
 */

// Global Audio Context Handler
if (typeof window.taskAudioContext === 'undefined') {
    window.taskAudioContext = null;
}

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
        const oscillator = ctx.createOscillator();
        const gainNode = ctx.createGain();
        oscillator.connect(gainNode);
        gainNode.connect(ctx.destination);
        const now = ctx.currentTime;

        if (type === 'add') {
            oscillator.type = 'square';
            oscillator.frequency.setValueAtTime(600, now);
            oscillator.frequency.exponentialRampToValueAtTime(100, now + 0.1);
            gainNode.gain.setValueAtTime(0.1, now);
            gainNode.gain.exponentialRampToValueAtTime(0.001, now + 0.1);
            oscillator.start(now);
            oscillator.stop(now + 0.1);
        } else if (type === 'success') {
            oscillator.type = 'sine';
            oscillator.frequency.setValueAtTime(1200, now);
            gainNode.gain.setValueAtTime(0.2, now);
            gainNode.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
            oscillator.start(now);
            oscillator.stop(now + 0.5);
        } else if (type === 'alarm') {
            oscillator.type = 'sawtooth';
            oscillator.frequency.setValueAtTime(800, now);
            gainNode.gain.setValueAtTime(0.2, now);
            gainNode.gain.setValueAtTime(0.2, now + 0.1);
            gainNode.gain.linearRampToValueAtTime(0, now + 0.4);
            oscillator.start(now);
            oscillator.stop(now + 0.4);
        }
    } catch (e) {
        console.error("Sound error:", e);
    }
};

// Initialize audio on first user interaction
document.addEventListener('click', window.initTaskAudio);
document.addEventListener('keydown', window.initTaskAudio);

/**
 * Creates the Note/Task Widget Alpine.js component
 * @returns {Object} - Alpine.js component data object
 */
export function createNoteTaskWidgetComponent() {
    return {
        isOpen: false,
        activeTab: 'tasks',
        tasks: [],
        notes: [],
        history: [],
        categories: [],
        newTaskTitle: '',
        newTaskDate: '',
        selectedCategory: null,
        alarmEnabled: false,
        newCategoryName: '',
        newCategoryColor: '#3b82f6',
        openBreakdownModal: false,
        breakdownPrompt: '',
        breakdownSteps: [],
        generatedTitle: '',
        isGenerating: false,
        viewingTask: null,
        showSearch: false,
        searchQuery: '',
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
        isGhostRecording: false,
        ghostEvents: [],
        ghostDemos: [],
        stopFn: null,

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
            return this.notes.filter(n => n.title.toLowerCase().includes(q) || (n.description && n.description.toLowerCase().includes(q)));
        },

        init() {
            this.fetchData();
            this.getMicrophones();
            if (window.lucide) window.lucide.createIcons();
            this.$watch('isOpen', value => {
                if (value && window.lucide) setTimeout(() => lucide.createIcons(), 100);
            });
            this.$watch('activeTab', value => {
                if (value === 'history') this.fetchHistory();
                if (value === 'voice') this.getMicrophones();
                if (window.lucide) setTimeout(() => lucide.createIcons(), 50);
            });
            setInterval(() => { this.checkAlarms(); }, 30000);
        },

        checkAlarms() {
            const now = new Date();
            this.tasks.forEach(task => {
                if (task.alarm_enabled && task.due_date && task.status !== 'completed') {
                    const dueDate = new Date(task.due_date);
                    if (now - dueDate >= 0 && now - dueDate < 60000) this.triggerAlarm(task);
                }
            });
        },

        triggerAlarm(task) {
            window.playTaskSound('alarm');
            if (Notification.permission === "granted") {
                new Notification("Task Due: " + task.title);
            }
        },

        async fetchData() {
            try {
                const res = await fetch('/tasks');
                const data = await res.json();
                if (data.categories) this.categories = data.categories;
                if (data.tasks) {
                    this.tasks = data.tasks.filter(t => t.type === 'task');
                    this.notes = data.tasks.filter(t => t.type === 'note');
                }
            } catch (e) {
                console.error('Failed to fetch data', e);
            }
        },

        async fetchHistory() {
            try {
                const res = await fetch('/tasks?trashed=1');
                const data = await res.json();
                if (data.tasks) this.history = data.tasks;
            } catch (e) {
                console.error(e);
            }
        },

        async restoreItem(id) {
            try {
                await fetch(`/tasks/${id}/restore`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                this.fetchHistory();
                this.fetchData();
            } catch (e) {
                console.error(e);
            }
        },

        async forceDeleteItem(id) {
            if (!confirm('Permanently delete?')) return;
            try {
                await fetch(`/tasks/${id}/force`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                this.fetchHistory();
            } catch (e) {
                console.error(e);
            }
        },

        async addTask(title = null, parentId = null, skipRefresh = false) {
            const taskTitle = title || this.newTaskTitle;
            if (!taskTitle || !taskTitle.trim()) return;
            const payload = {
                title: taskTitle.trim(),
                type: 'task',
                parent_id: parentId,
                category_id: this.selectedCategory ? this.selectedCategory.id : null,
                due_date: this.newTaskDate || null,
                alarm_enabled: this.alarmEnabled
            };
            try {
                const res = await fetch('/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.success) {
                    window.playTaskSound('add');
                    if (!parentId) {
                        this.tasks.unshift(data.task);
                        this.newTaskTitle = '';
                        this.alarmEnabled = false;
                    } else if (!skipRefresh) {
                        await this.fetchData();
                        if (this.viewingTask && this.viewingTask.id === parentId) {
                            this.viewingTask = this.tasks.find(t => t.id === parentId);
                        }
                    }
                }
            } catch (e) {
                console.error(e);
            }
        },

        openTaskDetails(task) {
            this.viewingTask = task;
        },

        closeTaskDetails() {
            this.viewingTask = null;
        },

        async updateTaskTitle(task, newTitle) {
            if (!newTitle.trim()) return;
            try {
                await fetch(`/tasks/${task.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title: newTitle })
                });
            } catch (e) { }
        },

        async updateTaskField(task, field, value) {
            try {
                await fetch(`/tasks/${task.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ [field]: value })
                });
            } catch (e) { }
        },

        async addNote() {
            try {
                const res = await fetch('/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title: 'Untitled Note', type: 'note' })
                });
                const data = await res.json();
                if (data.success) this.notes.unshift(data.task);
            } catch (e) { }
        },

        async addCategory() {
            if (!this.newCategoryName.trim()) return;
            try {
                const res = await fetch('/task-categories', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ name: this.newCategoryName, color: this.newCategoryColor })
                });
                const data = await res.json();
                if (data.success) {
                    this.categories.push(data.category);
                    this.newCategoryName = '';
                    this.selectedCategory = data.category;
                }
            } catch (e) { }
        },

        async deleteCategory(id) {
            try {
                await fetch(`/task-categories/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                this.categories = this.categories.filter(c => c.id !== id);
                this.fetchData();
            } catch (e) { }
        },

        async updateNote(note) {
            try {
                await fetch(`/tasks/${note.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title: note.title, description: note.description })
                });
            } catch (e) { }
        },

        async toggleTask(task) {
            const newStatus = task.status === 'completed' ? 'pending' : 'completed';
            task.status = newStatus;
            if (newStatus === 'completed') window.playTaskSound('success');
            try {
                await fetch(`/tasks/${task.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ status: newStatus })
                });
            } catch (e) {
                task.status = task.status === 'completed' ? 'pending' : 'completed';
            }
        },

        async deleteTask(id) {
            if (!confirm('Move to history?')) return;
            try {
                await fetch(`/tasks/${id}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                });
                this.fetchData();
                if (this.viewingTask && this.viewingTask.id === id) this.closeTaskDetails();
            } catch (e) { }
        },

        async generateBreakdown() {
            if (this.breakdownSteps.length > 0) {
                const parentTitle = this.generatedTitle || (this.breakdownPrompt.length > 50 ? this.breakdownPrompt.substring(0, 47) + '...' : this.breakdownPrompt);
                try {
                    const res = await fetch('/tasks', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            title: parentTitle,
                            description: this.breakdownPrompt,
                            type: 'task',
                            category_id: this.selectedCategory ? this.selectedCategory.id : null,
                            due_date: this.newTaskDate || null,
                            alarm_enabled: this.alarmEnabled
                        })
                    });
                    const data = await res.json();
                    if (data.success) {
                        window.playTaskSound('add');
                        for (const step of this.breakdownSteps) {
                            await this.addTask(step, data.task.id, true);
                        }
                        this.breakdownSteps = [];
                        this.breakdownPrompt = '';
                        this.openBreakdownModal = false;
                        this.fetchData();
                    }
                } catch (e) { }
                return;
            }
            this.isGenerating = true;
            try {
                const res = await fetch('/tasks/breakdown', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ content: this.breakdownPrompt })
                });
                const data = await res.json();
                if (data.success) {
                    this.breakdownSteps = data.steps;
                    this.generatedTitle = data.title;
                }
            } catch (e) { } finally {
                this.isGenerating = false;
            }
        },

        async getMicrophones() {
            if (!navigator.mediaDevices) return;
            try {
                let devices = await navigator.mediaDevices.enumerateDevices();
                this.availableMicrophones = devices.filter(device => device.kind === 'audioinput');
            } catch (err) { }
        },

        async startRecording() {
            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    audio: {
                        deviceId: this.selectedMicrophoneId !== 'default' ? { exact: this.selectedMicrophoneId } : undefined
                    }
                });
                this.mediaRecorder = new MediaRecorder(stream);
                this.audioChunks = [];
                this.mediaRecorder.ondataavailable = (e) => {
                    if (e.data.size > 0) this.audioChunks.push(e.data);
                };
                this.mediaRecorder.onstop = () => {
                    this.audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                    this.isRecording = false;
                    clearInterval(this.timerInterval);
                    stream.getTracks().forEach(t => t.stop());
                };
                this.mediaRecorder.start();
                this.isRecording = true;
                this.recordingTime = 0;
                this.timerInterval = setInterval(() => { this.recordingTime++; }, 1000);
            } catch (err) { }
        },

        stopRecording() {
            if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') this.mediaRecorder.stop();
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
                this.audioPlayer.onended = () => { this.isPlayingAudio = false; };
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
            const formData = new FormData();
            formData.append('audio', this.audioBlob, 'recording.webm');
            formData.append('type', type);
            try {
                const res = await fetch('/tasks/voice-to-intelligence', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    window.playTaskSound('success');
                    this.discardRecording();
                    this.fetchData();
                }
            } catch (e) { } finally {
                this.isProcessing = false;
            }
        },

        async saveAudio() {
            if (!this.audioBlob) return;
            this.isProcessing = true;
            const formData = new FormData();
            formData.append('audio', this.audioBlob, 'recording.webm');
            formData.append('title', 'Voice Note');
            try {
                const res = await fetch('/tasks/voice-save', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    window.playTaskSound('success');
                    this.discardRecording();
                }
            } catch (e) { } finally {
                this.isProcessing = false;
            }
        },

        startGhostRecording() {
            this.ghostEvents = [];
            this.isGhostRecording = true;
            this.isOpen = false;
            this.stopFn = rrweb.record({
                emit: (e) => { this.ghostEvents.push(e); },
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
                const res = await fetch('/tasks/ghost-demo', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ title: 'Demo', events: this.ghostEvents })
                });
                const data = await res.json();
                if (data.success) {
                    this.ghostDemos.unshift(data.demo);
                    window.playTaskSound('success');
                }
            } catch (e) { }
        },

        playDemo(id) {
            window.open('/tasks/ghost-demo/' + id, '_blank');
        },

        formatDate(d) {
            if (!d) return '';
            return new Date(d).toLocaleDateString();
        },

        formatDateTimeShort(d) {
            if (!d) return '';
            return new Date(d).toLocaleString([], { month: 'numeric', day: 'numeric', hour: '2-digit', minute: '2-digit' });
        },

        isOverdue(d) {
            if (!d) return false;
            return new Date(d) < new Date();
        }
    };
}

// Global registration for Alpine.js
if (typeof window !== 'undefined') {
    document.addEventListener('alpine:init', () => {
        Alpine.data('noteTaskWidget', () => createNoteTaskWidgetComponent());
    });
}
