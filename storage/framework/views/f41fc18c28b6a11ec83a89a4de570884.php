<?php $__env->startSection('content'); ?>
<div class="p-8 max-w-5xl mx-auto">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <a href="<?php echo e(route('content-creator.index')); ?>" class="text-sm text-muted-foreground hover:text-foreground flex items-center gap-1 mb-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back to Creator
            </a>
            <h1 class="text-3xl font-bold"><?php echo e($content->title); ?></h1>
            <p class="text-muted-foreground mt-1"><?php echo e(ucwords(str_replace('-', ' ', $content->type))); ?> • <?php echo e($content->created_at->format('M d, Y')); ?></p>
        </div>
        <div class="flex gap-2">
            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 uppercase tracking-wider font-bold text-xs">
                <i data-lucide="copy" class="w-4 h-4 mr-2"></i>
                Copy All
            </button>
            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 uppercase tracking-wider font-bold text-xs shadow-lg shadow-primary/20">
                <i data-lucide="send" class="w-4 h-4 mr-2"></i>
                Publish All
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="p-4 rounded-xl border border-border bg-card">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Length</p>
            <p class="text-2xl font-bold text-blue-500"><?php echo e($content->word_count); ?> Words</p>
        </div>
        <div class="p-4 rounded-xl border border-border bg-card">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Topic</p>
            <p class="text-lg font-bold truncate"><?php echo e($content->topic); ?></p>
        </div>
        <div class="p-4 rounded-xl border border-border bg-card">
            <p class="text-xs text-muted-foreground uppercase tracking-wider font-semibold">Status</p>
            <p class="text-2xl font-bold text-purple-500 uppercase"><?php echo e($content->status); ?></p>
        </div>
    </div>

    <!-- Social Media Feed Grid -->
    <?php
        // 1. Split content by markdown horizontal rule '---'
        $rawSegments = preg_split('/\n-{3,}\n/', $content->result ?? '');
        $posts = [];
        $globalHashtags = '';

        // 2. logic to detect if the last segment is just hashtags (common pattern)
        if (!empty($rawSegments)) {
            $lastSegment = trim(end($rawSegments));
            // Check if it starts with # and is relatively short (likely hashtags)
            if (count($rawSegments) > 1 && str_starts_with($lastSegment, '#') && strlen($lastSegment) < 300) {
                // Remove the last segment and store it as global tags
                array_pop($rawSegments);
                $globalHashtags = $lastSegment;
            }
            $posts = array_filter($rawSegments);
        } else {
            $posts = [$content->result ?? 'No content generated.'];
        }
    ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-20">
        <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            // Append global hashtags if they exist
            $finalPostContent = trim($post);
            if ($globalHashtags) {
                 $finalPostContent .= "\n\n" . $globalHashtags;
            }
        ?>
        <div class="w-full bg-card border border-border rounded-xl shadow-md overflow-hidden animate-in fade-in slide-in-from-bottom-4 duration-500" style="animation-delay: <?php echo e($index * 150); ?>ms;">
            <!-- Post Header -->
            <div class="p-4 flex items-center justify-between border-b border-border/50">
                <div class="flex items-center gap-3">
                    <!-- Avatar -->
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center border border-primary/10 shadow-inner">
                        <i data-lucide="bot" class="w-5 h-5 text-primary"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-foreground leading-tight">Architect AI</h4>
                        <div class="flex items-center gap-1.5 text-[10px] text-muted-foreground font-medium">
                            <span class="text-primary font-bold">Recommended for you</span>
                            <span class="text-muted-foreground/50">•</span>
                            <span><?php echo e($content->created_at->diffForHumans()); ?></span>
                            <span class="text-muted-foreground/50">•</span>
                            <i data-lucide="globe" class="w-3 h-3 opacity-70"></i>
                        </div>
                    </div>
                </div>
                <!-- Action Buttons Removed -->
            </div>

            <!-- Post Content Body -->
            <div class="p-4 text-foreground">
                <div class="prose prose-slate max-w-none dark:prose-invert prose-p:my-2 prose-headings:my-3 prose-ul:my-2 text-[15px] leading-relaxed">
                    <?php echo Str::markdown($finalPostContent); ?>

                </div>
            </div>

            <!-- Media Placeholder -->
            <div class="px-4 pb-4">
                 <div class="w-full h-56 rounded-lg bg-muted/20 border-2 border-dashed border-border/50 flex flex-col items-center justify-center gap-2 text-muted-foreground/50 transition-colors hover:bg-muted/30 hover:border-primary/20 hover:text-primary/70 cursor-pointer group">
                     <div class="w-12 h-12 rounded-full bg-background/50 flex items-center justify-center group-hover:scale-110 transition-transform shadow-sm">
                        <i data-lucide="image-plus" class="w-6 h-6"></i>
                     </div>
                     <span class="text-xs font-bold uppercase tracking-widest">Add Visuals</span>
                 </div>
            </div>

             <!-- Draft Actions Footer -->
            <div class="px-4 py-3 border-t border-border bg-muted/5 flex flex-wrap items-center justify-between gap-3">
                 <!-- Left Group: Visuals -->
                 <div class="flex gap-2 w-full md:w-auto">
                     <button class="flex-1 md:flex-none flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-card border border-border text-muted-foreground hover:text-primary hover:border-primary/30 transition-all text-xs font-bold uppercase tracking-wider whitespace-nowrap" title="Generate AI Image">
                        <i data-lucide="sparkles" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Gen Photo</span>
                    </button>
                    <button class="flex-1 md:flex-none flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-card border border-border text-muted-foreground hover:text-blue-500 hover:border-blue-200 transition-all text-xs font-bold uppercase tracking-wider whitespace-nowrap" title="Upload Image">
                        <i data-lucide="paperclip" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Attach</span>
                    </button>
                 </div>

                 <!-- Right Group: Actions -->
                 <div class="flex gap-2 w-full md:w-auto">
                     <button class="flex-1 md:flex-none flex items-center justify-center gap-2 py-2 px-3 rounded-lg bg-white border border-border text-muted-foreground hover:text-red-500 hover:border-red-200 hover:bg-red-50 transition-all text-xs font-bold uppercase tracking-wider" title="Regenerate Text">
                        <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                        <span class="hidden sm:inline">Redo</span>
                    </button>
                    <button class="flex-1 md:flex-none flex items-center justify-center gap-2 py-2 px-4 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-all text-xs font-bold uppercase tracking-wider shadow-sm flex-grow md:flex-grow-0">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        Publish
                    </button>
                 </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/content-creator/show.blade.php ENDPATH**/ ?>