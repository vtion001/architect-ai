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
