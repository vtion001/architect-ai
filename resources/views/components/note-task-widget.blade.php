<!-- resources/views/components/note-task-widget.blade.php -->
<div x-data="noteTaskWidget()" 
     x-cloak
     @open-notes.window="isOpen = true"
     @mousedown="getAudioContext().resume()"
     @keydown="getAudioContext().resume()"
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
             
             <!-- Tasks Tab -->
             <div x-show="activeTab === 'tasks'" class="space-y-4">
                 <!-- Add Task Input Area -->
                 <div class="bg-card p-3 rounded-xl border border-border shadow-sm">
                     <div class="relative mb-3">
                         <input type="text" x-model="newTaskTitle" @keydown.enter="addTask()" placeholder="What needs to be done?" 
                                class="w-full pl-3 pr-10 py-2.5 text-sm bg-muted/30 border border-border rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none transition-all placeholder:text-muted-foreground/50">
                         <button @click="openBreakdownModal = true" class="absolute right-2 top-1/2 -translate-y-1/2 text-primary/70 hover:text-primary transition-colors p-1 rounded-md hover:bg-primary/10" title="AI Breakdown">
                             <i data-lucide="sparkles" class="w-4 h-4"></i>
                         </button>
                     </div>
                     <div class="flex items-center gap-2 justify-between flex-wrap">
                        <div class="flex gap-2 items-center flex-wrap">
                            <!-- Category Select -->
                            <div class="relative" x-data="{ openCat: false }">
                                <button @click="openCat = !openCat" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-md bg-muted/30 border border-border text-xs text-muted-foreground hover:text-foreground hover:border-primary/30 transition-all">
                                    <template x-if="selectedCategory">
                                        <div class="flex items-center gap-1.5">
                                            <span class="w-2 h-2 rounded-full ring-1 ring-offset-1 ring-offset-card" :style="{ backgroundColor: selectedCategory.color, '--tw-ring-color': selectedCategory.color }"></span>
                                            <span x-text="selectedCategory.name" class="font-medium"></span>
                                        </div>
                                    </template>
                                    <template x-if="!selectedCategory">
                                        <span class="font-medium">Category</span>
                                    </template>
                                    <i data-lucide="chevron-down" class="w-3 h-3 opacity-70"></i>
                                </button>
                                
                                <!-- Dropdown -->
                                <div x-show="openCat" @click.away="openCat = false" 
                                     class="absolute top-full left-0 mt-2 w-52 bg-card border border-border rounded-xl shadow-xl z-50 p-1.5 animate-in fade-in zoom-in-95 duration-100 origin-top-left">
                                    <template x-for="cat in categories" :key="cat.id">
                                        <div class="group flex items-center justify-between px-2 py-1.5 rounded-lg hover:bg-muted cursor-pointer transition-colors" @click="selectedCategory = cat; openCat = false">
                                            <div class="flex items-center gap-2">
                                                <span class="w-2.5 h-2.5 rounded-full" :style="{ backgroundColor: cat.color }"></span>
                                                <span x-text="cat.name" class="text-xs font-medium text-foreground"></span>
                                            </div>
                                            <button @click.stop="deleteCategory(cat.id)" class="opacity-0 group-hover:opacity-100 p-1 text-muted-foreground hover:text-red-500 rounded-md hover:bg-red-50 transition-all">
                                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                                            </button>
                                        </div>
                                    </template>
                                    
                                    <div class="border-t border-border mt-1 pt-1.5 px-1">
                                        <p class="text-[9px] font-bold text-muted-foreground uppercase tracking-wider mb-1.5 ml-1">New Category</p>
                                        <div class="flex gap-1.5">
                                            <input type="text" x-model="newCategoryName" placeholder="Name..." class="flex-1 text-xs bg-muted/50 border border-transparent focus:border-primary/50 focus:bg-background rounded px-2 py-1 outline-none transition-all">
                                            <div class="relative w-6 h-6 shrink-0 overflow-hidden rounded-md ring-1 ring-border">
                                                <input type="color" x-model="newCategoryColor" class="absolute -top-2 -left-2 w-10 h-10 p-0 border-none cursor-pointer">
                                            </div>
                                            <button @click="addCategory()" class="w-6 h-6 flex items-center justify-center bg-primary text-primary-foreground rounded-md hover:opacity-90 transition-opacity">
                                                <i data-lucide="plus" class="w-3 h-3"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Due Date & Time -->
                             <div class="relative">
                                 <input type="datetime-local" x-model="newTaskDate" class="w-[130px] px-2 py-1.5 rounded-md bg-muted/30 border border-border text-[10px] text-muted-foreground hover:text-foreground hover:border-primary/30 outline-none transition-all cursor-pointer">
                             </div>

                             <!-- Alarm Toggle -->
                             <button @click="alarmEnabled = !alarmEnabled" 
                                     class="p-1.5 rounded-md transition-colors"
                                     :class="alarmEnabled ? 'text-primary bg-primary/10' : 'text-muted-foreground hover:bg-muted/50'">
                                <i data-lucide="bell" class="w-4 h-4"></i>
                             </button>
                        </div>
                        <button @click="addTask()" class="text-xs font-black bg-primary text-primary-foreground px-3 py-1.5 rounded-lg hover:opacity-90 transition-opacity">ADD</button>
                     </div>
                 </div>

                 <!-- Task List -->
                 <div class="space-y-3">
                     <template x-for="task in filteredTasks" :key="task.id">
                         <div class="group relative bg-card rounded-xl border border-border hover:border-primary/30 transition-all shadow-sm hover:shadow-md"
                              :class="{ 'opacity-60 bg-muted/30': task.status === 'completed' }">
                             
                             <div class="flex items-start gap-3 p-3">
                                 <!-- Checkbox -->
                                 <div class="mt-1 relative shrink-0">
                                     <input type="checkbox" :checked="task.status === 'completed'" @change="toggleTask(task)"
                                            class="peer w-5 h-5 rounded-md border-border text-primary focus:ring-primary/20 cursor-pointer appearance-none border-2 checked:bg-primary checked:border-primary transition-all">
                                     <i data-lucide="check" class="absolute top-0.5 left-0.5 w-3.5 h-3.5 text-primary-foreground opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity stroke-[3]"></i>
                                 </div>
                                 
                                 <div class="flex-1 min-w-0">
                                     <!-- Title & Details Trigger -->
                                     <div class="flex items-start justify-between gap-2">
                                         <div class="flex-1 cursor-pointer" @click="openTaskDetails(task)">
                                             <p x-text="task.title" 
                                                :class="{ 'line-through text-muted-foreground': task.status === 'completed', 'hover:text-primary': task.status !== 'completed' }" 
                                                class="text-sm font-medium leading-tight break-words transition-colors"></p>
                                         </div>
                                         
                                         <!-- Badges -->
                                         <div class="flex items-center gap-1.5 shrink-0">
                                            <template x-if="task.alarm_enabled">
                                                <i data-lucide="bell-ring" class="w-3 h-3 text-orange-500"></i>
                                            </template>
                                            <template x-if="task.category">
                                                <span class="text-[9px] px-2 py-0.5 rounded-full font-black uppercase tracking-wider text-white shadow-sm" 
                                                      :style="{ backgroundColor: task.category.color }"
                                                      x-text="task.category.name"></span>
                                            </template>
                                            <template x-if="task.due_date">
                                                <div class="flex items-center gap-1 px-1.5 py-0.5 rounded-md bg-muted/50 border border-border/50" 
                                                     :class="{ 'border-red-500/50 bg-red-500/10 text-red-600': isOverdue(task.due_date) && task.status !== 'completed' }">
                                                    <span x-text="formatDateTimeShort(task.due_date)" class="text-[9px] font-bold whitespace-nowrap"></span>
                                                </div>
                                            </template>
                                            <!-- Detail View Button -->
                                            <button @click.stop="openTaskDetails(task)" class="text-muted-foreground hover:text-foreground">
                                                <i data-lucide="maximize-2" class="w-3 h-3"></i>
                                            </button>
                                         </div>
                                     </div>

                                     <!-- Preview of Subtasks (First 2) -->
                                     <template x-if="task.subtasks && task.subtasks.length > 0">
                                         <div class="mt-2 space-y-1 relative">
                                             <template x-for="sub in task.subtasks.slice(0, 2)" :key="sub.id">
                                                 <div class="flex items-center gap-2 pl-1 opacity-70">
                                                     <div class="w-1 h-1 rounded-full bg-muted-foreground"></div>
                                                     <span x-text="sub.title" class="text-[10px] truncate text-muted-foreground"></span>
                                                 </div>
                                             </template>
                                             <div x-show="task.subtasks.length > 2" class="pl-1 text-[9px] text-muted-foreground italic">
                                                 + <span x-text="task.subtasks.length - 2"></span> more steps
                                             </div>
                                         </div>
                                     </template>
                                 </div>
                             </div>
                         </div>
                     </template>
                     
                     <div x-show="filteredTasks.length === 0" class="flex flex-col items-center justify-center py-12 text-muted-foreground opacity-50">
                         <i data-lucide="check-circle-2" class="w-12 h-12 mb-3 stroke-1"></i>
                         <p class="text-xs font-medium italic">No matching tasks found.</p>
                     </div>
                 </div>
             </div>

             <!-- Notes Tab -->
             <div x-show="activeTab === 'notes'" class="space-y-4">
                 <div class="flex justify-end">
                     <button @click="addNote()" class="text-xs font-bold text-primary hover:text-primary/80 flex items-center gap-1.5 px-3 py-1.5 bg-primary/10 rounded-lg transition-colors">
                         <i data-lucide="plus" class="w-3.5 h-3.5"></i> New Note
                     </button>
                 </div>
                 <div class="grid gap-3">
                     <template x-for="note in filteredNotes" :key="note.id">
                         <div class="group relative bg-card p-4 border border-border rounded-xl shadow-sm hover:border-primary/40 hover:shadow-md transition-all">
                             <input type="text" x-model="note.title" @change="updateNote(note)" 
                                    class="w-[90%] bg-transparent border-none p-0 text-sm font-bold text-foreground focus:ring-0 mb-2 placeholder:text-muted-foreground/50 transition-colors" placeholder="Untitled Note">
                             <textarea x-model="note.description" @change="updateNote(note)" 
                                       class="w-full bg-transparent border-none p-0 text-xs text-muted-foreground focus:ring-0 resize-none h-24 custom-scrollbar placeholder:text-muted-foreground/50 leading-relaxed" placeholder="Type your note here..."></textarea>
                             
                             <button @click="deleteTask(note.id)" class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 p-1.5 text-muted-foreground hover:text-red-600 hover:bg-red-50 rounded-lg transition-all">
                                 <i data-lucide="trash-2" class="w-4 h-4"></i>
                             </button>
                             
                             <div class="absolute bottom-3 right-3">
                                 <p class="text-[9px] font-mono text-muted-foreground/40 uppercase tracking-widest" x-text="formatDate(note.created_at)"></p>
                             </div>
                         </div>
                     </template>
                     
                     <div x-show="filteredNotes.length === 0" class="flex flex-col items-center justify-center py-12 text-muted-foreground opacity-50">
                         <i data-lucide="sticky-note" class="w-12 h-12 mb-3 stroke-1"></i>
                         <p class="text-xs font-medium italic">No matching notes found.</p>
                     </div>
                 </div>
             </div>

             <!-- History Tab -->
             <div x-show="activeTab === 'history'" class="space-y-4">
                 <div class="flex items-center justify-between">
                     <h3 class="text-xs font-bold text-muted-foreground uppercase tracking-wider">Recently Deleted</h3>
                     <button @click="fetchHistory()" class="text-[10px] font-bold text-primary hover:underline">Refresh</button>
                 </div>
                 
                 <div class="space-y-2">
                     <template x-for="item in history" :key="item.id">
                         <div class="flex items-center justify-between p-3 bg-muted/20 border border-border rounded-xl opacity-75 hover:opacity-100 transition-opacity">
                             <div class="min-w-0 flex-1 mr-3">
                                 <p x-text="item.title" class="text-xs font-medium truncate text-foreground"></p>
                                 <p class="text-[9px] text-muted-foreground uppercase tracking-wide" x-text="item.type + ' • ' + formatDate(item.deleted_at)"></p>
                             </div>
                             <div class="flex items-center gap-2">
                                 <button @click="restoreItem(item.id)" class="p-1.5 bg-primary/10 text-primary rounded-lg hover:bg-primary hover:text-white transition-colors" title="Restore">
                                     <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                                 </button>
                                 <button @click="forceDeleteItem(item.id)" class="p-1.5 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-colors" title="Delete Forever">
                                     <i data-lucide="trash" class="w-3.5 h-3.5"></i>
                                 </button>
                             </div>
                         </div>
                     </template>
                     
                     <div x-show="history.length === 0" class="flex flex-col items-center justify-center py-12 text-muted-foreground opacity-50">
                         <i data-lucide="trash-2" class="w-8 h-8 mb-2 stroke-1"></i>
                         <p class="text-xs font-medium italic">Trash is empty.</p>
                     </div>
                 </div>
             </div>
         </div>
         
         <!-- Task Details Modal -->
         <div x-show="viewingTask" x-cloak class="absolute inset-0 bg-background z-30 flex flex-col animate-in slide-in-from-right duration-200">
             <div class="flex items-center justify-between px-4 py-3 border-b border-border">
                 <button @click="closeTaskDetails()" class="flex items-center gap-1 text-xs font-bold text-muted-foreground hover:text-foreground">
                     <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
                 </button>
                 <div class="flex items-center gap-2">
                     <button @click="deleteTask(viewingTask.id); closeTaskDetails()" class="text-red-500 hover:bg-red-50 p-1.5 rounded-md transition-colors">
                         <i data-lucide="trash-2" class="w-4 h-4"></i>
                     </button>
                 </div>
             </div>
             
             <template x-if="viewingTask">
                 <div class="flex-1 overflow-y-auto p-5 custom-scrollbar">
                     <!-- Title Edit -->
                     <textarea x-model="viewingTask.title" 
                               @change="updateTaskTitle(viewingTask, viewingTask.title)"
                               class="w-full bg-transparent border-none p-0 text-lg font-bold text-foreground focus:ring-0 resize-none mb-4" 
                               rows="2"></textarea>
                     
                     <!-- Metadata Grid -->
                     <div class="grid grid-cols-2 gap-4 mb-6">
                         <div class="bg-muted/30 p-3 rounded-lg border border-border/50">
                             <label class="text-[10px] uppercase font-bold text-muted-foreground block mb-1">Due Date</label>
                             <input type="datetime-local" x-model="viewingTask.due_date" 
                                    @change="updateTaskField(viewingTask, 'due_date', viewingTask.due_date)"
                                    class="w-full bg-transparent border-none p-0 text-xs font-medium focus:ring-0">
                         </div>
                         <div class="bg-muted/30 p-3 rounded-lg border border-border/50 flex items-center justify-between">
                             <div>
                                 <label class="text-[10px] uppercase font-bold text-muted-foreground block mb-1">Alarm</label>
                                 <span x-text="viewingTask.alarm_enabled ? 'On' : 'Off'" class="text-xs font-medium"></span>
                             </div>
                             <button @click="viewingTask.alarm_enabled = !viewingTask.alarm_enabled; updateTaskField(viewingTask, 'alarm_enabled', viewingTask.alarm_enabled)" 
                                     class="p-1.5 rounded-full transition-colors"
                                     :class="viewingTask.alarm_enabled ? 'bg-primary text-primary-foreground' : 'bg-muted text-muted-foreground'">
                                 <i data-lucide="bell" class="w-4 h-4"></i>
                             </button>
                         </div>
                     </div>

                     <!-- Full Description -->
                     <div class="mb-6">
                         <label class="text-[10px] uppercase font-bold text-muted-foreground block mb-2">Description / Context</label>
                         <textarea x-model="viewingTask.description" 
                                   @change="updateTaskField(viewingTask, 'description', viewingTask.description)"
                                   class="w-full bg-muted/20 border border-border rounded-lg p-3 text-xs leading-relaxed min-h-[100px] focus:ring-1 focus:ring-primary focus:border-primary"
                                   placeholder="Add more details..."></textarea>
                     </div>

                     <!-- Subtasks List -->
                     <div>
                         <label class="text-[10px] uppercase font-bold text-muted-foreground block mb-2">Action Steps</label>
                         <div class="space-y-2">
                             <template x-for="sub in viewingTask.subtasks" :key="sub.id">
                                 <div class="flex items-start gap-2 group/sub p-2 rounded-lg hover:bg-muted/30">
                                     <input type="checkbox" :checked="sub.status === 'completed'" @change="toggleTask(sub)"
                                            class="mt-0.5 w-4 h-4 rounded border-border text-primary focus:ring-primary/20 cursor-pointer">
                                     <input type="text" x-model="sub.title" 
                                            @change="updateTaskTitle(sub, sub.title)"
                                            class="flex-1 bg-transparent border-none p-0 text-xs focus:ring-0"
                                            :class="sub.status === 'completed' ? 'line-through text-muted-foreground' : ''">
                                 </div>
                             </template>
                             <div class="flex items-center gap-2 p-2 opacity-50 hover:opacity-100 transition-opacity cursor-pointer text-primary" @click="addTask('New Step', viewingTask.id)">
                                 <i data-lucide="plus" class="w-4 h-4"></i>
                                 <span class="text-xs font-bold">Add Step</span>
                             </div>
                         </div>
                     </div>
                 </div>
             </template>
         </div>
         
         <!-- AI Breakdown Modal Overlay -->
         <div x-show="openBreakdownModal" class="absolute inset-0 bg-background/95 backdrop-blur-md z-20 flex flex-col p-6 animate-in fade-in duration-200" style="backdrop-filter: blur(8px);">
             <div class="flex items-center justify-between mb-6">
                 <div>
                    <h3 class="text-base font-black text-foreground flex items-center gap-2">
                        <i data-lucide="sparkles" class="w-4 h-4 text-primary fill-primary/20"></i>
                        AI Task Breakdown
                    </h3>
                    <p class="text-[10px] text-muted-foreground mt-1">Turn a complex goal into actionable steps.</p>
                 </div>
                 <button @click="openBreakdownModal = false" class="text-muted-foreground hover:text-foreground p-1 hover:bg-muted rounded-md transition-colors">
                     <i data-lucide="x" class="w-5 h-5"></i>
                 </button>
             </div>
             
             <div class="relative mb-4">
                 <textarea x-model="breakdownPrompt" placeholder="e.g. Plan a product launch campaign for next month..." 
                           class="w-full h-32 bg-card border border-border rounded-xl p-4 text-sm focus:ring-2 focus:ring-primary/20 focus:border-primary outline-none resize-none shadow-inner placeholder:text-muted-foreground/50 leading-relaxed"></textarea>
             </div>
             
             <div class="flex-1 overflow-y-auto mb-4 space-y-2 custom-scrollbar pr-1" x-show="breakdownSteps.length > 0">
                 <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest mb-3">Suggested Plan</p>
                 <template x-for="(step, idx) in breakdownSteps" :key="idx">
                     <div class="flex items-start gap-3 p-3 bg-card border border-border/50 rounded-xl text-xs shadow-sm">
                         <span class="flex items-center justify-center w-5 h-5 rounded-full bg-primary/10 text-primary font-bold text-[10px] shrink-0" x-text="idx + 1"></span>
                         <span x-text="step" class="leading-relaxed text-foreground/80"></span>
                     </div>
                 </template>
             </div>

             <button @click="generateBreakdown()" :disabled="isGenerating || !breakdownPrompt"
                     class="w-full py-3 bg-primary text-primary-foreground rounded-xl text-sm font-bold flex items-center justify-center gap-2 disabled:opacity-50 hover:opacity-90 transition-all shadow-lg shadow-primary/20">
                 <template x-if="isGenerating">
                     <span class="flex items-center gap-2">
                        <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                        <span class="tracking-wide">Analyzing...</span>
                     </span>
                 </template>
                 <template x-if="!isGenerating">
                     <span class="tracking-wide" x-text="breakdownSteps.length > 0 ? 'Accept Plan & Create Tasks' : 'Generate Breakdown'"></span>
                 </template>
             </button>
         </div>
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
    document.addEventListener('alpine:init', () => {
        Alpine.data('noteTaskWidget', () => ({
            isOpen: false,
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

            // Audio - Using Web Audio API for reliable synthesis (no external files)
            audioContext: null,

            getAudioContext() {
                if (!this.audioContext) {
                    this.audioContext = new (window.AudioContext || window.webkitAudioContext)();
                }
                return this.audioContext;
            },

            playSound(type) {
                const ctx = this.getAudioContext();
                if (ctx.state === 'suspended') {
                    ctx.resume();
                }

                const oscillator = ctx.createOscillator();
                const gainNode = ctx.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(ctx.destination);

                if (type === 'add') {
                    // "Pop" sound: Short, high pitch, quick decay
                    oscillator.type = 'sine';
                    oscillator.frequency.setValueAtTime(800, ctx.currentTime);
                    oscillator.frequency.exponentialRampToValueAtTime(100, ctx.currentTime + 0.1);
                    gainNode.gain.setValueAtTime(0.5, ctx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.1);
                    oscillator.start();
                    oscillator.stop(ctx.currentTime + 0.1);
                } else if (type === 'success') {
                    // "Ding" sound: Bell-like, higher pitch, longer decay
                    oscillator.type = 'sine';
                    oscillator.frequency.setValueAtTime(1200, ctx.currentTime); // High C
                    gainNode.gain.setValueAtTime(0.5, ctx.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.5);
                    oscillator.start();
                    oscillator.stop(ctx.currentTime + 0.5);
                    
                    // Add a second harmonic for richness
                    const osc2 = ctx.createOscillator();
                    const gain2 = ctx.createGain();
                    osc2.connect(gain2);
                    gain2.connect(ctx.destination);
                    osc2.frequency.setValueAtTime(2400, ctx.currentTime);
                    gain2.gain.setValueAtTime(0.3, ctx.currentTime);
                    gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                    osc2.start();
                    osc2.stop(ctx.currentTime + 0.4);
                } else if (type === 'alarm') {
                    // Alarm sound: Repeating beep
                    const now = ctx.currentTime;
                    oscillator.type = 'square';
                    oscillator.frequency.value = 880;
                    gainNode.gain.setValueAtTime(0.5, now);
                    gainNode.gain.setValueAtTime(0, now + 0.1);
                    gainNode.gain.setValueAtTime(0.5, now + 0.2);
                    gainNode.gain.setValueAtTime(0, now + 0.3);
                    
                    oscillator.start();
                    oscillator.stop(now + 0.4);
                }
            },

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
                this.$watch('isOpen', value => {
                    if (value && window.lucide) {
                        setTimeout(() => lucide.createIcons(), 100);
                    }
                });
                this.$watch('activeTab', value => {
                    if (value === 'history') {
                        this.fetchHistory();
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
                // Play sound using Web Audio API
                this.playSound('alarm');
                
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
                        this.playSound('add');
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
                    this.playSound('success');
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
                            this.playSound('add');
                            
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