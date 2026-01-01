<?php $__env->startSection('title', 'Financial Overview'); ?>
<?php $__env->startSection('container_class', 'premium'); ?>

<?php $__env->startSection('styles'); ?>
    @import url('https://fonts.googleapis.com/css2?family=Lora:ital,wght@0,400;0,700;1,400&display=swap');
    .report-wrapper { font-family: 'Lora', serif; color: #1e293b; }
    .report-wrapper::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 8px; background: #b45309; z-index: 10; }
    .report-header { padding: 60px 40px 40px; border-bottom: 1px solid #e2e8f0; text-align: center; }
    .report-header h1 { margin: 0; font-size: 2.2rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #0f172a; }
    .report-meta { margin-top: 20px; font-style: italic; color: #64748b; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; display: inline-block; padding: 10px 30px; }
    .report-content { padding: 40px; flex: 1; }
    h2 { color: #0f172a; font-size: 1.4rem; text-transform: uppercase; letter-spacing: 0.05em; text-align: center; margin-top: 60px; padding-bottom: 15px; border-bottom: 1px double #cbd5e1; }
    h3 { color: #b45309; margin-top: 30px; font-style: italic; }
    p { margin: 20px 0; text-align: justify; }
    table { width: 100%; border-collapse: collapse; margin: 30px 0; font-family: sans-serif; font-size: 0.9rem; }
    th { background: #0f172a; color: white; padding: 12px; text-align: left; }
    td { padding: 12px; border-bottom: 1px solid #e2e8f0; }
    .footer { padding: 40px; text-align: center; color: #94a3b8; font-size: 0.8rem; letter-spacing: 1px; margin-top: auto; }
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="report-header">
        <h1>Financial Overview</h1>
        <div class="report-meta">Fiscal Period | <?php echo e(date('Y')); ?></div>
        <?php if($recipientName): ?>
            <div style="margin-top: 15px; font-size: 0.9rem;">Prepared for: <?php echo e($recipientName); ?></div>
        <?php endif; ?>
    </div>

    <div class="report-content">
        <?php echo $content; ?>

    </div>

    <div class="footer">
        Architect AI | Financial Advisory
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('reports.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/reports/financial-overview.blade.php ENDPATH**/ ?>