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
