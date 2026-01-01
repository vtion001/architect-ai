<?php $__env->startSection('title', 'Custom Report'); ?>
<?php $__env->startSection('container_class', 'custom'); ?>

<?php $__env->startSection('styles'); ?>
    .report-wrapper { font-family: system-ui, -apple-system, sans-serif; color: #333; }
    .report-content { padding: 40px; flex: 1; }
    h1 { color: #2563eb; }
    h2 { color: #1e40af; border-bottom: 1px solid #eee; margin-top: 30px; }
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="report-header" style="padding: 40px; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
        <h1 style="margin: 0; font-size: 2rem;">Custom Analysis Report</h1>
        <div class="report-meta" style="margin-top: 10px; color: #64748b;">Generated on <?php echo e(date('F j, Y')); ?></div>
        <?php if($recipientName): ?>
            <div style="margin-top: 10px; font-weight: 500;">Prepared for: <?php echo e($recipientName); ?></div>
        <?php endif; ?>
    </div>

    <div class="report-content">
        <?php echo $content; ?>

    </div>

    <div class="footer" style="padding: 30px; text-align: center; color: #94a3b8; font-size: 0.85rem;">
        Architect AI
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('reports.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/reports/custom.blade.php ENDPATH**/ ?>