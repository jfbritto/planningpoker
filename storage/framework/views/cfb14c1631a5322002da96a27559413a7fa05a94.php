<?php $__env->startSection('title', 'Planning Poker - Início'); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 20px;">
        <div>
            <h1 style="margin-bottom: 4px;">🎴 Planning Poker</h1>
            <p style="color: #666; font-size: 13px; margin: 0;">
                Sistema simples e objetivo para estimativas ágeis
            </p>
        </div>
        <a href="<?php echo e(route('rooms.create')); ?>" class="btn">
            Criar Nova Sala
        </a>
    </div>
</div>

<?php if($rooms && $rooms->count() > 0): ?>
    <div class="card">
        <h2 style="margin-bottom: 16px;">Salas Ativas (<?php echo e($rooms->count()); ?>)</h2>
        <div class="rooms-list">
            <?php $__currentLoopData = $rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="room-item">
                    <div style="flex: 1;">
                        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                            <h3 style="margin: 0; font-size: 16px;"><?php echo e($room->name); ?></h3>
                            <span style="font-size: 11px; color: #999; background: #f3f4f6; padding: 2px 6px; border-radius: 10px; font-family: monospace;">
                                <?php echo e($room->code); ?>

                            </span>
                        </div>
                        <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: #666;">
                            <span>👥 <?php echo e($room->participants_count); ?> participante(s)</span>
                            <span>•</span>
                            <span>🕐 <?php echo e($room->updated_at->diffForHumans()); ?></span>
                        </div>
                    </div>
                    <a href="<?php echo e(route('rooms.show', $room->code)); ?>" class="btn" style="padding: 6px 16px; font-size: 13px;">
                        Entrar
                    </a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php else: ?>
    <div class="card" style="text-align: center; padding: 40px;">
        <p style="color: #666; font-size: 14px; margin-bottom: 16px;">Nenhuma sala ativa no momento</p>
        <a href="<?php echo e(route('rooms.create')); ?>" class="btn">
            Criar Primeira Sala
        </a>
    </div>
<?php endif; ?>

<div class="card">
    <h2 style="font-size: 16px; margin-bottom: 12px;">Como usar:</h2>
    <ol style="line-height: 1.8; color: #666; font-size: 13px; padding-left: 20px;">
        <li>Crie uma nova sala ou entre em uma sala existente</li>
        <li>Os participantes votam usando os cartões de Planning Poker</li>
        <li>Revele os votos para ver os resultados</li>
        <li>Discuta as diferenças e chegue a um consenso</li>
    </ol>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/resources/views/welcome.blade.php ENDPATH**/ ?>