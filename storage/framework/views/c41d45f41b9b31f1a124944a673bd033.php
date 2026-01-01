<?php $__env->startSection('content'); ?>
<div class="p-8 max-w-7xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Documents</h1>
        <p class="text-muted-foreground">Manage and organize your business documents</p>
    </div>

    <!-- Search and Actions -->
    <div class="flex gap-4 mb-6">
        <div class="relative flex-1">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground"></i>
            <input type="search" placeholder="Search documents..." class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-9" />
        </div>
        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
            <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
            Filter
        </button>
        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
            <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
            Upload
        </button>
    </div>

    <!-- All Documents -->
    <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm">
        <div class="flex flex-col space-y-1.5 p-6">
            <h3 class="text-2xl font-semibold leading-none tracking-tight">All Documents</h3>
        </div>
        <div class="p-6 pt-0">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-border">
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">Name</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">Type</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">Size</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">Category</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">Modified</th>
                            <th class="text-left py-3 px-4 text-xs font-medium text-muted-foreground">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $documents = [
                            ['id' => 1, 'name' => "Q4 2024 Business Report.pdf", 'type' => "PDF", 'size' => "2.4 MB", 'modified' => "2025-01-14", 'category' => "Reports"],
                            ['id' => 2, 'name' => "Marketing Strategy 2025.docx", 'type' => "DOCX", 'size' => "1.1 MB", 'modified' => "2025-01-13", 'category' => "Strategy"],
                            ['id' => 3, 'name' => "Product Roadmap.xlsx", 'type' => "XLSX", 'size' => "856 KB", 'modified' => "2025-01-12", 'category' => "Planning"],
                            ['id' => 4, 'name' => "Brand Guidelines 2025.pdf", 'type' => "PDF", 'size' => "4.2 MB", 'modified' => "2025-01-11", 'category' => "Brand"],
                            ['id' => 5, 'name' => "Customer Feedback Analysis.csv", 'type' => "CSV", 'size' => "324 KB", 'modified' => "2025-01-10", 'category' => "Analytics"],
                        ];
                        ?>
                        <?php $__currentLoopData = $documents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-b border-border hover:bg-muted/50">
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-blue-100 rounded flex items-center justify-center">
                                        <i data-lucide="file-text" class="w-4 h-4 text-blue-600"></i>
                                    </div>
                                    <span class="text-sm font-medium"><?php echo e($doc['name']); ?></span>
                                </div>
                            </td>
                            <td class="py-4 px-4">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80">
                                    <?php echo e($doc['type']); ?>

                                </span>
                            </td>
                            <td class="py-4 px-4 text-sm text-muted-foreground"><?php echo e($doc['size']); ?></td>
                            <td class="py-4 px-4 text-sm"><?php echo e($doc['category']); ?></td>
                            <td class="py-4 px-4 text-sm text-muted-foreground"><?php echo e($doc['modified']); ?></td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 w-9">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                    <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 w-9">
                                        <i data-lucide="download" class="w-4 h-4"></i>
                                    </button>
                                    <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 w-9">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/documents/index.blade.php ENDPATH**/ ?>