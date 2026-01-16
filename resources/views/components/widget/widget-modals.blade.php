<!-- Task Details Modal Overlay -->
<div x-show="viewingTask" 
     x-cloak 
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-x-8"
     x-transition:enter-end="opacity-100 translate-x-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 translate-x-0"
     x-transition:leave-end="opacity-0 translate-x-8"
     class="absolute inset-0 bg-background z-[60] flex flex-col">
    
    <div class="flex items-center justify-between px-4 py-3 border-b border-border shrink-0">
        <button @click="closeTaskDetails()" class="flex items-center gap-1 text-xs font-black uppercase tracking-widest text-muted-foreground hover:text-primary transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Back
        </button>
        <div class="flex items-center gap-2">
            <button @click="deleteTask(viewingTask.id); closeTaskDetails()" class="text-muted-foreground hover:text-red-500 p-1.5 rounded-md transition-colors hover:bg-red-50" title="Delete Task">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </div>
    </div>
    
    <template x-if="viewingTask">
        <div class="flex-1 overflow-y-auto p-5 custom-scrollbar bg-background">
            <!-- Title Edit -->
            <textarea x-model="viewingTask.title" 
                      @change="updateTaskTitle(viewingTask, viewingTask.title)"
                      class="w-full bg-transparent border-none p-0 text-xl font-black text-foreground focus:ring-0 resize-none mb-6 leading-tight" 
                      rows="2" placeholder="Task title..."></textarea>
            
            <!-- Metadata Grid -->
            <div class="grid grid-cols-2 gap-3 mb-8">
                <div class="bg-muted/30 p-3 rounded-xl border border-border/50">
                    <label class="text-[9px] uppercase font-black text-muted-foreground block mb-1.5 tracking-widest">Due Date</label>
                    <input type="datetime-local" x-model="viewingTask.due_date" 
                           @change="updateTaskField(viewingTask, 'due_date', viewingTask.due_date)"
                           class="w-full bg-transparent border-none p-0 text-[11px] font-bold focus:ring-0 text-foreground cursor-pointer">
                </div>
                <div class="bg-muted/30 p-3 rounded-xl border border-border/50 flex items-center justify-between">
                    <div>
                        <label class="text-[9px] uppercase font-black text-muted-foreground block mb-1.5 tracking-widest">Alarm</label>
                        <span x-text="viewingTask.alarm_enabled ? 'Active' : 'Disabled'" class="text-[11px] font-bold" :class="viewingTask.alarm_enabled ? 'text-orange-500' : 'text-muted-foreground'"></span>
                    </div>
                    <button @click="viewingTask.alarm_enabled = !viewingTask.alarm_enabled; updateTaskField(viewingTask, 'alarm_enabled', viewingTask.alarm_enabled)" 
                            class="p-2 rounded-lg transition-all"
                            :class="viewingTask.alarm_enabled ? 'bg-orange-500 text-white shadow-lg shadow-orange-500/20' : 'bg-muted text-muted-foreground'">
                        <i data-lucide="bell" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-3">
                    <i data-lucide="align-left" class="w-3.5 h-3.5 text-primary"></i>
                    <label class="text-[9px] uppercase font-black text-muted-foreground tracking-widest">Context / Notes</label>
                </div>
                <textarea x-model="viewingTask.description" 
                          @change="updateTaskField(viewingTask, 'description', viewingTask.description)"
                          class="w-full bg-muted/20 border border-border rounded-xl p-4 text-xs leading-relaxed min-h-[120px] focus:ring-2 focus:ring-primary/20 focus:border-primary/50 outline-none transition-all placeholder:italic"
                          placeholder="Add detailed instructions or context here..."></textarea>
            </div>

            <!-- Action Steps -->
            <div class="space-y-4">
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2">
                        <i data-lucide="list-checks" class="w-3.5 h-3.5 text-primary"></i>
                        <label class="text-[9px] uppercase font-black text-muted-foreground tracking-widest">Action Steps</label>
                    </div>
                    <span class="text-[9px] font-bold text-muted-foreground" x-text="(viewingTask.subtasks ? viewingTask.subtasks.filter(s => s.status === 'completed').length : 0) + '/' + (viewingTask.subtasks ? viewingTask.subtasks.length : 0)"></span>
                </div>

                <div class="space-y-2">
                    <template x-if="viewingTask.subtasks">
                        <template x-for="sub in viewingTask.subtasks" :key="sub.id">
                            <div class="flex items-center gap-3 group/sub p-2 rounded-xl hover:bg-muted/30 transition-colors border border-transparent hover:border-border/50">
                                <div class="relative shrink-0 flex items-center justify-center">
                                    <input type="checkbox" :checked="sub.status === 'completed'" @change="toggleTask(sub)"
                                           class="peer w-4 h-4 rounded border-border text-primary focus:ring-primary/20 cursor-pointer appearance-none border-2 checked:bg-primary checked:border-primary transition-all">
                                    <i data-lucide="check" class="absolute w-2.5 h-2.5 text-white opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity stroke-[4]"></i>
                                </div>
                                <input type="text" x-model="sub.title" 
                                       @change="updateTaskTitle(sub, sub.title)"
                                       class="flex-1 bg-transparent border-none p-0 text-xs font-medium focus:ring-0 transition-all"
                                       :class="sub.status === 'completed' ? 'line-through text-muted-foreground opacity-50' : 'text-foreground'">
                                <button @click="deleteTask(sub.id)" class="opacity-0 group-hover/sub:opacity-100 p-1 text-muted-foreground hover:text-red-500 transition-all">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                </button>
                            </div>
                        </template>
                    </template>
                    
                    <button @click="addTask('New Step', viewingTask.id)" class="w-full flex items-center gap-2 p-3 rounded-xl border border-dashed border-border hover:border-primary/50 hover:bg-primary/5 transition-all text-muted-foreground hover:text-primary group/add">
                        <div class="w-5 h-5 rounded-lg bg-muted flex items-center justify-center group-hover/add:bg-primary/10 transition-colors">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        </div>
                        <span class="text-xs font-bold uppercase tracking-wider">Add Action Step</span>
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>

<!-- AI Breakdown Modal Overlay -->
<div x-show="openBreakdownModal" 
     x-cloak
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95"
     x-transition:enter-end="opacity-100 scale-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100"
     x-transition:leave-end="opacity-0 scale-95"
     class="absolute inset-0 bg-background z-[70] flex flex-col p-6 overflow-hidden">
    
    <div class="flex items-center justify-between mb-6 shrink-0">
        <div>
           <h3 class="text-base font-black text-foreground flex items-center gap-2 uppercase tracking-tight">
               <i data-lucide="sparkles" class="w-5 h-5 text-primary fill-primary/20"></i>
               AI Task Breakdown
           </h3>
           <p class="text-[10px] font-bold text-muted-foreground uppercase tracking-widest mt-1 opacity-70">Neural Strategy Protocol</p>
        </div>
        <button @click="openBreakdownModal = false" class="text-muted-foreground hover:text-foreground p-2 hover:bg-muted rounded-xl transition-all">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>
    
    <div class="flex-1 flex flex-col min-h-0 space-y-4">
        <div class="relative shrink-0">
            <textarea x-model="breakdownPrompt" placeholder="e.g. Plan a product launch campaign for next month..." 
                      class="w-full h-32 bg-muted/20 border border-border rounded-2xl p-4 text-sm font-medium focus:ring-2 focus:ring-primary/20 focus:border-primary/50 outline-none resize-none shadow-inner placeholder:italic placeholder:opacity-50 leading-relaxed transition-all"></textarea>
        </div>
        
        <div class="flex-1 overflow-y-auto custom-scrollbar pr-1" x-show="breakdownSteps.length > 0">
            <p class="text-[9px] font-black text-primary uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                <span class="w-8 h-px bg-primary/30"></span>
                Suggested Plan Matrix
            </p>
            <div class="space-y-2">
                <template x-for="(step, idx) in breakdownSteps" :key="idx">
                    <div class="flex items-start gap-3 p-4 bg-card border border-border rounded-2xl text-xs shadow-sm hover:border-primary/30 transition-colors group">
                        <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-primary/10 text-primary font-black text-[10px] shrink-0 group-hover:bg-primary group-hover:text-white transition-all" x-text="idx + 1"></span>
                        <span x-text="step" class="leading-relaxed font-medium text-foreground/80 pt-0.5"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <div class="pt-6 mt-auto shrink-0">
        <button @click="generateBreakdown()" :disabled="isGenerating || !breakdownPrompt"
                class="w-full py-4 bg-primary text-primary-foreground rounded-2xl text-xs font-black uppercase tracking-[0.2em] flex items-center justify-center gap-3 disabled:opacity-50 hover:opacity-90 transition-all shadow-xl shadow-primary/20 active:scale-[0.98]">
            <template x-if="isGenerating">
                <div class="flex items-center gap-2">
                   <i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i>
                   <span>Synthesizing...</span>
                </div>
            </template>
            <template x-if="!isGenerating">
                <div class="flex items-center gap-2">
                    <i :data-lucide="breakdownSteps.length > 0 ? 'check-circle' : 'zap'" class="w-4 h-4"></i>
                    <span x-text="breakdownSteps.length > 0 ? 'Initialize Action Plan' : 'Generate Breakdown'"></span>
                </div>
            </template>
        </button>
    </div>
</div>
