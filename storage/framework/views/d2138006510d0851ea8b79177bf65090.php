<?php $__env->startSection('content'); ?>
<div class="p-8 max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Global Knowledge Base</h1>
        <p class="text-muted-foreground">Centralized repository of your business knowledge and resources</p>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total Documents -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Total Documents</p>
                        <p class="text-2xl font-bold">1,247</p>
                    </div>
                    <i data-lucide="file-text" class="w-8 h-8 text-blue-500"></i>
                </div>
            </div>
        </div>
        <!-- Categories -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Categories</p>
                        <p class="text-2xl font-bold">24</p>
                    </div>
                    <i data-lucide="folder" class="w-8 h-8 text-purple-500"></i>
                </div>
            </div>
        </div>
        <!-- Tags -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Tags</p>
                        <p class="text-2xl font-bold">156</p>
                    </div>
                    <i data-lucide="tag" class="w-8 h-8 text-green-500"></i>
                </div>
            </div>
        </div>
        <!-- Recent Updates -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-muted-foreground">Recent Updates</p>
                        <p class="text-2xl font-bold">38</p>
                    </div>
                    <i data-lucide="clock" class="w-8 h-8 text-amber-500"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Actions -->
    <div class="flex gap-4 mb-8">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
            <input type="search" placeholder="Search knowledge base..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-9" />
        </div>
        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Add Document
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Categories -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm lg:col-span-2">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center gap-2">
                    <i data-lucide="folder" class="w-5 h-5"></i>
                    Categories
                </h3>
            </div>
            <div class="p-6 pt-0">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php
                    $categories = [
                        ['name' => "Brand Guidelines", 'docs' => 45, 'updated' => "2 days ago"],
                        ['name' => "Product Information", 'docs' => 127, 'updated' => "1 week ago"],
                        ['name' => "Company Policies", 'docs' => 67, 'updated' => "3 days ago"],
                        ['name' => "Case Studies", 'docs' => 34, 'updated' => "1 day ago"],
                        ['name' => "Marketing Resources", 'docs' => 89, 'updated' => "5 days ago"],
                        ['name' => "Technical Documentation", 'docs' => 156, 'updated' => "1 week ago"],
                    ];
                    ?>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-4 border border-border rounded-lg hover:bg-muted/50 cursor-pointer transition-colors">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="folder" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-sm"><?php echo e($category['name']); ?></h3>
                                <p class="text-xs text-muted-foreground"><?php echo e($category['docs']); ?> documents</p>
                            </div>
                        </div>
                        <p class="text-xs text-muted-foreground flex items-center gap-1">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            Updated <?php echo e($category['updated']); ?>

                        </p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>

        <!-- Recent Updates -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
            <div class="flex flex-col space-y-1.5 p-6">
                <h3 class="text-2xl font-semibold leading-none tracking-tight flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-5 h-5"></i>
                    Recent Updates
                </h3>
            </div>
            <div class="p-6 pt-0">
                <div class="space-y-4">
                    <?php
                    $recentDocuments = [
                        ['id' => 1, 'title' => "Q4 2024 Brand Guidelines Update", 'category' => "Brand Guidelines", 'tags' => ["Design", "Brand", "Visual Identity"], 'updated' => "2025-01-14"],
                        ['id' => 2, 'title' => "Product Specifications - AI Suite v3.0", 'category' => "Product Information", 'tags' => ["Product", "Technical", "AI"], 'updated' => "2025-01-13"],
                        ['id' => 3, 'title' => "Customer Success Story - Enterprise Client", 'category' => "Case Studies", 'tags' => ["Success", "Enterprise", "Customer"], 'updated' => "2025-01-12"],
                    ];
                    ?>
                    <?php $__currentLoopData = $recentDocuments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-3 border border-border rounded-lg hover:bg-muted/50 cursor-pointer">
                        <h3 class="font-semibold text-sm mb-2"><?php echo e($doc['title']); ?></h3>
                        <p class="text-xs text-muted-foreground mb-2"><?php echo e($doc['category']); ?></p>
                        <div class="flex flex-wrap gap-1 mb-2">
                            <?php $__currentLoopData = $doc['tags']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80">
                                <?php echo e($tag); ?>

                            </span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <p class="text-xs text-muted-foreground"><?php echo e($doc['updated']); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/knowledge-base/index.blade.php ENDPATH**/ ?>