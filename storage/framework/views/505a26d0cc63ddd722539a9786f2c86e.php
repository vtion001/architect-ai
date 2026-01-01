<?php $__env->startSection('content'); ?>
<div class="p-8 max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Knowledge-Base Driven Content Creator</h1>
        <p class="text-muted-foreground">Generate high-quality content powered by your knowledge base</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Content -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Total Content</p>
                        <p class="text-2xl font-bold">3,345</p>
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
                        <p class="text-2xl font-bold">423</p>
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
                        <p class="text-2xl font-bold">28</p>
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
                        <p class="text-2xl font-bold">3,317</p>
                    </div>
                    <i data-lucide="sparkles" class="w-8 h-8 text-purple-500"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Content Generator -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm lg:col-span-2">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-5 h-5"></i>
                    Generate New Content
                </h3>
                <p class="text-sm text-muted-foreground">Create content using AI with your brand voice and knowledge base</p>
            </div>
            <div class="p-6 pt-0 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="content-topic" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Content Topic</label>
                        <input type="text" id="content-topic" placeholder="What do you want to write about?" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1.5" />
                    </div>
                    <div>
                        <label for="content-type" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Content Type</label>
                        <select id="content-type" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1.5">
                            <option value="" disabled selected>Select type</option>
                            <option value="blog-post">Blog Post</option>
                            <option value="social-media">Social Media Post</option>
                            <option value="email">Email Campaign</option>
                            <option value="case-study">Case Study</option>
                            <option value="product-description">Product Description</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="additional-context" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Additional Context (Optional)</label>
                    <textarea id="additional-context" placeholder="Add any specific instructions or context..." rows="3" class="flex min-h-[80px] w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 mt-1.5"></textarea>
                </div>

                <button class="w-full inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <i data-lucide="sparkles" class="w-4 h-4 mr-2"></i>
                    Generate Content
                </button>
            </div>
        </div>

        <!-- Recent Content -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight">Recent Content</h3>
            </div>
            <div class="p-6 pt-0">
                <div class="space-y-4">
                    <?php
                    $recentContent = [
                        ['id' => 1, 'title' => "10 AI Trends Transforming Business", 'type' => "Blog Post", 'words' => 1247, 'status' => "Published"],
                        ['id' => 2, 'title' => "Product Launch Announcement", 'type' => "Social Media", 'words' => 284, 'status' => "Draft"],
                        ['id' => 3, 'title' => "Customer Success Story - TechCorp", 'type' => "Case Study", 'words' => 1856, 'status' => "Published"],
                    ];
                    ?>

                    <?php $__currentLoopData = $recentContent; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $content): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-3 border border-border rounded-lg hover:bg-muted/50">
                        <h3 class="font-semibold text-sm mb-2"><?php echo e($content['title']); ?></h3>
                        <div class="flex items-center justify-between text-xs text-muted-foreground mb-2">
                            <span><?php echo e($content['type']); ?></span>
                            <span><?php echo e($content['words']); ?> words</span>
                        </div>
                        <?php
                             $statusClass = $content['status'] === "Published" ? "bg-green-100 text-green-700 hover:bg-green-100" : "bg-amber-100 text-amber-700 hover:bg-amber-100";
                        ?>
                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 <?php echo e($statusClass); ?>">
                            <?php echo e($content['status']); ?>

                        </span>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/content-creator/index.blade.php ENDPATH**/ ?>