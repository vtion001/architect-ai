<?php $__env->startSection('title', 'Executive Summary'); ?>
<?php $__env->startSection('container_class', 'standard'); ?>

<?php $__env->startSection('styles'); ?>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');
    
    /* Default / Corporate Style (exec-corporate) */
    .report-wrapper { font-family: 'Inter', sans-serif; color: #1e293b; }
    .report-header { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 40px; }
    .report-header h1 { margin: 0; font-size: 2.5rem; font-weight: 700; }
    .report-meta { margin-top: 10px; opacity: 0.9; font-size: 0.9rem; font-weight: 500; }
    .report-content { padding: 40px; flex: 1; }
    h2 { color: #1e3a8a; border-bottom: 2px solid #e2e8f0; padding-bottom: 12px; margin-top: 40px; font-weight: 700; font-size: 1.5rem; }
    h3 { color: #334155; margin-top: 25px; font-weight: 600; }
    p { margin: 16px 0; color: #475569; }
    ul, ol { padding-left: 20px; color: #475569; }
    li { margin-bottom: 8px; }
    .footer { padding: 30px; border-top: 1px solid #f1f5f9; text-align: center; color: #94a3b8; font-size: 0.85rem; }

    /* Minimal Style (exec-minimal) */
    <?php if($variant === 'exec-minimal'): ?>
        .report-header { background: white; color: #0f172a; border-bottom: 4px solid #1e3a8a; padding: 40px 0; margin: 0 40px; }
        .report-header h1 { font-size: 2rem; letter-spacing: -0.025em; }
        h2 { border-bottom: none; position: relative; padding-left: 20px; }
        h2::before { content: ''; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: #1e3a8a; }
        .report-content { padding-top: 20px; }
    <?php endif; ?>

    /* Detailed Style (exec-detailed) */
    <?php if($variant === 'exec-detailed'): ?>
        .report-header { background: #0f172a; padding: 60px 40px; border-left: 20px solid #1e3a8a; }
        .report-header h1 { text-transform: uppercase; letter-spacing: 0.1em; font-size: 2.2rem; }
        .report-content { padding: 50px 60px; }
        .report-content > h2 { background: #f8fafc; padding: 15px; border: 1px solid #e2e8f0; border-left: 5px solid #1e3a8a; margin-left: -20px; margin-right: -20px; }
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="report-header">
        <h1>Executive Summary</h1>
        <div class="report-meta">Generated Report | <?php echo e(date('F j, Y')); ?></div>
        <?php if($recipientName): ?>
            <div style="margin-top: 10px; opacity: 0.8;">Prepared for: <?php echo e($recipientName); ?></div>
        <?php endif; ?>
    </div>

    <div class="report-content">
        <?php echo $content; ?>

    </div>

    <div class="footer">
        Architect AI | Strategy & Intelligence Portal
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('reports.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/resources/views/reports/executive-summary.blade.php ENDPATH**/ ?>