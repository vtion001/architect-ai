<?php $__env->startSection('content'); ?>
<div class="p-8 max-w-7xl mx-auto" x-data="{
    topic: '',
    type: 'blog-post',
    count: 2,
    tone: 'Default Tone',
    length: 'Default Length',
    context: '',
    cta: '',
    addLineBreaks: true,
    includeHashtags: false,
    isGenerating: false,
    generateContent() {
        if (!this.topic) {
            alert('Please enter a topic.');
            return;
        }
        this.isGenerating = true;
        fetch('<?php echo e(route('content-creator.generate')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                topic: this.topic,
                type: this.type,
                count: this.count,
                tone: this.tone,
                length: this.length,
                context: this.context,
                cta: this.cta,
                addLineBreaks: this.addLineBreaks,
                includeHashtags: this.includeHashtags
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Generation failed: ' + (data.message || 'Unknown error'));
                this.isGenerating = false;
            }
        })
        .catch(err => {
            console.error(err);
            this.isGenerating = false;
        });
    }
}">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Knowledge-Base Driven Content Creator</h1>
        <p class="text-muted-foreground">Generate high-quality content powered by your knowledge base</p>
    </div>

    <div class="mb-8 rounded-xl border border-primary/20 bg-primary/5 p-6 border-l-4 border-l-primary flex flex-col md:flex-row justify-between gap-6">
        <div class="flex gap-4 flex-1">
            <div class="rounded-full bg-primary/10 p-2 h-fit">
                <i data-lucide="help-circle" class="w-6 h-6 text-primary"></i>
            </div>
            <div>
                <h3 class="font-semibold text-lg mb-2">How to Use This Page</h3>
                <ol class="text-sm text-muted-foreground space-y-2 list-decimal list-inside">
                    <li>Enter your topic and generate posts using the form below</li>
                    <li>Review and edit your generated posts on the right</li>
                    <li>Select posts using checkboxes, then use "Bulk Images" or "Bulk Schedule"</li>
                    <li>For individual posts, click "Post Now" or "Schedule" to choose platforms</li>
                    <li>Platform selection happens when posting/scheduling, not during generation</li>
                </ol>
            </div>
        </div>
        
        <!-- Generator Toggles (Matching User Photo) -->
        <div class="flex flex-col gap-2 min-w-[200px]">
            <button class="flex items-center gap-3 px-4 py-3 rounded-lg bg-slate-900 text-white/70 hover:text-white transition-all text-sm font-medium border border-white/5">
                <i data-lucide="video" class="w-4 h-4"></i>
                Video Generator
            </button>
            <button class="flex items-center gap-3 px-4 py-3 rounded-lg bg-slate-800 text-white shadow-xl shadow-black/20 text-sm font-bold border border-white/10 ring-1 ring-white/20">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                Post Generator
            </button>
            <button class="flex items-center gap-3 px-4 py-3 rounded-lg bg-slate-900 text-white/70 hover:text-white transition-all text-sm font-medium border border-white/5">
                <i data-lucide="book" class="w-4 h-4"></i>
                Blog Generator
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Content -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Total Content</p>
                        <p class="text-2xl font-bold"><?php echo e(number_format($stats['total_content'])); ?></p>
                    </div>
                    <i data-lucide="file-text" class="w-8 h-8 text-blue-500"></i>
                </div>
            </div>
        </div>
        <!-- This Month -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">This Month</p>
                        <p class="text-2xl font-bold"><?php echo e(number_format($stats['this_month'])); ?></p>
                    </div>
                    <i data-lucide="trending-up" class="w-8 h-8 text-green-500"></i>
                </div>
            </div>
        </div>
        <!-- In Draft -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">In Draft</p>
                        <p class="text-2xl font-bold"><?php echo e(number_format($stats['in_draft'])); ?></p>
                    </div>
                    <i data-lucide="pencil" class="w-8 h-8 text-amber-500"></i>
                </div>
            </div>
        </div>
        <!-- Published -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Published</p>
                        <p class="text-2xl font-bold"><?php echo e(number_format($stats['published'])); ?></p>
                    </div>
                    <i data-lucide="sparkles" class="w-8 h-8 text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Content Generator -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm lg:col-span-2 overflow-hidden">
            <div class="bg-muted/50 border-b border-border p-3 text-center">
                <span class="text-sm font-semibold tracking-wide uppercase">Generate from Topic</span>
            </div>
            
            <div class="p-8 space-y-8">
                <div>
                    <h3 class="text-xl font-bold mb-1">Generate from Topic</h3>
                    <p class="text-sm text-muted-foreground">Define parameters for bulk text post generation based on a topic or idea.</p>
                </div>

                <!-- Main Topic -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-bold uppercase tracking-tight">Main Topic / Theme</label>
                        <button class="text-xs text-primary font-medium flex items-center gap-1 hover:underline">
                            <i data-lucide="lightbulb" class="w-3.5 h-3.5"></i>
                            Prompt Helper
                        </button>
                    </div>
                    <input x-model="topic" type="text" placeholder="e.g., 'Healthy Summer Recipes'" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary">
                </div>

                <!-- Parameters Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-3">
                        <label class="text-sm font-bold uppercase tracking-tight">Number of Posts</label>
                        <input x-model="count" type="number" min="1" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                    </div>
                    <div class="space-y-3">
                        <label class="text-sm font-bold uppercase tracking-tight">Tone (Optional)</label>
                        <select x-model="tone" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                            <option>Default Tone</option>
                            <option>Professional</option>
                            <option>Casual</option>
                            <option>Witty</option>
                            <option>Authoritative</option>
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="text-sm font-bold uppercase tracking-tight">Length (Optional)</label>
                        <select x-model="length" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                            <option>Default Length</option>
                            <option>Short (Small)</option>
                            <option>Medium (Standard)</option>
                            <option>Long (Detailed)</option>
                        </select>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="space-y-3">
                    <label class="text-sm font-bold uppercase tracking-tight">Additional Instructions for Text (Optional)</label>
                    <textarea x-model="context" placeholder="e.g., 'Focus on benefits for beginners'" rows="4" class="flex min-h-[100px] w-full rounded-lg border border-input bg-muted/30 px-4 py-3 text-sm"></textarea>
                </div>

                <!-- CTA -->
                <div class="space-y-3">
                    <label class="text-sm font-bold uppercase tracking-tight">Call to Action (Optional)</label>
                    <input x-model="cta" type="text" placeholder="e.g., 'Visit my website: https://example.com' or 'Use code SUMMER20'" class="flex h-12 w-full rounded-lg border border-input bg-muted/30 px-4 py-2 text-sm">
                </div>

                <!-- Checkboxes -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="flex items-center gap-3 p-4 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-colors">
                        <input type="checkbox" x-model="addLineBreaks" class="w-4 h-4 rounded border-input text-primary focus:ring-primary">
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm font-medium leading-none">Add line breaks</span>
                            <i data-lucide="help-circle" class="w-3.5 h-3.5 text-muted-foreground"></i>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-4 rounded-xl border border-border bg-muted/10 cursor-pointer hover:bg-muted/20 transition-colors">
                        <input type="checkbox" x-model="includeHashtags" class="w-4 h-4 rounded border-input text-primary focus:ring-primary">
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm font-medium leading-none">Include hashtags</span>
                            <i data-lucide="help-circle" class="w-3.5 h-3.5 text-muted-foreground"></i>
                        </div>
                    </label>
                </div>

                <div class="text-xs text-muted-foreground mt-4">
                    Est. Text Generation Cost: <span x-text="count"></span> token(s)
                </div>

                <div class="pt-6 border-t border-border">
                    <button @click="generateContent" :disabled="isGenerating" class="w-full inline-flex items-center justify-center rounded-xl text-md font-bold ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:scale-[1.01] active:scale-[0.99] h-14 px-8 py-4 shadow-lg shadow-primary/20">
                        <template x-if="!isGenerating">
                            <div class="flex items-center gap-2">
                                <i data-lucide="sparkles" class="w-5 h-5"></i>
                                <span>Generate Posts (<span x-text="count"></span> <i data-lucide="gem" class="w-4 h-4 inline-block align-middle ml-1"></i>)</span>
                            </div>
                        </template>
                        <template x-if="isGenerating">
                            <div class="flex items-center gap-2">
                                <i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i>
                                <span>Architecting <span x-text="count"></span> Posts...</span>
                            </div>
                        </template>
                    </button>
                </div>
            </div>
        </div>

        <!-- Recent Content -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Recent Content</h3>
            </div>
            <div class="p-6 pt-0">
                <div class="space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $recentContents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('content-creator.show', $item)); ?>" class="block p-3 border border-border rounded-lg hover:bg-muted/50">
                        <h3 class="font-semibold text-sm mb-2"><?php echo e($item->title); ?></h3>
                        <div class="flex items-center justify-between text-xs text-muted-foreground mb-2">
                            <span><?php echo e(ucwords(str_replace('-', ' ', $item->type))); ?></span>
                            <span><?php echo e($item->word_count); ?> words</span>
                        </div>
                        <?php
                            $statusClasses = [
                                'published' => 'bg-green-100 text-green-700',
                                'draft' => 'bg-amber-100 text-amber-700',
                                'generating' => 'bg-blue-100 text-blue-700',
                                'failed' => 'bg-red-100 text-red-700'
                            ];
                            $currentStatusClass = $statusClasses[$item->status] ?? 'bg-slate-100 text-slate-700';
                        ?>
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 <?php echo e($currentStatusClass); ?>">
                            <?php echo e(ucfirst($item->status)); ?>

                        </span>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="text-center py-8 text-muted-foreground">
                        <i data-lucide="pencil" class="w-8 h-8 mx-auto mb-2 opacity-20"></i>
                        <p>No content generated yet.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/content-creator/index.blade.php ENDPATH**/ ?>