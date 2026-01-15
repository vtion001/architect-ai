<!-- resources/views/components/note-task-widget.blade.php -->
<div x-data="noteTaskWidget()" 
     x-cloak
     @open-notes.window="isOpen = true"
     class="fixed z-[99999]" 
     style="bottom: 96px; right: 24px;"> <!-- Positioned above chat widget -->

    <!-- Panel -->
    <div x-show="isOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-x-4"
         x-transition:enter-end="opacity-100 scale-100 translate-x-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-x-0"
         x-transition:leave-end="opacity-0 scale-95 translate-x-4"
         class="absolute bottom-0 right-16 w-80 sm:w-96 bg-card border border-border rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-[600px] h-[600px]"
         style="bottom: 0; right: 70px;">
         
         <!-- Header -->
         <div class="flex items-center justify-between px-4 py-3 border-b border-border bg-muted/20">
             <div class="flex gap-2 bg-muted rounded-lg p-1">
                 <button @click="activeTab = 'tasks'" 
                         :class="activeTab === 'tasks' ? 'bg-background text-foreground shadow-sm font-bold' : 'text-muted-foreground hover:text-foreground'"
                         class="px-4 py-1.5 rounded-md text-xs uppercase tracking-wider transition-all">Tasks</button>
                 <button @click="activeTab = 'notes'" 
                         :class="activeTab === 'notes' ? 'bg-background text-foreground shadow-sm font-bold' : 'text-muted-foreground hover:text-foreground'"
                         class="px-4 py-1.5 rounded-md text-xs uppercase tracking-wider transition-all">Notes</button>
                 <button @click="activeTab = 'voice'" 
                         :class="activeTab === 'voice' ? 'bg-background text-foreground shadow-sm font-bold' : 'text-muted-foreground hover:text-foreground'"
                         class="px-3 py-1.5 rounded-md text-xs uppercase tracking-wider transition-all" title="Meeting Scribe">
                     <i data-lucide="mic" class="w-3.5 h-3.5"></i>
                 </button>
                 <button @click="activeTab = 'studio'" 
                         :class="activeTab === 'studio' ? 'bg-background text-foreground shadow-sm font-bold' : 'text-muted-foreground hover:text-foreground'"
                         class="px-3 py-1.5 rounded-md text-xs uppercase tracking-wider transition-all" title="Ghost Studio (Demo Recorder)">
                     <i data-lucide="clapperboard" class="w-3.5 h-3.5"></i>
                 </button>
                 <button @click="activeTab = 'history'" 
                         :class="activeTab === 'history' ? 'bg-background text-foreground shadow-sm font-bold' : 'text-muted-foreground hover:text-foreground'"
                         class="px-3 py-1.5 rounded-md text-xs uppercase tracking-wider transition-all" title="Trash / History">
                     <i data-lucide="history" class="w-3.5 h-3.5"></i>
                 </button>
             </div>
             <div class="flex items-center gap-1">
                 <button @click="showSearch = !showSearch" class="p-1.5 hover:bg-muted rounded-md transition-colors text-muted-foreground hover:text-foreground">
                     <i data-lucide="search" class="w-4 h-4"></i>
                 </button>
                 <button @click="isOpen = false" class="p-1.5 hover:bg-muted rounded-md transition-colors text-muted-foreground hover:text-foreground">
                     <i data-lucide="x" class="w-4 h-4"></i>
                 </button>
             </div>
         </div>

         <!-- Search Bar (Optional Toggle) -->
         <div x-show="showSearch" x-transition class="px-4 py-2 border-b border-border bg-card">
             <input type="text" x-model="searchQuery" placeholder="Search tasks & notes..." 
                    class="w-full bg-muted/30 border border-border rounded-lg px-3 py-1.5 text-xs outline-none focus:ring-1 focus:ring-primary/30">
         </div>

         <!-- Content -->
         <div class="flex-1 overflow-y-auto p-4 custom-scrollbar bg-background/50">
             @include('components.widget.tasks-tab')
             @include('components.widget.notes-tab')
             @include('components.widget.voice-tab')
             @include('components.widget.studio-tab')
             @include('components.widget.history-tab')
         </div>
         
         @include('components.widget.widget-modals')
    </div>

    <!-- Trigger Button -->
    <button @click="isOpen = !isOpen" 
            class="w-14 h-14 rounded-full shadow-2xl flex items-center justify-center bg-card border border-border text-foreground transition-all hover:scale-105 active:scale-95 group relative overflow-hidden ring-offset-4 ring-offset-background hover:ring-2 ring-primary/50">
        <div class="absolute inset-0 bg-gradient-to-tr from-primary/10 to-transparent group-hover:from-primary/20 transition-colors"></div>
        <i data-lucide="check-square" class="w-6 h-6 text-primary relative z-10 transition-transform group-hover:-rotate-12"></i>
        <template x-if="pendingCount > 0">
            <span class="absolute top-3 right-3 flex h-2.5 w-2.5">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
            </span>
        </template>
    </button>
</div>

<script>
    // Global Audio Context Handler to ensure unlocking works across all interactions
    window.taskAudioContext = null;
    
    window.initTaskAudio = function() {
        if (!window.taskAudioContext) {
            window.taskAudioContext = new (window.AudioContext || window.webkitAudioContext)();
        }
        if (window.taskAudioContext.state === 'suspended') {
            window.taskAudioContext.resume();
        }
    };

    window.playTaskSound = function(type) {
        window.initTaskAudio();
        const ctx = window.taskAudioContext;
        
        if (!ctx) return;

        // Ensure we try to resume if suspended (e.g. if init failed or timed out)
        if (ctx.state === 'suspended') {
            ctx.resume().catch(e => console.error("Audio resume failed", e));
        }

        try {
            const oscillator = ctx.createOscillator();
            const gainNode = ctx.createGain();

            oscillator.connect(gainNode);
            gainNode.connect(ctx.destination);

            const now = ctx.currentTime;

            if (type === 'add') {
                // "Pop" - Square wave
                oscillator.type = 'square';
                oscillator.frequency.setValueAtTime(600, now);
                oscillator.frequency.exponentialRampToValueAtTime(100, now + 0.1);
                
                gainNode.gain.setValueAtTime(0.1, now);
                gainNode.gain.exponentialRampToValueAtTime(0.001, now + 0.1);
                
                oscillator.start(now);
                oscillator.stop(now + 0.1);
            } else if (type === 'success') {
                // "Ding" - Sine wave with harmonics
                oscillator.type = 'sine';
                oscillator.frequency.setValueAtTime(1200, now);
                
                gainNode.gain.setValueAtTime(0.2, now);
                gainNode.gain.exponentialRampToValueAtTime(0.001, now + 0.5);
                
                oscillator.start(now);
                oscillator.stop(now + 0.5);
                
                // Harmonic
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(2400, now);
                gain2.gain.setValueAtTime(0.1, now);
                gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.4);
                osc2.start(now);
                osc2.stop(now + 0.4);
            } else if (type === 'alarm') {
                // Alarm - Sawtooth
                oscillator.type = 'sawtooth';
                oscillator.frequency.setValueAtTime(800, now);
                
                gainNode.gain.setValueAtTime(0.2, now);
                gainNode.gain.setValueAtTime(0.2, now + 0.1);
                gainNode.gain.linearRampToValueAtTime(0, now + 0.4);
                
                oscillator.start(now);
                oscillator.stop(now + 0.4);
            }
        } catch (e) {
            console.error("Sound playback error:", e);
        }
    };

    document.addEventListener('click', window.initTaskAudio);
    document.addEventListener('keydown', window.initTaskAudio);

    document.addEventListener('alpine:init', () => {
        Alpine.data('noteTaskWidget', () => ({
            isOpen: false,
            // ... (rest of the component) ...
            activeTab: 'tasks',
            tasks: [],
            notes: [],
            history: [],
            categories: [],
            
            // New Task Form
            newTaskTitle: '',
            newTaskDate: '',
            selectedCategory: null,
            alarmEnabled: false,
            
            // Category Management
            newCategoryName: '',
            newCategoryColor: '#3b82f6',

            // AI Breakdown
            openBreakdownModal: false,
            breakdownPrompt: '',
            breakdownSteps: [],
            generatedTitle: '',
            isGenerating: false,
            
            // Viewing Detail
            viewingTask: null,

            // Search
            showSearch: false,
            searchQuery: '',

            // Voice / Meeting Scribe
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

            get pendingCount() {
                return this.tasks.filter(t => t.status !== 'completed').length;
            },

            get filteredTasks() {
                if (!this.searchQuery.trim()) return this.tasks;
                const q = this.searchQuery.toLowerCase();
                return this.tasks.filter(t => 
                    t.title.toLowerCase().includes(q) || 
                    (t.description && t.description.toLowerCase().includes(q))
                );
            },

            get filteredNotes() {
                if (!this.searchQuery.trim()) return this.notes;
                const q = this.searchQuery.toLowerCase();
                return this.notes.filter(n => 
                    n.title.toLowerCase().includes(q) || 
                    (n.description && n.description.toLowerCase().includes(q))
                );
            },

            init() {
                this.fetchData();
                this.getMicrophones(); // Try to fetch mics if permission already granted
                
                this.$watch('isOpen', value => {
                    if (value && window.lucide) {
                        setTimeout(() => lucide.createIcons(), 100);
                    }
                });
                this.$watch('activeTab', value => {
                    if (value === 'history') {
                        this.fetchHistory();
                    }
                    if (value === 'voice') {
                        this.getMicrophones();
                    }
                });
                
                // Alarm Polling every 30 seconds
                setInterval(() => {
                    this.checkAlarms();
                }, 30000);
            },

            checkAlarms() {
                const now = new Date();
                this.tasks.forEach(task => {
                    if (task.alarm_enabled && task.due_date && task.status !== 'completed') {
                        const dueDate = new Date(task.due_date);
                        // Check if due time is within the last minute (to avoid re-triggering old alarms endlessly)
                        const diff = now - dueDate;
                        if (diff >= 0 && diff < 60000) {
                            // Trigger Alarm
                            this.triggerAlarm(task);
                        }
                    }
                });
            },

            triggerAlarm(task) {
                // Play sound using global handler
                window.playTaskSound('alarm');
                
                // Alert user
                if (Notification.permission === "granted") {
                    new Notification("Task Due: " + task.title);
                } else if (Notification.permission !== "denied") {
                    Notification.requestPermission().then(permission => {
                        if (permission === "granted") {
                            new Notification("Task Due: " + task.title);
                        }
                    });
                }
            },

            async fetchData() {
                try {
                    const res = await fetch('/tasks');
                    const data = await res.json();
                    
                    if (data.categories) {
                        this.categories = data.categories;
                    }

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
                    if (data.tasks) {
                        this.history = data.tasks;
                    }
                } catch (e) { console.error(e); }
            },

            async restoreItem(id) {
                if (!confirm('Restore this item?')) return;
                try {
                    await fetch(`/tasks/${id}/restore`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    this.fetchHistory();
                    this.fetchData(); // Refresh main lists
                } catch (e) { console.error(e); }
            },

            async forceDeleteItem(id) {
                if (!confirm('Permanently delete? This cannot be undone.')) return;
                try {
                    await fetch(`/tasks/${id}/force`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    this.fetchHistory();
                } catch (e) { console.error(e); }
            },

            async addTask(title = null, parentId = null, skipRefresh = false) {
                const taskTitle = title || this.newTaskTitle;
                if (!taskTitle || !taskTitle.trim()) return;

                // Format datetime-local string to ISO/MySQL format if needed
                let formattedDate = this.newTaskDate;

                const payload = { 
                    title: taskTitle.trim(), 
                    type: 'task', 
                    parent_id: parentId,
                    category_id: this.selectedCategory ? this.selectedCategory.id : null,
                    due_date: formattedDate || null,
                    alarm_enabled: this.alarmEnabled
                };

                try {
                    const res = await fetch('/tasks', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'Accept': 'application/json',
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
                            // Reset optional fields
                            this.alarmEnabled = false; 
                            // Keep category/date for batch entry comfort? or reset? Let's keep for now.
                        } else if (!skipRefresh) {
                            await this.fetchData();
                            // If we are viewing a task, refresh it
                            if (this.viewingTask && this.viewingTask.id === parentId) {
                                // Re-find updated parent
                                const updatedParent = this.tasks.find(t => t.id === parentId);
                                if(updatedParent) this.viewingTask = updatedParent;
                            }
                        }
                    }
                } catch (e) { 
                    console.error(e); 
                }
            },

            openTaskDetails(task) {
                // Clone task to avoid direct mutation issues before save, or just use reference
                // Using reference allows immediate UI updates
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
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ title: newTitle })
                    });
                } catch (e) { console.error(e); }
            },

            async updateTaskField(task, field, value) {
                try {
                    await fetch(`/tasks/${task.id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ [field]: value })
                    });
                } catch (e) { console.error(e); }
            },

            async addNote() {
                try {
                    const res = await fetch('/tasks', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json', 
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                        },
                        body: JSON.stringify({ title: 'Untitled Note', type: 'note' })
                    });
                    
                    const data = await res.json();
                    if (data.success) {
                        this.notes.unshift(data.task);
                    }
                } catch (e) { 
                    console.error(e); 
                }
            },

            async addCategory() {
                if (!this.newCategoryName.trim()) return;
                try {
                    const res = await fetch('/task-categories', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ name: this.newCategoryName, color: this.newCategoryColor })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.categories.push(data.category);
                        this.newCategoryName = '';
                        this.selectedCategory = data.category;
                    }
                } catch (e) { console.error(e); }
            },

            async deleteCategory(id) {
                if (!confirm('Delete this category?')) return;
                try {
                    await fetch(`/task-categories/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    this.categories = this.categories.filter(c => c.id !== id);
                    if (this.selectedCategory && this.selectedCategory.id === id) {
                        this.selectedCategory = null;
                    }
                    this.fetchData();
                } catch (e) { console.error(e); }
            },

            async updateNote(note) {
                try {
                    await fetch(`/tasks/${note.id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ title: note.title, description: note.description })
                    });
                } catch (e) { console.error(e); }
            },

            async toggleTask(task) {
                const newStatus = task.status === 'completed' ? 'pending' : 'completed';
                task.status = newStatus;
                
                if (newStatus === 'completed') {
                    window.playTaskSound('success');
                }

                try {
                    await fetch(`/tasks/${task.id}`, {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ status: newStatus })
                    });
                } catch (e) { 
                    task.status = task.status === 'completed' ? 'pending' : 'completed';
                }
            },

            async deleteTask(id) {
                if (!confirm('Delete this item? It will be moved to history.')) return;
                
                try {
                    await fetch(`/tasks/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    this.tasks = this.tasks.filter(t => t.id !== id && (!t.parent_id || t.parent_id !== id));
                    this.tasks.forEach(t => {
                         if(t.subtasks) t.subtasks = t.subtasks.filter(s => s.id !== id);
                    });
                    this.notes = this.notes.filter(t => t.id !== id);
                    
                    if (this.viewingTask && this.viewingTask.id === id) {
                        this.closeTaskDetails();
                    }
                } catch (e) { console.error(e); }
            },

            async generateBreakdown() {
                if (this.breakdownSteps.length > 0) {
                    const parentTitle = this.generatedTitle || (
                        this.breakdownPrompt.length > 50 
                        ? this.breakdownPrompt.substring(0, 47) + '...' 
                        : this.breakdownPrompt
                    );
                    
                    const parentDescription = this.breakdownPrompt; // The original prompt is the context

                    try {
                        const res = await fetch('/tasks', {
                            method: 'POST',
                            headers: { 
                                'Content-Type': 'application/json', 
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                            },
                            body: JSON.stringify({ 
                                title: parentTitle, 
                                description: parentDescription,
                                type: 'task',
                                category_id: this.selectedCategory ? this.selectedCategory.id : null,
                                due_date: this.newTaskDate || null,
                                alarm_enabled: this.alarmEnabled
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            const parent = data.task;
                            window.playTaskSound('add');
                            
                            for (const step of this.breakdownSteps) {
                                await this.addTask(step, parent.id, true);
                            }
                            
                            this.breakdownSteps = [];
                            this.breakdownPrompt = '';
                            this.generatedTitle = ''; // Reset
                            this.openBreakdownModal = false;
                            
                            this.fetchData();
                        }
                    } catch (e) { 
                        console.error(e);
                    }
                    return;
                }

                this.isGenerating = true;
                try {
                    const res = await fetch('/tasks/breakdown', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: JSON.stringify({ content: this.breakdownPrompt })
                    });
                    const data = await res.json();
                    if (data.success) {
                        this.breakdownSteps = data.steps;
                        this.generatedTitle = data.title; // Store the AI generated title
                    } else {
                        alert('AI Breakdown failed: ' + data.message);
                    }
                } catch (e) { console.error(e); alert('Connection error.'); }
                finally { this.isGenerating = false; }
            },

            // --- Voice Methods ---

            async getMicrophones() {
                if (!navigator.mediaDevices || !navigator.mediaDevices.enumerateDevices) return;
                
                try {
                    // We need to request permission first to get labels
                    // But we don't want to start recording yet.
                    // Just enumerating usually returns empty labels if permission not granted.
                    // If user has already granted permission (persisted), labels will show.
                    
                    const devices = await navigator.mediaDevices.enumerateDevices();
                    this.availableMicrophones = devices.filter(device => device.kind === 'audioinput');
                    
                    if (this.availableMicrophones.length > 0 && this.selectedMicrophoneId === 'default') {
                        // Keep default or select first? Default is fine.
                    }
                } catch (err) {
                    console.error("Error fetching microphones:", err);
                }
            },

            async startRecording() {
                console.log('startRecording initiated');
                
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert('Audio recording is not supported in this browser or context (requires HTTPS or localhost).');
                    return;
                }

                // Check Permissions API if available
                if (navigator.permissions && navigator.permissions.query) {
                    try {
                        const permissionStatus = await navigator.permissions.query({ name: 'microphone' });
                        if (permissionStatus.state === 'denied') {
                            alert("Microphone access is blocked. Please click the lock icon in your address bar and allow microphone access.");
                            return;
                        }
                    } catch (e) {
                        console.log("Permission query skipped:", e);
                    }
                }

                try {
                    console.log('Requesting microphone access...');
                    
                    const constraints = { 
                        audio: this.selectedMicrophoneId !== 'default' 
                            ? { deviceId: { exact: this.selectedMicrophoneId } } 
                            : true 
                    };

                    const stream = await navigator.mediaDevices.getUserMedia(constraints);
                    console.log('Microphone access granted');
                    
                    // Refresh mic list now that we have permission (labels might appear)
                    this.getMicrophones();
                    
                    let mimeType = 'audio/webm';
                    if (MediaRecorder.isTypeSupported('audio/webm;codecs=opus')) {
                        mimeType = 'audio/webm;codecs=opus';
                    } else if (MediaRecorder.isTypeSupported('audio/mp4')) {
                        mimeType = 'audio/mp4';
                    }
                    console.log('Using MIME type:', mimeType);

                    this.mediaRecorder = new MediaRecorder(stream, { mimeType });
                    this.audioChunks = [];

                    this.mediaRecorder.ondataavailable = (event) => {
                        if (event.data.size > 0) {
                            this.audioChunks.push(event.data);
                        }
                    };

                    this.mediaRecorder.onstop = () => {
                        this.audioBlob = new Blob(this.audioChunks, { type: mimeType });
                        this.audioChunks = [];
                        
                        // Stop all tracks to release mic
                        stream.getTracks().forEach(track => track.stop());
                    };

                    this.mediaRecorder.start();
                    this.isRecording = true;
                    this.recordingTime = 0;
                    this.recordingTitle = 'Meeting - ' + new Date().toLocaleDateString();
                    this.recordingDescription = '';
                    
                    this.timerInterval = setInterval(() => {
                        this.recordingTime++;
                    }, 1000);

                } catch (err) {
                    console.error("Microphone access denied or error:", err);
                    alert("Microphone access is required for this feature. Please check your browser permissions.");
                }
            },

            stopRecording() {
                if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                    this.mediaRecorder.stop();
                    this.isRecording = false;
                    clearInterval(this.timerInterval);
                }
            },

            discardRecording() {
                this.audioBlob = null;
                this.recordingTime = 0;
                this.isRecording = false;
                if (this.timerInterval) clearInterval(this.timerInterval);
                if (this.audioPlayer) {
                    this.audioPlayer.pause();
                    this.audioPlayer = null;
                    this.isPlayingAudio = false;
                }
            },

            toggleAudioPlayback() {
                if (this.isPlayingAudio && this.audioPlayer) {
                    this.audioPlayer.pause();
                    this.isPlayingAudio = false;
                    return;
                }

                if (!this.audioPlayer && this.audioBlob) {
                    const audioUrl = URL.createObjectURL(this.audioBlob);
                    this.audioPlayer = new Audio(audioUrl);
                    this.audioPlayer.onended = () => {
                        this.isPlayingAudio = false;
                    };
                }

                if (this.audioPlayer) {
                    this.audioPlayer.play();
                    this.isPlayingAudio = true;
                }
            },

            formatTime(seconds) {
                const mins = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
            },

            async processAudio(type) {
                if (!this.audioBlob) return;
                this.isProcessing = true;

                const formData = new FormData();
                formData.append('audio', this.audioBlob, 'recording.webm');
                formData.append('type', type); // 'note' or 'tasks'
                formData.append('title', this.recordingTitle);
                formData.append('description', this.recordingDescription);

                try {
                    const res = await fetch('/tasks/voice-to-intelligence', {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                        },
                        body: formData
                    });

                    const data = await res.json();

                    if (data.success) {
                        window.playTaskSound('success');
                        
                        if (type === 'note') {
                            this.notes.unshift(data.note);
                            this.activeTab = 'notes';
                        } else if (type === 'tasks') {
                            // If it created a parent task with subtasks
                            if (data.task) {
                                this.tasks.unshift(data.task);
                            }
                            // If it returned a list of individual tasks
                            if (data.tasks) {
                                // this.tasks = [...data.tasks, ...this.tasks]; // Prepend multiple?
                                // Let's assume the backend returns a single parent task for cleaner UI usually
                            }
                            this.activeTab = 'tasks';
                        }
                        
                        this.discardRecording(); // Reset UI
                        this.fetchData(); // Ensure sync
                    } else {
                        alert('Processing failed: ' + (data.message || 'Unknown error'));
                    }
                } catch (e) {
                    console.error(e);
                    alert('Upload failed. Please check connection.');
                } finally {
                    this.isProcessing = false;
                }
            },

            // --- Ghost Studio Methods ---

            startGhostRecording() {
                if (typeof rrweb === 'undefined') {
                    alert('Ghost Recorder resources not loaded. Please refresh the page.');
                    return;
                }

                this.ghostEvents = [];
                this.isGhostRecording = true;
                this.isOpen = false; // Hide widget to record the app interaction

                this.stopFn = rrweb.record({
                    emit: (event) => {
                        this.ghostEvents.push(event);
                    },
                    recordCanvas: true,
                    collectFonts: true
                });
            },

            stopGhostRecording() {
                if (this.stopFn) {
                    this.stopFn();
                    this.stopFn = null;
                }
                this.isGhostRecording = false;
                this.isOpen = true; // Show widget again
                this.saveGhostRecording();
            },

            async saveGhostRecording() {
                if (this.ghostEvents.length < 2) return;

                const title = 'Demo ' + new Date().toLocaleTimeString();
                
                try {
                    const res = await fetch('/tasks/ghost-demo', {
                        method: 'POST',
                        headers: { 
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content 
                        },
                        body: JSON.stringify({ 
                            title: title,
                            events: this.ghostEvents 
                        })
                    });
                    
                    const data = await res.json();
                    if(data.success) {
                        this.ghostDemos.unshift(data.demo);
                        this.ghostEvents = [];
                        window.playTaskSound('success');
                    } else {
                        alert('Failed to save demo: ' + data.message);
                    }
                } catch(e) {
                    console.error(e);
                    alert('Failed to save demo. Recording might be too large.');
                }
            },

            playDemo(id) {
                window.open('/tasks/ghost-demo/' + id, '_blank');
            },
            
            playSound(type) {
                if (type === 'success' && this.audioSuccess) {
                    this.audioSuccess.currentTime = 0;
                    this.audioSuccess.play().catch(e => console.log('Audio blocked', e));
                } else if (type === 'add' && this.audioAdd) {
                    this.audioAdd.currentTime = 0;
                    this.audioAdd.play().catch(e => console.log('Audio blocked', e));
                }
            },

            formatDate(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString(undefined, { month: 'short', day: 'numeric' });
            },
            
            formatDateTimeShort(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString(undefined, { month: 'numeric', day: 'numeric' }) + ' ' +
                       date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            },

            isOverdue(dateString) {
                if (!dateString) return false;
                return new Date(dateString) < new Date();
            }
        }));
    });
</script>
